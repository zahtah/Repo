<?php

namespace App\Imports;

use App\Models\Allocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Morilog\Jalali\Jalalian;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AllocationsImport implements ToModel, WithHeadingRow, WithCalculatedFormulas, WithChunkReading
{
    use Importable;

    /**
     * accumulator keeps track of V_m sums we've already inserted
     * for a given (code|Takhsis_group) while processing THIS import.
     * Format: [ "code|group" => float(totalVmSoFarFromThisImport) ]
     */
    protected $accumulator = [];

    /**
     * dbSums caches the SUM(V_m) currently present in DB for a given key
     * Format: [ "code|group" => float(sumInDbAtFirstRead) ]
     * We load each key at most once (one query per distinct key)
     */
    protected $dbSums = [];

    protected $fileName;

    public function __construct(string $fileName = null)
    {
        $this->fileName = $fileName ? preg_replace('/\.xlsx$/i', '', $fileName) : null;
    }

    /**
     * Convert a value that represents a decimal in Excel to a float or null.
     * This is conservative: if input is empty/null -> null.
     */
    protected function parseDecimal($value)
    {
        if ($value === null) return null;
        if ($value === '') return null;
        if (is_numeric($value)) return (float) $value;

        // convert persian digits and comma/dot variants
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','،'];
        $english = ['0','1','2','3','4','5','6','7','8','9','.','.'];
        $v = str_replace($persian, $english, (string)$value);

        // remove any non numeric except dot and minus
        $v = preg_replace('/[^\d\.\-]/u', '', $v);
        if ($v === '' || $v === null) return null;
        if (is_numeric($v)) return (float) $v;
        return null;
    }

    /**
     * Normalize the 'sum' value from Excel for DB storage WITHOUT recalculating it.
     * The goal: keep the numeric value as the user provided in Excel, but
     * normalize digits and separators so it can be stored reliably in the DB
     * as a numeric-like string (e.g. "1234.56"). If the cell was textual
     * but not parseable, we keep a trimmed string fallback.
     */
    protected function normalizeSumForDb($raw)
    {
        if ($raw === null) return null;
        if ($raw === '') return null;

        // If it's numeric (Excel returned number), format with dot decimal separator preserving decimals
        if (is_numeric($raw)) {
            // keep as-is but cast to string with no thousands separators
            return (string) (float) $raw;
        }

        // convert persian digits and decimal separators
        $pers = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','٬','،'];
        $eng = ['0','1','2','3','4','5','6','7','8','9','.','','.'];
        $s = str_replace($pers, $eng, (string)$raw);

        // remove thousands separators (commas, thin space, nbsp, non-digit except dot/minus)
        $s = preg_replace('/[\s\u{00A0}\u{2009},]+/u', '', $s);

        // now keep only digits, dot and minus
        $s = preg_replace('/[^0-9\.\-]/', '', $s);

        // If result is numeric, return normalized string
        if ($s === '' ) return null;
        if (is_numeric($s)) return (string) (float) $s;

        // fallback: trimmed original raw string (best-effort preservation)
        return trim((string)$raw);
    }

    /**
     * Return DB sum for a key (cache on first access). This avoids running
     * a DB sum query for every row; instead we run one query per distinct key.
     */
    protected function getDbSumForKey($code, $takhsis)
    {
        $key = ($code === null ? '::NULL::' : (string)$code) . '|' . ($takhsis === null ? '::NULL::' : (string)$takhsis);
        if (array_key_exists($key, $this->dbSums)) return $this->dbSums[$key];

        $query = Allocation::query();
        if ($code !== null && $code !== '') {
            $query->where('code', $code);
        } else {
            $query->whereNull('code');
        }
        if ($takhsis !== null && $takhsis !== '') {
            $query->where('Takhsis_group', $takhsis);
        } else {
            $query->whereNull('Takhsis_group');
        }

        $sum = (float) $query->sum('V_m');
        $this->dbSums[$key] = $sum;
        return $sum;
    }

    public function model(array $row)
    {
        // normalize and fetch values (flexible key matching)
        $code = $this->getValue($row, 'code');
        $takhsis = $this->getValue($row, 'Takhsis_group') ?? $this->getValue($row, 'takhsis_group');

        $vmRaw = $this->getValue($row, 'V_m') ?? $this->getValue($row, 'v_m') ?? $this->getValue($row, 'V_m3') ?? 0;
        $v_m = $this->toFloat($vmRaw);

        // key for accumulator
        $key = ($code === null ? '::NULL::' : (string)$code) . '|' . ($takhsis === null ? '::NULL::' : (string)$takhsis);

        // 1) sum of V_m already in DB (cached per distinct key)
        $otherSumInDb = $this->getDbSumForKey($code, $takhsis);

        // 2) plus sum of V_m from rows already processed in THIS import (accumulator)
        $otherSumFromImport = $this->accumulator[$key] ?? 0.0;

        // final sum if you ever need it for internal checks (do NOT overwrite Excel 'sum')
        $finalSum = $otherSumInDb + $otherSumFromImport + $v_m;

        // update accumulator: include current row's V_m for following rows
        $this->accumulator[$key] = $otherSumFromImport + $v_m;

        // t_mosavvab
        $t_m = $this->toFloat($this->getValue($row, 't_mosavvab') ?? $this->getValue($row, 't_m'));

        // BAGHI: we will prefer file value (if present). We do NOT auto-compute baghi
        // because you requested 'sum' NOT be recalculated. If baghi must be computed,
        // we can add that logic later.
        $baghi = $this->parseDecimal($this->getValue($row, 'baghi') ?? null);

        // NOTE: sum WILL be taken from Excel as-is (normalized for storage) and not computed
        $excelSumRaw = $this->getValue($row, 'sum') ?? $this->getValue($row, 'Sum') ?? $this->getValue($row, 'SUM');
        $sumForDb = $this->normalizeSumForDb($excelSumRaw);

        // parse date fields using convertJalali
        $erja = $this->convertJalali($this->getValue($row, 'erja'));
        $comete = $this->convertJalali($this->getValue($row, 'comete'));
        $dateShimareRaw = $this->getValue($row, 'date-shimare') ?? $this->getValue($row, 'date_shimare') ?? $this->getValue($row, 'date shimare');
        $date_shimare = $this->convertJalali($dateShimareRaw);

        return new Allocation([
            'row' => $this->getValue($row, 'row'),
            'file_name' => $this->fileName,
            'Shahrestan' => $this->getValue($row, 'Shahrestan'),
            'sal' => $this->toInt($this->getValue($row, 'sal')),
            'erja' => $erja,
            'code' => $code,
            'mantaghe' => $this->getValue($row, 'mantaghe'),
            'Abadi' => $this->getValue($row, 'Abadi'),
            'kelace' => $this->getValue($row, 'kelace'),
            'motaghasi' => $this->getValue($row, 'motaghasi'),
            'darkhast' => $this->getValue($row, 'darkhast'),
            'Takhsis_group' => $takhsis,
            'masraf' => $this->getValue($row, 'masraf'),
            'comete' => $comete,
            'shomare' => $this->getValue($row, 'shomare'),
            'date_shimare' => $date_shimare,
            'vahed' => $this->getValue($row, 'vahed'),
            'q_m' => $this->toFloat($this->getValue($row, 'q_m')),
            'V_m' => $v_m,
            't_mosavvab' => $t_m,
            // store Excel's sum (normalized) WITHOUT recalculating it
            'sum' => $sumForDb,
            'baghi' => $baghi,
            'mosavabat' => $this->getValue($row, 'mosavabat'),
        ]);
    }

    private function convertJalali($value)
    {
        if (!$value && $value !== 0) return null;

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float)$value))->format('Y-m-d');
            } catch (\Throwable $e) {
                // continue to textual parsing
            }
        }

        $trans = [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            "\x{200F}"=>'', "\x{200E}"=>'', "\x{FEFF}"=>''
        ];
        $v = trim(strtr((string)$value, $trans));
        $v = rtrim($v, ".\t ");
        $v = preg_replace('/[.\-,\x{2212}\s]+/u', '/', $v);

        $parts = preg_split('/\//', $v);
        if (count($parts) >= 3) {
            if (mb_strlen($parts[0]) === 2) {
                $parts[0] = '13' . $parts[0];
            }
            $year = $parts[0];
            $month = str_pad(preg_replace('/\D/','', $parts[1]), 2, '0', STR_PAD_LEFT);
            $day = str_pad(preg_replace('/\D/','', $parts[2]), 2, '0', STR_PAD_LEFT);

            $jalali = "{$year}/{$month}/{$day}";
            try {
                return Jalalian::fromFormat('Y/m/d', $jalali)->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                // fall through
            }
        }

        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning('Date parse failed in AllocationsImport', ['value' => $value]);
            return null;
        }
    }

    protected function getValue(array $row, $key)
    {
        if (array_key_exists($key, $row)) return $row[$key];

        $lower = mb_strtolower($key);
        if (array_key_exists($lower, $row)) return $row[$lower];

        $normKey = preg_replace('/[^a-z0-9]/i', '', mb_strtolower($key));
        foreach ($row as $k => $v) {
            $nk = preg_replace('/[^a-z0-9]/i', '', mb_strtolower($k));
            if ($nk === $normKey) return $v;
        }

        return null;
    }

    protected function toFloat($v)
    {
        if ($v === null || $v === '') return 0.0;
        if (is_numeric($v)) return (float)$v;
        $s = (string) $v;
        $pers = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        foreach ($pers as $i => $p) $s = str_replace($p, (string)$i, $s);
        $s = str_replace(['٬', ','], ['', ''], $s);
        $s = str_replace('٫', '.', $s);
        $s = trim($s);
        return is_numeric($s) ? (float)$s : 0.0;
    }

    protected function toInt($v)
    {
        if ($v === null || $v === '') return null;
        return (int) $v;
    }

    /**
     * Use chunk reading to keep memory usage low on large files.
     */
    public function chunkSize(): int
    {
        return 1000; // adjust as needed
    }
}

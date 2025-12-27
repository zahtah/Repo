<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AllocationsImport;
use App\Models\Allocation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Exports\AllocationsExport;
use App\Models\FileCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AllocationController extends Controller
{
    // public function allocations(){
    //     return view('admin.allocations.allocations');
    // }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        Excel::import(new AllocationsImport($originalName), $request->file('file'));

        return back()->with('success', 'اطلاعات با موفقیت وارد شد.'.$originalName);
    }

public function index(Request $request)
    {
        $query = Allocation::query();

    // فیلتر بر اساس file_name از پارامتر query
        $fileFilter = $request->query('file_name');
        if ($fileFilter) {
            $query->where('file_name', $fileFilter);
        }

        // فیلتر متناظر با فیلدهای فرم
        if ($request->filled('Shahrestan')) {
            $query->where('Shahrestan', $request->get('Shahrestan'));
        }

        if ($request->filled('masraf')) {
            $query->where('Takhsis_group', $request->get('masraf'));
        }

        // بازه تاریخ برای ستون erja (فرمت ورودی باید YYYY-MM-DD از فرم date باشد)
        // دریافت ورودی خام از درخواست (ممکن است شمسی یا میلادی باشد)
        $rawFrom = $request->get('from');
        $rawTo   = $request->get('to');

        $from = $this->parseToGregorianDate($rawFrom);
        $to   = $this->parseToGregorianDate($rawTo);

        // اگر هر دو تاریخ معتبر باشند از whereBetween استفاده کن (توجه: ترتیب از -> تا)
        if ($from && $to) {
            // اطمینان از ترتیب درست (اگر کاربر اشتباها معکوس زده باشد)
            if ($from <= $to) {
                $query->whereBetween('erja', [$from, $to]);
            } else {
                $query->whereBetween('erja', [$to, $from]);
            }
        } elseif ($from) {
            $query->where('erja', '>=', $from);
        } elseif ($to) {
            $query->where('erja', '<=', $to);
        }


        // جستجوی عمومی (ردیف، کلاسه، متقاضی و... )
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('row', 'like', "%{$q}%")
                    ->orWhere('kelace', 'like', "%{$q}%")
                    ->orWhere('motaghasi', 'like', "%{$q}%")
                    ->orWhere('Shahrestan', 'like', "%{$q}%");
            });
        }

        // مرتب‌سازی دلخواه (مثال: ?sort=sal&direction=asc)
        $allowedSorts = ['id','row','Shahrestan','sal','erja','kelace','q_m','V_m','sum','baghi'];
        $sort = $request->get('sort', 'id');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }
        $query->orderBy($sort, $direction);

        // صفحه‌بندی: پارامتر per_page قابل تنظیم است
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0 || $perPage > 500) $perPage = 10;

        $cumulativeSql = DB::raw("(
        SELECT ROUND(COALESCE(SUM(t2.V_m),0), 2)
        FROM allocations t2
        WHERE COALESCE(t2.code, '') = COALESCE(allocations.code, '')
          AND COALESCE(t2.Takhsis_group, '') = COALESCE(allocations.Takhsis_group, '')
          AND COALESCE(t2.file_name, '__NO_FILE__') = COALESCE(allocations.file_name, '__NO_FILE__')
          AND t2.id <= allocations.id
        ) as cumulative_vm");

        // این select اصلی: همه ستون‌ها + ستون محاسبه‌شده
        $rowsQuery = $query->select('allocations.*', $cumulativeSql)
        ->orderBy($sort, $direction);


        $allocations = $query->paginate($perPage)->appends($request->query());

        $takhsis = $request->get('Takhsis_group');
        $code = $request->get('code');
        $fileFilter = $request->get('file_name');

        // Base query for rows (with optional filters)
        $base = Allocation::query();

        if ($takhsis && $takhsis !== 'all') {
            $base->where('Takhsis_group', $takhsis);
        }

        if ($code && $code !== 'all') {
            $base->where('code', $code);
        }

        if ($fileFilter && $fileFilter !== 'all') {
            $base->where('file_name', $fileFilter);
        }
        // زیرکوئری: محاسبه مجموع `sum` برای هر file_name (با مدیریت NULL)
    $fileSums = DB::table('allocations')
        ->select(DB::raw("IFNULL(file_name, '__NO_FILE__') as fn"), DB::raw('ROUND(SUM(`sum`), 2) as file_sum'))
        ->groupBy(DB::raw("IFNULL(file_name, '__NO_FILE__')"));

    // الحاق زیرکوئری به ردیف‌ها تا هر ردیف یک ستون file_sum داشته باشد
    $rowsQuery = $base->select('allocations.*')
        ->leftJoinSub($fileSums, 'fs', DB::raw("IFNULL(allocations.file_name, '__NO_FILE__')"), '=', 'fs.fn')
        ->addSelect('fs.file_sum as file_sum');

    // pagination و اجرای نهایی
    $rows = $rowsQuery->orderBy('created_at','desc')->paginate(25)->withQueryString();

        // داده‌های کمکی برای فیلترها (مثلا dropdown)
        $shahrestans = Allocation::select('Shahrestan')
            ->whereNotNull('Shahrestan')
            ->distinct()
            ->orderBy('Shahrestan')
            ->pluck('Shahrestan');

        $masrafs = Allocation::select('masraf')
            ->whereNotNull('masraf')
            ->distinct()
            ->orderBy('masraf')
            ->pluck('masraf');

        $fileNames = Allocation::select('file_name')
            ->whereNotNull('file_name')
            ->distinct()
            ->whereNotNull('file_name')
            ->orderBy('file_name')
            ->pluck('file_name')
            ->toArray();

        return view('admin.allocations.index', compact('rows','allocations', 'shahrestans','takhsis','code', 'masrafs','fileNames','fileFilter'));
    }

    public function create()
    {
    // گرفتن بیشترین مقدار عددی ستون row (در صورتی که row به صورت string ذخیره شده)
    $maxRow = DB::table('allocations')
        ->select(DB::raw('MAX(CAST(`row` AS UNSIGNED)) as max_row'))
        ->value('max_row');

    $nextRow = $maxRow ? ((int)$maxRow + 1) : 1;

    // گزینه‌های شهرستان موجود در دیتابیس
    $shahrOptions = Allocation::query()
        ->select('Shahrestan')
        ->distinct()
        ->whereNotNull('Shahrestan')
        ->pluck('Shahrestan');

    // گزینه‌های نوع درخواست موجود در دیتابیس
    $darkhastOptions = Allocation::query()
        ->select('darkhast')
        ->distinct()
        ->whereNotNull('darkhast')
        ->pluck('darkhast');

    // گزینه‌های تخصیص موجود در دیتابیس
    $takhsisOptions = Allocation::query()
        ->select('Takhsis_group')
        ->distinct()
        ->whereNotNull('Takhsis_group')
        ->pluck('Takhsis_group');

    // گزینه‌های منطقه موجود در دیتابیس
    $mantagheOptions = Allocation::query()
        ->select('mantaghe')
        ->distinct()
        ->whereNotNull('mantaghe')
        ->pluck('mantaghe');

        // گزینه‌های کد موجود در دیتابیس
    $codeOptions = Allocation::query()
        ->select('code')
        ->distinct()
        ->whereNotNull('code')
        ->pluck('code');

    // گزینه‌های واحد دبی موجود در دیتابیس
    $vahedOptions = Allocation::query()
        ->select('vahed')
        ->distinct()
        ->whereNotNull('vahed')
        ->pluck('vahed');

    $fileOptions = FileCategory::whereDoesntHave('children')
        ->orderBy('name')
        ->get();

    return view('admin.allocations.create', compact('nextRow','darkhastOptions', 'takhsisOptions', 'shahrOptions','mantagheOptions','vahedOptions','fileOptions','codeOptions'));
}

public function nextRow(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        //$fileName = FileCategory::findOrFail($fileCategoryId)->name;
        //$fileName = $request->query('file_name');

        $nextRow = Allocation::where('file_category_id', $fileCategoryId)
        ->max('row');

        $nextRow = $nextRow ? $nextRow + 1 : 1;


        return response()->json(['nextRow' => $nextRow]);
    }

    // ---------------------- computeTMosavvab ----------------------
    // AllocationController.php
    public function computeTMosavvab(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;

        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');

        $query = Allocation::query();

        $query->where('file_category_id', $request->file_category_id);

        if ($request->code) {
            $query->where('code', $request->code);
        }

        if ($request->Takhsis_group) {
            $query->where('Takhsis_group', $request->Takhsis_group);
        }

        $t_mosavvab = $query->max('t_mosavvab');

        return response()->json([
            't_mosavvab' => $t_mosavvab ?? 0
        ]);

    }


    // ---------------------- computeSum ----------------------
    public function computeSum(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;
        //$fileName = $request->input('file_name');
        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');
        $V_m = floatval($request->input('V_m', 0));

        // مثال ساده: جمع تمام V_mهای موجود با همان file_name + code + Takhsis_group
        $sum = Allocation::where('file_category_id', $request->file_category_id)
        ->when($request->code, fn($q) => $q->where('code', $request->code))
        ->when($request->Takhsis_group, fn($q) => $q->where('Takhsis_group', $request->Takhsis_group))
        ->sum('V_m');

        // جمع فعلی + مقدار ورودی جدید
        $sum += $V_m;

        return response()->json(['sum' => $sum]);
    }

    public function computeEditSum(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;
        $id = $request->input('id');
         $allocation = Allocation::findOrFail($id);
        $fileName = $request->input('file_name');
        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');
        $V_m = floatval($request->input('V_m', 0));

        // مقدار قبلی V_m از دیتابیس
        //$old_V_m = floatval($allocation->V_m);




        // // مثال ساده: جمع تمام V_mهای موجود با همان file_name + code + Takhsis_group
        // $sum = Allocation::when($fileName, fn($q) => $q->where('file_name', $fileName))
        //                  ->when($code, fn($q) => $q->where('code', $code))
        //                  ->when($takhsis, fn($q) => $q->where('Takhsis_group', $takhsis))
        //                  ->sum('V_m');

        $currentRow = is_numeric($allocation->row) ? $allocation->row : intval($allocation->row);
        $sumBefore = Allocation::when($fileName, fn($q) => $q->where('file_name', $fileName))
                           ->when($code, fn($q) => $q->where('code', $code))
                           ->when($takhsis, fn($q) => $q->where('Takhsis_group', $takhsis))
                           ->where('row', '<', $currentRow)
                           ->sum('V_m');

        //$sum = floatval($allocation->sum);

        $sum = $sumBefore + $V_m;
        // جمع فعلی + مقدار ورودی جدید
        //$sum-=$old_V_m;
        //$sum += $V_m;

        return response()->json(['sum' => $sum]);
    }

    



public function store(Request $request)
{
    // اعتبارسنجی پایه‌ای
    $validated = $request->validate([
        'row'           => 'nullable',
        'Shahrestan'    => 'nullable|string|max:255',
        'sal'           => 'nullable|integer',
        'erja'          => 'nullable|date',
        'code'          => 'nullable|integer',
        'mantaghe'      => 'nullable|string|max:255',
        'Abadi'         => 'nullable|string|max:255',
        'kelace'        => 'required|unique:allocations,kelace|max:255',
        'motaghasi'     => 'nullable|string|max:255',
        'darkhast'      => 'nullable|string|max:255',
        'Takhsis_group' => 'nullable|string|max:255',
        'masraf'        => 'nullable|string|max:255',
        'comete'        => 'nullable|date',
        'shomare'       => 'nullable|string|max:255',
        'date_shimare'  => 'nullable|date',
        'vahed'         => 'nullable|string|max:255',
        'q_m'           => 'nullable|integer',
        'V_m'           => 'nullable',
        't_mosavvab'    => 'nullable',
        'mosavabat'     => 'nullable|string|max:255',
        'file_name'     => 'nullable|string|max:255',
        'file_category_id' => 'required|exists:file_categories,id',

    ]);

    // --- 1) تبدیل تاریخ شمسی و اعداد فارسی ---
    $dateFields = ['erja', 'comete', 'date_shimare'];
    foreach ($dateFields as $field) {
        if (!empty($validated[$field])) {
            $val = $validated[$field];
            // تبدیل اعداد فارسی به انگلیسی
            $val = str_replace(
                ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'],
                ['0','1','2','3','4','5','6','7','8','9'],
                $val
            );
            try {
                $validated[$field] = Jalalian::fromFormat('Y/m/d', $val)->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                $validated[$field] = Carbon::parse($val)->format('Y-m-d');
            }
        }
    }

    // --- 2) helper: تبدیل اعداد فارسی/کامای هزارگان ---
    $normalizeNumber = function($v) {
        if ($v === null || $v === '') return null;
        $v = (string)trim($v);
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','،',','];
        $english = ['0','1','2','3','4','5','6','7','8','9','.','.',''];
        $v = str_replace($persian, $english, $v);
        $v = preg_replace('/[^\d\.\-]/', '', $v);
        return is_numeric($v) ? (float)$v : null;
    };

    $currentVm = $normalizeNumber($validated['V_m']);
    $t_mosavvab = $normalizeNumber($validated['t_mosavvab']);

    // --- 3) نرمال‌سازی file_name ---
    // $fileName = $validated['file_name'] ?? null;
    // if ($fileName) {
    //     $fileName = trim($fileName);
    //     $fileName = pathinfo($fileName, PATHINFO_FILENAME);
    // }
    


    // --- 4) نرمال‌سازی code و Takhsis_group ---
    $code = isset($validated['code']) && $validated['code'] !== '' ? $validated['code'] : null;
    $takhsis = isset($validated['Takhsis_group']) && $validated['Takhsis_group'] !== '' ? $validated['Takhsis_group'] : null;

    try {

        $fileCategoryId = $validated['file_category_id'];
        $fileName = FileCategory::find($fileCategoryId)?->name;


        $allocation = DB::transaction(function () use ($validated, $fileName, $currentVm, $t_mosavvab, $code, $takhsis) {
            

            $fileCategoryId = $validated['file_category_id'];

            // محاسبه next row با lock
            $maxRowQuery = Allocation::query();
            $fileName !== null ? $maxRowQuery->where('file_name', $fileName) : $maxRowQuery->whereNull('file_name');
            $maxRow = $maxRowQuery->select(DB::raw('MAX(CAST(`row` AS UNSIGNED)) as max_row'))->lockForUpdate()->value('max_row');
            $nextRow = $maxRow ? ((int)$maxRow + 1) : 1;

            // محاسبه مجموع V_m قبلی
            $sumQuery = Allocation::query();
            $code !== null ? $sumQuery->where('code', $code) : $sumQuery->whereNull('code');
            $takhsis !== null ? $sumQuery->where('Takhsis_group', $takhsis) : $sumQuery->whereNull('Takhsis_group');
            $fileName !== null ? $sumQuery->where('file_name', $fileName) : $sumQuery->whereNull('file_name');
            $otherSum = (float) $sumQuery->lockForUpdate()->sum('V_m');

            $finalSum = round(($otherSum + ($currentVm ?? 0)), 3);
            $baghi = round(($t_mosavvab ?? 0) - $finalSum, 3);

            // آماده‌سازی داده برای ایجاد رکورد
            $toCreate = $validated;
            $toCreate['row'] = $nextRow;
            $toCreate['file_name'] = $fileName;
            $toCreate['file_category_id'] = $fileCategoryId;
            $toCreate['V_m'] = $currentVm;
            $toCreate['t_mosavvab'] = $t_mosavvab;
            $toCreate['sum'] = $finalSum;
            $toCreate['baghi'] = $baghi;

            return Allocation::create($toCreate);
        }, 5); // 5 تلاش در صورت deadlock
    } catch (\Throwable $e) {
        
        return redirect()->back()->withInput()->withErrors(['general' => 'خطا هنگام ذخیره رکورد، لطفاً لاگ را بررسی کنید.']);
    }

    return redirect()->route('allocations.index')->with('success', 'رکورد با موفقیت ایجاد شد.');
}


public function edit($id){ 
    $allocation = Allocation::find($id);
    // گرفتن بیشترین مقدار عددی ستون row (در صورتی که row به صورت string ذخیره شده)
    $maxRow = DB::table('allocations')
        ->select(DB::raw('MAX(CAST(`row` AS UNSIGNED)) as max_row'))
        ->value('max_row');

    $nextRow = $maxRow ? ((int)$maxRow + 1) : 1;

    // گزینه‌های شهرستان موجود در دیتابیس
    $shahrOptions = Allocation::query()
        ->select('Shahrestan')
        ->distinct()
        ->whereNotNull('Shahrestan')
        ->pluck('Shahrestan');

    // گزینه‌های نوع درخواست موجود در دیتابیس
    $darkhastOptions = Allocation::query()
        ->select('darkhast')
        ->distinct()
        ->whereNotNull('darkhast')
        ->pluck('darkhast');

    // گزینه‌های تخصیص موجود در دیتابیس
    $takhsisOptions = Allocation::query()
        ->select('Takhsis_group')
        ->distinct()
        ->whereNotNull('Takhsis_group')
        ->pluck('Takhsis_group');

    // گزینه‌های منطقه موجود در دیتابیس
    $mantagheOptions = Allocation::query()
        ->select('mantaghe')
        ->distinct()
        ->whereNotNull('mantaghe')
        ->pluck('mantaghe');

        // گزینه‌های کد موجود در دیتابیس
    $codeOptions = Allocation::query()
        ->select('code')
        ->distinct()
        ->whereNotNull('code')
        ->pluck('code');

    // گزینه‌های واحد دبی موجود در دیتابیس
    $vahedOptions = Allocation::query()
        ->select('vahed')
        ->distinct()
        ->whereNotNull('vahed')
        ->pluck('vahed');

        $fileOptions = Allocation::query()
        ->select('file_name')
        ->distinct()
        ->whereNotNull('file_name')
        ->pluck('file_name');

        
    return view('admin.allocations.edit', compact('id','darkhastOptions', 'takhsisOptions', 'shahrOptions','mantagheOptions','vahedOptions','fileOptions','codeOptions'))->with('allocation',$allocation); }


public function show(Request $request, $id)
{
    try {
        // تست ساده: فقط id را برگردان
        return response()->json(['ok' => true, 'id' => $id]);
    } catch (\Throwable $e) {
        Log::error('show simple test error: '.$e->getMessage());
        return response()->json(['error'=>'server'], 500);
    }
}



public function update(Request $request, $id)
{
    $allocation = Allocation::findOrFail($id);

    $validated = $request->validate([
        'row' => [
         'required',
         Rule::unique('allocations')->where(function ($q) use ($request) {
             return $q->where('file_name', $request->input('file_name'));
         })->ignore($allocation->id)
        ],
        'Shahrestan'    => 'nullable|string|max:255',
        'sal'           => 'nullable|integer',
        'erja'          => 'nullable|string', // ممکن است میلادی یا شمسی؛ تبدیل پایین انجام می‌شود
        'code'          => 'nullable|integer',
        'mantaghe'      => 'nullable|string|max:255',
        'Abadi'         => 'nullable|string|max:255',
        'kelace'        => ['required', Rule::unique('allocations','kelace')->ignore($allocation->id)],
        'motaghasi'     => 'nullable|string|max:255',
        'darkhast'      => 'nullable|string|max:255',
        'Takhsis_group' => 'nullable|string|max:255',
        'masraf'        => 'nullable|string|max:255',
        'comete'        => 'nullable|string',
        'shomare'       => 'nullable|string|max:255',
        'date_shimare'  => 'nullable|string',
        'vahed'         => 'nullable|string|max:255',
        'q_m'           => 'nullable|integer',
        'V_m'           => 'numeric|nullable',
        'sum'           => 'numeric|nullable',
        't_mosavvab'    => 'numeric|nullable',
        //'baghi'         => 'numeric|nullable',
        'mosavabat'     => 'nullable|string|max:255',
    ]);

    // helper: تبدیل اعداد فارسی به انگلیسی
    $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    $english = ['0','1','2','3','4','5','6','7','8','9'];

    $dateFields = ['erja', 'comete', 'date_shimare'];
    foreach ($dateFields as $f) {
        if (!empty($validated[$f])) {
            $val = str_replace($persian, $english, $validated[$f]);

            // اگر ورودی فرمت YYYY/MM/DD شمسی باشد، سعی کن به میلادی تبدیل کنی
            try {
                // اگر سال >= 1300 -> شمسی
                if (preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $val) && (int)explode('/',$val)[0] >= 1300) {
                    $validated[$f] = Jalalian::fromFormat('Y/m/d', $val)->toCarbon()->format('Y-m-d');
                } else {
                    // تلاش با Carbon
                    $validated[$f] = Carbon::parse($val)->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // اگر تبدیل نشد، پاکش کن
                $validated[$f] = null;
            }
        }
    }

    // $t = isset($validated['t_mosavvab']) ? (float)$validated['t_mosavvab'] : (float)$allocation->t_mosavvab;
    // $s = isset($validated['sum']) ? (float)$validated['sum'] : (float)$allocation->sum;
    // $validated['baghi'] = round($t - $s, 3);

    $normalize = function($v) {
    if ($v === null || $v === '') return null;
    $v = str_replace(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','،',','], ['0','1','2','3','4','5','6','7','8','9','.','.',''], $v);
    return is_numeric($v) ? (float)$v : null;
    };

    $validated['V_m'] = $normalize($validated['V_m']);
    $validated['t_mosavvab'] = $normalize($validated['t_mosavvab']);
    $validated['sum'] = $normalize($validated['sum']);

    $validated['baghi'] = round( ($validated['t_mosavvab'] ?? (float)$allocation->t_mosavvab) - ($validated['sum'] ?? (float)$allocation->sum), 3);



    $allocation->update($validated);

    // پاسخ JSON برای AJAX
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'تغییرات با موفقیت ذخیره شد',
            'data' => $allocation->fresh()
        ], 200);
    }

    return redirect()->route('allocations.index')->with('success', 'تغییرات ذخیره شد');
}

public function filterOptions(Request $request)
{
    $file = $request->query('file_name');
    $codes = Allocation::where('file_name', $file)->distinct()->pluck('code')->filter()->values();
    $takhsis = Allocation::where('file_name', $file)->distinct()->pluck('Takhsis_group')->filter()->values();
    // می‌تونی بقیه فیلدها را هم اضافه کنی
    return response()->json(['codes' => $codes, 'takhsis' => $takhsis]);
}



public function destroy(Request $request, $id)
{
    $allocation = Allocation::findOrFail($id);
    $allocation->delete();

    // اگر درخواست AJAX یا خواستنده JSON است، پاسخ JSON بده
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'رکورد با موفقیت حذف شد'
        ], 200);
    }

    return back()->with('success', 'رکورد با موفقیت حذف شد.');
}


// validation helper
protected function validateData(Request $request, $id = null){
    return $request->validate([
      'row' => ['required','string', Rule::unique('allocations','row')->ignore($id)],
      'Shahrestan' => 'nullable|string',
      'sal' => 'nullable|integer',
      'erja' => 'nullable|date',
      'kelace' => ['nullable','string', Rule::unique('allocations','kelace')->ignore($id)],
      // ... بقیه فیلدها
    ]);
}

// تبدیل تاریخ اگر ورودی جلالی است (نمونه)
protected function convertDatesIfJalali(array $data){
    if(!empty($data['erja']) && preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/',$data['erja'])){
        // مثال: 1402/01/12 -> به میلادی تبدیل کن (نیاز به بسته Morilog\Jalali)
        try{
            $data['erja'] = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $data['erja'])->toCarbon()->format('Y-m-d');
        }catch(\Throwable $e){}
    }
    // تکرار برای comete و date_shimare
    return $data;
}



/**
 * تبدیل یک رشته تاریخ (شمسی یا میلادی) به تاریخ میلادی فرمت Y-m-d
 * - اگر ورودی شبیه 1403/10/22 یا 1403-10-22 باشه -> فرض می‌کنیم شمسی و تبدیل می‌کنیم.
 * - اگر سال <= 1400 (مثلاً 2025-01-12) باشه -> فرض می‌کنیم میلادی و به Carbon تبدیل می‌کنیم.
 * - در صورت نامعتبر بودن => بازگشت null
 */
private function parseToGregorianDate(?string $value): ?string
{
    if (empty($value)) return null;

    // نرمال‌سازی: تبدیل '-' به '/' و ترمیم فاصله‌ها
    $v = trim(str_replace('-', '/', $value));

    // اگر فرمت عددی با 3 بخش باشد (YYYY/MM/DD)
    if (preg_match('/^\d{2,4}\/\d{1,2}\/\d{1,2}$/', $v)) {
        $parts = explode('/', $v);
        $year = (int) $parts[0];

        // اگر سال بزرگتر یا مساوی 1300 فرض شمسی (سال‌های شمسی معمولاً 13xx یا 14xx)
        if ($year >= 1300) {
            try {
                // Jalalian::fromFormat نیاز به فرمت یکنواخت دارد
                $jalali = str_pad($parts[0], 4, '0', STR_PAD_LEFT) . '/'
                        . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '/'
                        . str_pad($parts[2], 2, '0', STR_PAD_LEFT);

                return Jalalian::fromFormat('Y/m/d', $jalali)->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        } else {
            // فرض میلادی (مثلاً 2025/01/12)
            try {
                return Carbon::createFromFormat('Y/m/d', $v)->format('Y-m-d');
            } catch (\Throwable $e) {
                // سعی کن با پارس معمولی هم تبدیل کنی
                try {
                    return Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e2) {
                    return null;
                }
            }
        }
    }

    // اگر رشته‌ای است که Carbon می‌تواند parse کند (مثلاً 2025-01-12)
    try {
        return Carbon::parse($value)->format('Y-m-d');
    } catch (\Throwable $e) {
        return null;
    }
}

public function export()
{
    return Excel::download(new AllocationsExport , 'allocations.xlsx');
}




}

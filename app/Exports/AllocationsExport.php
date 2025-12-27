<?php

namespace App\Exports;

use App\Models\Allocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllocationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Allocation::select(
            'row',
            'Shahrestan',
            'sal',
            'erja',
            'code',
            'mantaghe',
            'Abadi',
            'kelace',
            'motaghasi',
            'darkhast',
            'Takhsis_group',
            'masraf',
            'comete',
            'shomare',
            'date_shimare',
            'vahed',
            'q_m',
            'V_m',
            't_mosavvab',
            'sum',
            'baghi',
            'mosavabat'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ردیف',
            'شهرستان',
            'سال',
            'ارجاع',
            'کد',
            'منطقه',
            'آبادی',
            'کلاسه',
            'متقاضی',
            'نوع درخواست',
            'گروه تخصیص',
            'مصرف',
            'کمیته',
            'شماره',
            'تاریخ شماره',
            'واحد',
            'Q(m)',
            'V(m)',
            'تخصیص پنجم',
            'جمع',
            'باقی‌مانده',
            'مصوبات',
        ];
    }
}

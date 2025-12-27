<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DebugImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // نمایش اولین ردیف برای فهمیدن نام دقیق کلیدها
        dd($rows->first()->toArray());
    }
}

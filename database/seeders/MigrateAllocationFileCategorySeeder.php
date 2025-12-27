<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allocation;
use App\Models\FileCategory;

class MigrateAllocationFileCategorySeeder extends Seeder
{
    public function run()
    {
        // mapping بین file_name قدیمی و نود جدید
        $map = FileCategory::pluck('id', 'name')->toArray();

        Allocation::whereNull('file_category_id')
            ->chunkById(500, function ($rows) use ($map) {
                foreach ($rows as $row) {
                    if (isset($map[$row->file_name])) {
                        $row->update([
                            'file_category_id' => $map[$row->file_name]
                        ]);
                    }
                }
            });
    }
}

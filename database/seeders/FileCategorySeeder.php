<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FileCategory;


class FileCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $surface = FileCategory::create(['name' => 'منابع سطحی']);

        $dams = FileCategory::create([
            'name' => 'سدها',
            'parent_id' => $surface->id
        ]);
        $dams2 = FileCategory::create([
            'name' => 'رودخانه',
            'parent_id' => $surface->id
        ]);
        $dams3 = FileCategory::create([
            'name' => 'چشمه',
            'parent_id' => $surface->id
        ]);

        FileCategory::insert([
            ['name' => 'دامغان', 'parent_id' => $dams->id],
            ['name' => 'مجن', 'parent_id' => $dams->id],
            ['name' => 'کالپوش', 'parent_id' => $dams->id],
            ['name' => 'نمرود', 'parent_id' => $dams->id],
        ]);
        FileCategory::insert([
            ['name' => 'حبله رود', 'parent_id' => $dams2->id],
            ['name' => 'سمنان', 'parent_id' => $dams2->id],
        ]);
        FileCategory::insert([
            ['name' => 'روزیه', 'parent_id' => $dams3->id],
            ['name' => 'تلخاب', 'parent_id' => $dams3->id],
        ]);

        $surface2 = FileCategory::create(['name' => 'پساب']);
        FileCategory::insert([
            ['name' => 'مهدیشهر', 'parent_id' => $surface2->id],
            ['name' => 'شمال', 'parent_id' => $surface2->id],
            ['name' => 'سرخه', 'parent_id' => $surface2->id],
            ['name' => 'دامغان', 'parent_id' => $surface2->id],
            ['name' => 'شاهرود1', 'parent_id' => $surface2->id],
            ['name' => 'شاهرود2', 'parent_id' => $surface2->id],
            ['name' => 'شهمیرزاد', 'parent_id' => $surface2->id],
            ['name' => 'گرمسار مسکن مهر', 'parent_id' => $surface2->id],
            ['name' => 'گرمسار', 'parent_id' => $surface2->id],
        ]);

        $surface3 = FileCategory::create(['name' => 'آب های زیرزمینی']);
        FileCategory::insert([
            ['name' => 'برنامه چهارم', 'parent_id' => $surface3->id],
            ['name' => 'برنامه پنجم', 'parent_id' => $surface3->id],
            ['name' => 'برنامه ششم', 'parent_id' => $surface3->id],
            ['name' => 'نامتعارف', 'parent_id' => $surface3->id],
        ]);

    }
}

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ConvertAllocationsToUtf8mb4 extends Migration
{
    public function up()
    {
        // تبدیل engine (اختیاری، ولی توصیه‌شده)
        DB::statement("ALTER TABLE `allocations` ENGINE=InnoDB");
        // تبدیل charset و collation کل جدول — این دستور تمام ستون‌های متن را تبدیل می‌کند
        DB::statement("ALTER TABLE `allocations` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public function down()
    {
        // در صورت نیاز بازگردانی به utf8 یا latin1 را اینجا بنویس
        DB::statement("ALTER TABLE `allocations` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
    }
}

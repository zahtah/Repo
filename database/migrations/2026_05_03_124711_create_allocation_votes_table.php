<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allocation_votes', function (Blueprint $table) {
            $table->id(); // کلید اصلی اتوماتیک
            $table->foreignId('allocation_id')->constrained()->onDelete('cascade'); // کلید خارجی به جدول allocations
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // کلید خارجی به جدول users
            $table->text('comment')->nullable(); // فیلد نظر کاربر (می‌تواند خالی باشد)
            $table->tinyInteger('vote')->nullable(); // مثلاً برای رای مثبت/منفی (اختیاری)
            // می‌توانید فیلدهای دیگری مانند تاریخ رای‌گیری، امتیاز و ... را اضافه کنید
            // $table->timestamp('voted_at')->nullable();
            $table->timestamps(); // فیلدهای created_at و updated_at

            // اطمینان از اینکه هر کاربر فقط یک بار برای یک allocation می‌تواند نظر بدهد (اختیاری)
            // $table->unique(['allocation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_votes');
    }
};

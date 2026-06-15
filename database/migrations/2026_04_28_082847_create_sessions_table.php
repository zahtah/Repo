<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('session_number'); // شماره جلسه
            $table->string('title')->nullable(); // عنوان اختیاری
            $table->text('description')->nullable(); // توضیحات
            $table->date('date'); // تاریخ جلسه
            $table->time('time'); // ساعت جلسه
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
};

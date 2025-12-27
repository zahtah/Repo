<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileNameToAllocationsTable extends Migration
{
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('id')->default('برنامه پنجم'); // جایگزین `after` با ستون مناسب در صورت نیاز
        });
    }

    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->dropColumn('file_name');
        });
    }
}

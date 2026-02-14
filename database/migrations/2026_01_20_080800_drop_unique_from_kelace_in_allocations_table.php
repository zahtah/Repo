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
        // Schema::table('allocations', function (Blueprint $table) {
        //     $table->dropUnique('allocations_kelace_unique');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * 
     */
    public function down()
    {
        // Schema::table('allocations', function (Blueprint $table) {
        //     $table->unique('kelace');
        // });
    }
};

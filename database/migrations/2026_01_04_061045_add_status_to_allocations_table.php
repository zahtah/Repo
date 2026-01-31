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
    //     $table->enum('status', [
    //         'draft',
    //         'pending',
    //         'approved',
    //         'rejected'
    //     ])->default('approved');

    //     $table->foreignId('created_by')
    //           ->after('id')
    //           ->constrained('users');

    //     $table->foreignId('approved_by')
    //           ->nullable()
    //           ->constrained('users');

    //     $table->timestamp('approved_at')->nullable();
    // });
}

public function down()
{
    // Schema::table('allocations', function (Blueprint $table) {
    //     $table->dropColumn([
    //         'status',
    //         'created_by',
    //         'approved_by',
    //         'approved_at'
    //     ]);
    // });
}

};

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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->string('row')->nullable();
            $table->string('Shahrestan')->nullable();
            $table->integer('sal')->nullable();
            $table->date('erja')->nullable();
            $table->integer('code')->nullable();
            $table->string('mantaghe')->nullable();
            $table->string('Abadi')->nullable();
            $table->string('kelace')->nullable();
            $table->string('motaghasi')->nullable();
            $table->string('darkhast')->nullable();
            $table->string('Takhsis_group')->nullable();
            $table->string('masraf')->nullable();
            $table->date('comete')->nullable();
            $table->string('shomare')->nullable();
            $table->date('date_shimare')->nullable();
            $table->string('vahed')->nullable();
            $table->integer('q_m')->nullable();
            $table->decimal('V_m', 10, 2)->nullable();
            $table->decimal('t_mosavvab',10,2)->nullable();
            $table->decimal('sum', 10, 2)->nullable();
            $table->decimal('baghi', 10, 2)->nullable();
            $table->string('mosavabat')->nullable();
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
        Schema::dropIfExists('allocations');
    }
};

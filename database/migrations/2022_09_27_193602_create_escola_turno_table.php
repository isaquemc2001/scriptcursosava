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
        Schema::create('escola_turno', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->integer('cod_escola');
            $table->integer('cod_turno');
            $table->foreign('cod_escola')->references('id')->on('escolas');
            $table->foreign('cod_turno')->references('id')->on('turnos');
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
        Schema::dropIfExists('escola_turno');
    }
};

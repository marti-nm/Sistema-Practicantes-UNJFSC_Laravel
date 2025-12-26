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
        Schema::create('grupo_practica', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('id_docente')->constrained('asignacion_persona')->onDelete('restrict');
            $table->foreignId('id_supervisor')->constrained('asignacion_persona')->onDelete('restrict');
            $table->foreignId('id_sa')->constrained('seccion_academica')->onDelete('restrict');
            $table->timestamps();
            $table->boolean('state')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_practica');
    }
};

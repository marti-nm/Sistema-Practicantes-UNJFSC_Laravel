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
        Schema::create('asignacion_persona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_persona')->constrained('personas')->onDelete('restrict');
            $table->foreignId('id_rol')->constrained('type_users')->onDelete('restrict');
            $table->foreignId('id_semestre')->constrained('semestres')->onDelete('restrict');
            $table->foreignId('id_sa')->nullable()->constrained('seccion_academica')->onDelete('restrict');
            $table->timestamps();
            $table->integer('state')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignacion_persona');
    }
};

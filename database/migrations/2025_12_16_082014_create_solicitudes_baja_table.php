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
        Schema::create('solicitudes_baja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ap_delete');
            $table->unsignedBigInteger('id_sa');
            $table->unsignedBigInteger('id_ap_sol');
            $table->string('justification_sol');
            $table->enum('tipo_sol', ['deshabilitar', 'eliminar', 'habilitar']);
            $table->enum('estado_sol', ['pendiente', 'aceptado', 'rechazado']);
            $table->unsignedBigInteger('id_ap_admin')->nullable();
            $table->string('comentario_admin')->nullable();
            $table->timestamps();
            $table->integer('state')->default(1);

            $table->foreign('id_ap_delete')->references('id')->on('asignacion_persona')->onDelete('cascade');
            $table->foreign('id_ap_sol')->references('id')->on('asignacion_persona')->onDelete('cascade');
            $table->foreign('id_ap_admin')->references('id')->on('asignacion_persona')->onDelete('cascade');
            $table->foreign('id_sa')->references('id')->on('seccion_academica')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_baja');
    }
};

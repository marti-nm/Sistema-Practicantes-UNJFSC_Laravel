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
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->morphs('archivo');
            $table->string('estado_archivo', 20)->default('Enviado');
            $table->string('tipo', 50);
            $table->string('ruta');
            $table->string('comentario')->nullable();
            $table->unsignedBigInteger('subido_por_user_id')->nullable();
            $table->unsignedBigInteger('revisado_por_user_id')->nullable();
            $table->integer('estado');

            $table->foreign('subido_por_user_id')->references('id')->on('asignacion_persona')->onDelete('restrict');
            $table->foreign('revisado_por_user_id')->references('id')->on('asignacion_persona')->onDelete('restrict');

            $table->timestamps();

            $table->index(['tipo', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivos');
    }
};

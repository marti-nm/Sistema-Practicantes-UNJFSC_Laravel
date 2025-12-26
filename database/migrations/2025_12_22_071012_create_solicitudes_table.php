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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ap_solicitante');
            $table->unsignedBigInteger('id_ap_revisor')->nullable();
            
            // Crea solicitudable_id (unsignedBigInteger) y solicitudable_type (string)
            $table->morphs('solicitudable'); 
            
            $table->string('tipo'); // ej: rectificacion_nota, cambio_tipo_practica, baja_estudiante
            $table->text('motivo'); // Argumento del solicitante
            $table->text('justificacion')->nullable(); // Comentario del revisor/admin
            $table->json('data')->nullable(); // Para guardar valores específicos (ej: nueva_nota)
            $table->integer('state')->default(0); // 0: Pendiente, 1: Aprobado, 2: Rechazado
            
            $table->timestamps();

            // Relaciones foráneas
            $table->foreign('id_ap_solicitante')->references('id')->on('asignacion_persona');
            $table->foreign('id_ap_revisor')->references('id')->on('asignacion_persona');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes');
    }
};

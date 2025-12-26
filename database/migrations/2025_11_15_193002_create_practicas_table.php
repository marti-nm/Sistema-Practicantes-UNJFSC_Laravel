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
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ap')->constrained('asignacion_persona')->onDelete('restrict');
            $table->string('estado_practica')->nullable();
            $table->string('tipo_practica')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('observacion')->nullable();
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->timestamps();
            $table->integer('state')->default(0);

            /*
            $table->string('ruta_fut')->nullable();
            $table->string('ruta_carta_aceptacion')->nullable();
            $table->string('ruta_carta_presentacion')->nullable();
            $table->string('ruta_plan_actividades')->nullable();
            $table->string('ruta_constancia_cumplimiento')->nullable();
            $table->string('ruta_registro_actividades')->nullable();
            $table->string('ruta_control_actividades')->nullable();
            $table->string('ruta_informe_final')->nullable();
            $table->timestamps();
            $table->integer('state');*/
            //añadir el foring key de la tabla grupo practica cuando se cree la tabla 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('practicas');
    }
};

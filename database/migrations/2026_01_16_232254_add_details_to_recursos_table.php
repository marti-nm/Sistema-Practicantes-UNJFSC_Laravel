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
        Schema::table('recursos', function (Blueprint $table) {
            $table->tinyInteger('nivel')->default(1)->after('descripcion'); // 1: Global, 2: Facultad, 3: Escuela, 4: Sección
            $table->unsignedBigInteger('id_semestre')->nullable()->after('nivel');
            $table->unsignedBigInteger('id_rol')->nullable()->after('id_semestre'); // null = Todos
            
            $table->foreign('id_semestre')->references('id')->on('semestres')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('type_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign(['id_semestre']);
            $table->dropForeign(['id_rol']);
            $table->dropColumn(['nivel', 'id_semestre', 'id_rol']);
        });
    }
};

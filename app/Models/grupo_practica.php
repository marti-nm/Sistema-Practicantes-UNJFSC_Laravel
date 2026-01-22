<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grupo_practica extends Model
{
    use HasFactory;

    protected $table = 'grupo_practica';
    protected $fillable = [
        'name',
        'id_docente',
        'id_supervisor',
        'id_sa',
        'id_modulo',
        'state',
    ];


    public function docente()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_docente');
    }

    public function supervisor()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_supervisor');
    }

    public function seccion_academica()
    {
        return $this->belongsTo(seccion_academica::class, 'id_sa');
    }

    public function grupo_estudiante()
    {
        return $this->hasMany(grupo_estudiante::class, 'id_gp');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }
}

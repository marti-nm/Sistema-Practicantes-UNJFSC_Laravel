<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grupo_estudiante extends Model
{
    use HasFactory;
    protected $table = 'grupo_estudiante';

    protected $fillable = [
    'id_estudiante',
    'id_gp',
    'state'
    ];
    public function estudiante()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_estudiante');
    }
    public function grupo_practica()
    {
        return $this->belongsTo(grupo_practica::class, 'id_gp');
    }


}

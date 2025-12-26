<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JefeInmediato extends Model
{
    protected $table = 'jefe_inmediato';
    protected $fillable = [
        'nombres',
        'dni',
        'cargo',
        'area',
        'correo',
        'telefono',
        'web',
        'comentario',
        'id_practica',
        'state'
    ];

    public function practica()
    {
        return $this->belongsTo(Practica::class, 'id_practica');
    }
}

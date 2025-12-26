<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\JefeInmediato;
use App\Models\Practica;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'id_practica',
        'nombre',
        'ruc',
        'razon_social',
        'direccion',
        'telefono',
        'correo',
        'web',
        'comentario',
        'state'
    ];

    public function practicas()
    {
        return $this->belongsTo(Practica::class, 'id_practica');
    }
}

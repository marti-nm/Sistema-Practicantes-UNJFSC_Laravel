<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acreditar extends Model
{
    use HasFactory;
    protected $table = 'acreditaciones';
    protected $fillable = [
        'ap_id',
        'estado_acreditacion',
        'observacion',
        'f_acreditacion',
        'estado' 
    ];

    public function asignacion_persona()
    {
        return $this->belongsTo(asignacion_persona::class, 'ap_id');
    }

    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivo');
    }
}

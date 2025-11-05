<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;
    protected $table = 'archivos';
    protected $fillable = [
        'archivo_id',
        'archivo_type',
        'estado_archivo',
        'tipo',
        'ruta',
        'comentario',
        'subido_por_user_id',
        'revisado_por_user_id',
        'estado'
    ];

    public function archivos() {
        return $this->morphTo();
    }

    public function uploader() {
        return $this->belongsTo(asignado_persona::class, 'subido_por_user_id');
    }

    public function reviewer() {
        return $this->belongsTo(asignado_persona::class, 'revisado_por_user_id');
    }
}

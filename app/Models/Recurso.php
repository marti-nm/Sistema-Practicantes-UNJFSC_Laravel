<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $fillable = [
        'nombre',
        'tipo',
        'ruta',
        'descripcion',
        'subido_por_ap',
        'id_sa',
        'state',
        'nivel',
        'id_semestre',
        'id_rol'
    ];

    /**
     * Relación con la persona que subió el recurso
     */
    public function uploader()
    {
        return $this->belongsTo(asignacion_persona::class, 'subido_por_ap');
    }

    /**
     * Relación con la sección académica
     */
    public function seccionAcademica()
    {
        return $this->belongsTo(seccion_academica::class, 'id_sa');
    }

    /**
     * Scope para recursos activos
     */
    public function scopeActivo($query)
    {
        return $query->where('state', 1);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

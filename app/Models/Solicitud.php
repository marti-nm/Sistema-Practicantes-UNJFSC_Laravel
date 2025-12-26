<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'id_ap_solicitante',
        'id_ap_revisor',
        'solicitudable_id',
        'solicitudable_type',
        'tipo',
        'motivo',
        'justificacion',
        'data', // Campo flexible para valores adicionales
        'state'
    ];

    protected $casts = [
        'data' => 'array',
        'state' => 'integer'
    ];

    /**
     * Obtener el modelo al que pertenece la solicitud (Practica, AsignacionPersona, etc.)
     */
    public function solicitudable()
    {
        return $this->morphTo();
    }

    /**
     * Persona que realizó la solicitud (Docente o Estudiante)
     */
    public function solicitante()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_ap_solicitante');
    }

    /**
     * Usuario (normalmente Admin) que revisó la solicitud
     */
    public function revisor()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_ap_revisor');
    }
}

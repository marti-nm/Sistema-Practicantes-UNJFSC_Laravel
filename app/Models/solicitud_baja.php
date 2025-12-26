<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class solicitud_baja extends Model
{
    use HasFactory;
    protected $table = 'solicitudes_baja';
    protected $fillable = [
        'id_ap_delete',
        'id_sa',
        'id_ap_sol',
        'justification_sol',
        'tipo_sol',
        'estado_sol',
        'id_ap_admin',
        'comentario_admin',
        'state',
    ];

    public function ap_delete()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_ap_delete');
    }

    public function sa()
    {
        return $this->belongsTo(seccion_academica::class, 'id_sa');
    }

    public function ap_sol()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_ap_sol');
    }

    public function ap_admin()
    {
        return $this->belongsTo(asignacion_persona::class, 'id_ap_admin');
    }
}

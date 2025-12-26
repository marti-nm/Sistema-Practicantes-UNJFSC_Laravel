<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asignacion_persona extends Model
{
    use HasFactory;
    protected $table = 'asignacion_persona';

    protected $fillable = [
        'id_persona',
        'id_rol',
        'id_semestre',
        'id_facultad',
        'id_sa',
        'state',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
    public function rol()
    {
        return $this->belongsTo(type_users::class, 'id_rol');
    }
    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'id_semestre');
    }
    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'id_facultad');
    }
    public function seccion_academica()
    {
        return $this->belongsTo(seccion_academica::class, 'id_sa');
    }
    public function acreditacion()
    {
        return $this->hasMany(Acreditar::class, 'id_ap');
    }
    public function grupo_estudiante()
    {
        return $this->hasMany(grupo_estudiante::class, 'id_estudiante');
    }
    public function matricula()
    {
        return $this->hasMany(Matricula::class, 'id_ap');
    }
    public function practicas()
    {
        return $this->hasMany(Practica::class, 'id_ap');
    }
    public function evaluaciones() {
        return $this->hasMany(Evaluacion::class, 'id_alumno');
    }
    public function evaluacion_practica() {
        return $this->hasMany(EvaluacionPractica::class, 'id_ap');
    }

    public function preguntas() {
        return $this->hasMany(Pregunta::class, 'id_ap');
    }
    public function respuestas() {
        return $this->hasMany(Respuesta::class, 'id_persona');
    }

}

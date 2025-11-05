<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class matricula extends Model
{
    use HasFactory;
    protected $table = 'matriculas';
    protected $fillable = [
    'estado_ficha',
    'estado_record',
    'ruta_ficha',
    'ruta_record',
    'persona_id',
    'estado',
    'created_at',
    'updated_at'
    
];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

}

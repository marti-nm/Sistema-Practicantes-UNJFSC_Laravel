<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use App\Models\Matricula;
use App\Models\Persona;
use App\Models\Semestre;
use App\Models\asignacion_persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class validacionMatriculaController extends Controller
{
    public function Vmatricula(){
        $user = auth()->user();
        $persona = $user->persona;
        $asignacion = $persona->asignacion_persona;
        
        if (!$asignacion) {
            return redirect()->back()->with('error', 'No se encontró asignación para este usuario');
        }
        
        $rolId = $asignacion->id_rol;
        $semestreId = $asignacion->id_semestre;
        $escuelaId = $asignacion->id_escuela;
        
        // Si es admin (rol 1), mostrar todos los estudiantes
        if ($rolId == 1) {
            $estudiantes = asignacion_persona::with([
                'persona.matricula',
                'escuela',
                'semestre'
            ])->where('id_rol', 5) // Solo estudiantes
              ->get();
        } 
        // Si es sub admin (rol 2), mostrar estudiantes de su semestre (puede no tener escuela específica)
        else if ($rolId == 2) {
            $query = asignacion_persona::with([
                'persona.matricula',
                'escuela',
                'semestre'
            ])->where('id_rol', 5) // Solo estudiantes
              ->where('id_semestre', $semestreId);
              
            // Si tiene escuela específica, filtrar por ella
            if ($escuelaId) {
                $query->where('id_escuela', $escuelaId);
            }
            
            $estudiantes = $query->get();
        }
        // Si es docente (rol 3), mostrar estudiantes de su escuela y semestre
        else if ($rolId == 3) {
            $estudiantes = asignacion_persona::with([
                'persona.matricula',
                'escuela',
                'semestre'
            ])->where('id_rol', 5) // Solo estudiantes
              ->where('id_semestre', $semestreId)
              ->where('id_escuela', $escuelaId)
              ->get();
        }
        // Si es supervisor (rol 4), mostrar estudiantes de su escuela y semestre
        else if ($rolId == 4) {
            $estudiantes = asignacion_persona::with([
                'persona.matricula',
                'escuela',
                'semestre'
            ])->where('id_rol', 5) // Solo estudiantes
              ->where('id_semestre', $semestreId)
              ->where('id_escuela', $escuelaId)
              ->get();
        }
        else {
            $estudiantes = collect(); // Lista vacía para otros roles
        }

        return view('ValidacionMatricula.ValidacionMatricula', compact('estudiantes'));
    }
    
    public function actualizarEstadoFicha(Request $request, $id)
    {
        $matricula = Matricula::findOrFail($id);
        $matricula->estado_ficha = $request->estado_ficha;
        $matricula->save();

        return back()->with('success', 'Estado de ficha actualizado correctamente');
    }

    public function actualizarEstadoRecord(Request $request, $id)
    {
        $matricula = Matricula::findOrFail($id);
        $matricula->estado_record = $request->estado_record;
        $matricula->save();

        return back()->with('success', 'Estado de récord académico actualizado correctamente');
    }


}

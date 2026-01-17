<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use App\Models\Matricula;
use App\Models\Persona;
use App\Models\Semestre;
use App\Models\asignacion_persona;
use App\Models\Facultad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class validacionMatriculaController extends Controller
{
    public function Vmatricula(Request $request){
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        $ap_now = $user->persona->asignacion_persona()->latest()->first();

        $estQuery = Persona::whereHas('asignacion_persona', function ($query) use ($id_semestre, $ap_now, $request) {
            $query->where('id_semestre', $id_semestre);
            $query->where('id_rol', 5);

            if ($ap_now->id_rol == 3) {
                $query->where('id_sa', $ap_now->id_sa);
            }

            if ($ap_now->id_rol == 2) {
                $query->whereHas('seccion_academica', function($q) use ($ap_now) {
                    $q->where('id_facultad', $ap_now->seccion_academica->id_facultad);
                });
            }

            if ($request->filled('facultad') || $request->filled('escuela') || $request->filled('seccion')) {
                $query->whereHas('seccion_academica', function ($q) use ($request) {
                    if ($request->filled('facultad')) {
                        $q->where('id_facultad', $request->facultad);
                    }
                    if ($request->filled('escuela')) {
                        $q->where('id_escuela', $request->escuela);
                    }
                    if ($request->filled('seccion')) {
                        $q->where('id', $request->seccion);
                    }
                });
            }
        });

        $estudiantes = $estQuery->with([
            'asignacion_persona.matricula.archivos' => function ($query) {
                $query->select('id', 'archivo_id', 'tipo', 'estado_archivo', 'created_at')
                        ->orderBy('created_at', 'desc');
            },
            'asignacion_persona.seccion_academica.escuela',
        ])->orderBy('apellidos', 'asc')->get();

        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        return view('ValidacionMatricula.ValidacionMatricula', compact('estudiantes', 'facultades'));
    }

    public function getMatricula($id)
    {
        $matricula = Matricula::findOrFail($id)->with('archivos')->get();
        return $matricula;
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

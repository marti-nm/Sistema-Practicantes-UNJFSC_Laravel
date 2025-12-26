<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\grupo_practica;
use App\Models\grupo_estudiante;
use App\Models\EvaluacionPractica;
use App\Models\evaluacion_archivo;
use App\Models\Facultad;
use Illuminate\Support\Facades\Log;

class RevisarController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        $ap_now = $user->persona->asignacion_persona()->where('id_semestre', $id_semestre)->first();

        $request->validate([
            'grupo' => 'nullable|exists:grupo_practica,id',
        ]);

        Log::info('Request ALL: '.json_encode($request->all()));
        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        $gpQuery = grupo_practica::whereHas('seccion_academica', function($query) use ($request, $ap_now, $id_semestre){
            $query->where('id_semestre', $id_semestre);
            if($request->filled('facultad')){
                $query->where('id_facultad', ($ap_now->id_rol == 2) ? $ap_now->seccion_academica->id_facultad : $request->facultad);
            }
            if($request->filled('escuela')){
                $query->where('id_escuela', $request->escuela);
            }
            if($request->filled('seccion')){
                $query->where('id', $request->seccion);
            }
        });

        if($ap_now->id_rol == 3) {
            $gpQuery->where('id_docente', $ap_now->id);
        }

        $grupos_practica = $gpQuery->with([
            'seccion_academica.escuela'
        ])
        ->get();

        $selected_grupo_id = $request->input('grupo');
        $id_modulo = $request->modulo;
        $id_modulo_now = 0;

        // enviar a la vista name_escuela, name_seccion, name_grupo
        $name_escuela = "s-n";
        $name_seccion = "s-n";
        $name_grupo = "s-n";

        if ($selected_grupo_id) {
            $gp = grupo_practica::where('id', $selected_grupo_id)->first();
            $id_modulo_now = $gp->id_modulo;
            $name_grupo = $gp->name;
            $name_seccion = $gp->seccion_academica->seccion;
            $name_escuela = $gp->seccion_academica->escuela->name;
            if ($id_modulo === null) {
                $id_modulo = $id_modulo_now;
            } else {
                $id_modulo = ($id_modulo > $id_modulo_now) ? $id_modulo_now : $id_modulo;
            }
        } else {
            $id_modulo = null;
        }

        // 1. Iniciar la consulta desde EvaluacionPractica para el módulo seleccionado.
        $evalQuery = EvaluacionPractica::where('id_modulo', $id_modulo)
            ->with([
                'asignacion_persona.seccion_academica.facultad',
                'asignacion_persona.seccion_academica.escuela',
                'asignacion_persona.persona', // Cargar la persona del estudiante.
                'asignacion_persona.grupo_estudiante.grupo_practica' // Cargar el grupo para mostrar su nombre.
            ]);

        if ($selected_grupo_id) {
            $evalQuery->whereHas('asignacion_persona.grupo_estudiante', function ($q) use ($selected_grupo_id) {
                $q->where('id_gp', $selected_grupo_id);
            });
        }

        // Optimización: Traer solo el estado del último anexo 7 y 8 mediante subconsultas
        $evalQuery->addSelect([
            'status_anexo_7' => evaluacion_archivo::select('state')
                ->whereColumn('id_evaluacion', 'evaluacion_practica.id')
                ->whereHas('archivos', function($q) {
                    $q->where('tipo', 'anexo_7');
                })
                ->latest()
                ->limit(1),
            'status_anexo_8' => evaluacion_archivo::select('state')
                ->whereColumn('id_evaluacion', 'evaluacion_practica.id')
                ->whereHas('archivos', function($q) {
                    $q->where('tipo', 'anexo_8');
                })
                ->latest()
                ->limit(1),
        ]);

        $grupo_estudiante = $evalQuery->get();

        return view('revisar.index', compact(
            'facultades', 
            'grupos_practica', 
            'grupo_estudiante', 
            'selected_grupo_id', 
            'id_modulo_now', 
            'id_modulo',
            'name_escuela', 
            'name_seccion', 
            'name_grupo'
        ));
    }
}

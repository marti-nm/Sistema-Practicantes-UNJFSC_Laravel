<?php

namespace App\Http\Controllers;

use App\Models\EvaluacionPractica;
use App\Models\evaluacion_archivo;
use App\Models\grupo_practica;
use App\Models\Facultad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EvaluacionPracticaController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        $ap_now = $user->persona->asignacion_persona()->first();

        $request->validate([
            'grupo' => 'nullable|exists:grupo_practica,id',
        ]);

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

        if($ap_now->id_rol == 4) {
            $gpQuery->where('id_supervisor', $ap_now->id);
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

        // Consultar evaluaciones para el módulo seleccionado y los estudiantes del grupo
        $query = EvaluacionPractica::where('id_modulo', $id_modulo)
            ->with([
                'asignacion_persona.seccion_academica.facultad',
                'asignacion_persona.seccion_academica.escuela',
                'asignacion_persona.persona',
                'asignacion_persona.grupo_estudiante.grupo_practica',
            ]);

        if ($selected_grupo_id) {
            $query->whereHas('asignacion_persona.grupo_estudiante', function ($q) use ($selected_grupo_id) {
                $q->where('id_gp', $selected_grupo_id);
            });
        }

        // Optimización: Traer solo el estado del último anexo 7 y 8 mediante subconsultas
        $query->addSelect([
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

        $grupo_estudiante = $query->get();

        return view('EvaluacionPractica.index', compact(
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

    public function getEvaluacionPractica($id_ap, $id_modulo, $anexo)
    {
        $query = EvaluacionPractica::where('id_modulo', $id_modulo)
            ->where('id_ap', $id_ap);
        $evaluacionPractica = $query->with([
            'evaluacion_archivo' => function ($query) use ($anexo) {
                $query->whereHas('archivos', function ($subQuery) use ($anexo) {
                    $subQuery->where('tipo', $anexo);
                })
                ->with(['archivos' => function ($subQuery) use ($anexo) {
                    $subQuery->where('tipo', $anexo);
                }])
                ->orderBy('created_at', 'desc');
            }
        ])->get();
        Log::info('Modulo: '.$id_modulo);
        //Log::info('evaluacionPractica: ', $evaluacionPractica->toArray());
        return response()->json($evaluacionPractica);
    }
}

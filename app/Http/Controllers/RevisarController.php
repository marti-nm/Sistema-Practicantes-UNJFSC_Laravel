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
        $ap_now = $user->persona->asignacion_persona()->latest()->first();

        // Facultades según rol
        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        // Grupos disponibles
        $gpQuery = grupo_practica::whereHas('seccion_academica', function($query) use ($request, $ap_now, $id_semestre){
            $query->where('id_semestre', $id_semestre);
            if($request->filled('facultad')) $query->where('id_facultad', $request->facultad);
            if($request->filled('escuela')) $query->where('id_escuela', $request->escuela);
            if($request->filled('seccion')) $query->where('id', $request->seccion);
        });

        if($ap_now->id_rol == 3) $gpQuery->where('id_docente', $ap_now->id);
        $grupos_practica = $gpQuery->with(['seccion_academica.escuela'])->get();

        $selected_grupo_id = $request->input('grupo');
        $id_modulo = $request->modulo;
        $id_modulo_now = 0;
        $name_escuela = $name_seccion = $name_grupo = "s-n";

        $facultad_id = $request->facultad;
        $escuela_id = $request->escuela;
        $seccion_id = $request->seccion;

        if ($selected_grupo_id) {
            $gp = grupo_practica::find($selected_grupo_id);
            if ($gp) {
                $id_modulo_now = $gp->id_modulo;
                $name_grupo = $gp->name;
                $name_seccion = $gp->seccion_academica->seccion;
                $name_escuela = $gp->seccion_academica->escuela->name;
                $id_modulo = $id_modulo ?? $id_modulo_now;

                // Sync hierarchy if not provided
                $facultad_id = $facultad_id ?? $gp->seccion_academica->id_facultad;
                $escuela_id = $escuela_id ?? $gp->seccion_academica->id_escuela;
                $seccion_id = $seccion_id ?? $gp->seccion_academica->id;
            }
        }

        // Estudiantes y sus estados para la nueva UI
        $rows = [];
        if ($id_modulo) {
            $evaluations = EvaluacionPractica::where('id_modulo', $id_modulo)
                ->with(['asignacion_persona.persona', 'asignacion_persona.seccion_academica.escuela'])
                ->whereHas('asignacion_persona.grupo_estudiante', function ($q) use ($selected_grupo_id) {
                    $q->where('id_gp', $selected_grupo_id);
                })
                ->get();

            foreach ($evaluations as $eval) {
                $ap = $eval->asignacion_persona;
                $person = $ap->persona;
                
                // Estados de anexos
                $getStatus = function($type) use ($eval) {
                    $last = evaluacion_archivo::where('id_evaluacion', $eval->id)
                        ->whereHas('archivos', fn($q) => $q->where('tipo', $type))
                        ->latest()->first();
                    return $last ? ['estado' => $this->mapState($last->state), 'id' => $last->id] : ['estado' => 'Falta'];
                };

                $rows[] = [
                    'id' => $eval->id,
                    'id_ap' => $ap->id,
                    'id_modulo' => $eval->id_modulo,
                    'people' => $person->paterno . ' ' . $person->materno . ' ' . $person->nombres,
                    'avatar' => strtoupper(substr($person->paterno, 0, 1) . substr($person->nombres, 0, 1)),
                    'escuela' => $ap->seccion_academica->escuela->name,
                    'seccion' => $ap->seccion_academica->seccion,
                    'semestre' => $ap->seccion_academica->semestre->name ?? '',
                    'archivos' => [
                        'anexo7' => $getStatus('anexo_7'),
                        'anexo8' => $getStatus('anexo_8'),
                    ]
                ];
            }
        }

        $config = [
            "module_name" => "Revisión",
            "route_button" => null,
            "api_endpoint" => "/api/evaluacion/archivos", 
            "form_action" => route("actualizar.anexo"),
            "columns" => [
                [
                    "key" => "anexo7",
                    "label" => "Anexo 7",
                    "icon" => "bi-file-earmark-text",
                ],
                [
                    "key" => "anexo8",
                    "label" => "Anexo 8",
                    "icon" => "bi-file-earmark-text",
                ]
            ],
        ];

        return view('revisar.index', compact(
            'facultades', 'grupos_practica', 'rows', 'selected_grupo_id', 
            'id_modulo_now', 'id_modulo', 'name_escuela', 'name_seccion', 'name_grupo', 'config'
        ))->with([
            'facultad_id' => $facultad_id,
            'escuela_id' => $escuela_id,
            'seccion_id' => $seccion_id
        ]);
    }

    public function index_v1(Request $request)
    {
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        $ap_now = $user->persona->asignacion_persona()->latest()->first();

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

        return view('revisar.index_v1', compact(
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

    private function mapState($state) {
        return match($state) {
            1 => 'Enviado',
            5 => 'Aprobado',
            2, 3, 4 => 'Corregir',
            default => 'Falta'
        };
    }
}

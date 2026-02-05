<?php

namespace App\Http\Controllers;

use App\Models\grupo_practica;
use App\Models\EvaluacionPractica;
use App\Models\evaluacion_archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Facultad;
use App\Models\Archivo;
use App\Models\Escuela;
use App\Models\SeccionAcademica;

class EvaluacionController extends Controller
{
    /**
     * Mostrar evaluación para estudiantes asignados al docente o supervisor.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $id_semestre = session("semestre_actual_id");
        $ap_now = $user->persona->asignacion_persona()->latest()->first();

        $request->validate([
            "grupo" => "nullable|exists:grupo_practica,id",
        ]);

        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where("id", $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        $id_grupo = $request->grupo;
        $id_modulo = $request->modulo;
        $id_modulo_now = 0;

        $facultad_id = null;
        $escuela_id = null;
        $seccion_id = null;

        if ($id_grupo) {
            $grupo = grupo_practica::with('seccion_academica')->find($id_grupo);
            if ($grupo) {
                $id_modulo_now = $grupo->id_modulo;
                if ($grupo->seccion_academica) {
                    $seccion_id = $grupo->seccion_academica->id;
                    $escuela_id = $grupo->seccion_academica->id_escuela;
                    $facultad_id = $grupo->seccion_academica->id_facultad;
                }
            }
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
                'asignacion_persona.grupo_estudiante.grupo_practica', // Cargar el grupo para mostrar su nombre.
                'evaluacion_archivo.archivos'
            ]);

        if ($id_grupo) {
            $evalQuery->whereHas('asignacion_persona.grupo_estudiante', function ($q) use ($id_grupo) {
                $q->where('id_gp', $id_grupo);
            });
        }

        $grupo_evaluaciones = $evalQuery->get();

        // Mapeo de datos para la vista unificada (Estilo AcreditarController)
        $rows = $grupo_evaluaciones->map(function ($eval) {
            $ap = $eval->asignacion_persona;
            $user = $ap->persona;
            
            // Obtenemos todos los archivos de todas las evaluaciones_archivos de esta EvaluacionPractica
            $allFiles = $eval->evaluacion_archivo->flatMap(function($ea) {
                return $ea->archivos;
            });

            $getEstado = function ($tipo) use ($allFiles, $eval, $user) {
                // Mapeo de tipos para coincidir con la DB si es necesario
                $db_tipo = ($tipo == 'anexo7') ? 'anexo_7' : (($tipo == 'anexo8') ? 'anexo_8' : $tipo);
                
                $f = $allFiles
                    ->where("tipo", $db_tipo)
                    ->sortByDesc("created_at")
                    ->first();

                // Intentar encontrar el evaluacion_archivo_id para este tipo
                $ea_id = null;
                if ($f) {
                    $ea_id = $f->archivo_id; // archivo_id en Archivo apunta a evaluacion_archivo.id
                }

                return [
                    "exists" => (bool) $f,
                    "estado" => $f ? $f->estado_archivo : "Falta",
                    "id" => $eval->id, // ID para el fetch del modal (EvaluacionPractica ID)
                    "eval_archivo_id" => $ea_id,
                    "type" => $tipo,
                    "name" => $user->apellidos . " " . $user->nombres,
                ];
            };

            return [
                "id" => $eval->id,
                "id_ap" => $eval->id_ap,
                "id_modulo" => $eval->id_modulo,
                "unique_id" => $eval->id,
                "people" => $user->apellidos . ", " . $user->nombres,
                "avatar" => substr($user->nombres, 0, 1) . substr($user->apellidos, 0, 1),
                "semestre" => $ap->semestre->codigo ?? "",
                "escuela" => $ap->seccion_academica->escuela->name ?? "",
                "facultad" => $ap->seccion_academica->escuela->facultad->name ?? "",
                "seccion" => $ap->seccion_academica->seccion ?? "",
                "archivos" => [
                    "anexo7" => $getEstado("anexo7"),
                    "anexo8" => $getEstado("anexo8")
                ],
            ];
        });

        $config = [
            "view_title" => "Gestión de Evaluaciones",
            "view_subtitle" => "Administración de evaluaciones y anexos",
            "msj_button" => null,
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

        return view('evaluacion.index', compact('facultades', 
            'id_grupo', 'facultad_id', 'escuela_id', 'seccion_id', 'id_modulo_now', 'id_modulo', 'rows', 'config'));
    }

    public function getGroups($fac, $esc, $sec): Collection
    {
        Log::info(
            "Buscando grupos con: Facultad=$fac, Escuela=$esc, Seccion=$sec",
        );

        // Convertir '0' en null para que la lógica de !empty funcione correctamente
        $fac = $fac == "0" ? null : $fac;
        $esc = $esc == "0" ? null : $esc;
        $sec = $sec == "0" ? null : $sec;

        // 1. Regla de oro: Si no hay facultad, no hay resultados.
        if (empty($fac)) {
            return collect();
        }

        $query = grupo_practica::query();

        $query->whereHas("seccion_academica", function ($q) use (
            $fac,
            $esc,
            $sec,
        ) {
            if (!empty($sec)) {
                // Si hay sección, es el filtro más específico (ID de la sección)
                $q->where("id", $sec);
            } elseif (!empty($esc)) {
                // Si no hay sección pero sí escuela, filtramos por escuela
                $q->where("id_escuela", $esc);
            } else {
                // Si solo hay facultad, filtramos por facultad
                $q->where("id_facultad", $fac);
            }
        });

        $grupos = $query->get();
        Log::info("Grupos: " . json_encode($grupos));

        return $grupos;
    }

    public function getArchivos($id_evaluacion, $tipo)
    {
        try {
            $db_tipo = ($tipo == 'anexo7') ? 'anexo_7' : (($tipo == 'anexo8') ? 'anexo_8' : $tipo);

            $archivos = Archivo::where('archivo_type', evaluacion_archivo::class)
                ->whereHasMorph('archivo', [evaluacion_archivo::class], function($query) use ($id_evaluacion) {
                    $query->where('id_evaluacion', $id_evaluacion);
                })
                ->where('tipo', $db_tipo)
                ->latest()
                ->get();

            $archivos->transform(function($archivo) {
                $relativePath = str_replace(url('/'), '', $archivo->ruta);
                $relativePath = ltrim($relativePath, '/');
                $fullPath = public_path($relativePath);
                
                $archivo->peso = (file_exists($fullPath) && !is_dir($fullPath)) 
                    ? $this->formatBytes(filesize($fullPath)) 
                    : 'N/A';
                $archivo->extension = strtoupper(pathinfo($archivo->ruta, PATHINFO_EXTENSION));
                return $archivo;
            });

            return response()->json($archivos);
        } catch (\Exception $e) {
            Log::error('Error obteniendo documentos de evaluación: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener documentos'], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

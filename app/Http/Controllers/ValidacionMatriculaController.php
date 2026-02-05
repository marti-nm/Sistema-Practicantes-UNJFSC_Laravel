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
use App\Models\Archivo;

class ValidacionMatriculaController extends Controller
{
    public function Vmatricula(Request $request)
    {
        $user = auth()->user();
        $id_semestre = session("semestre_actual_id");
        $ap_now = $user->getAp();

        $estQuery = Persona::whereHas("asignacion_persona", function (
            $query,
        ) use ($id_semestre, $ap_now, $request) {
            $query->where("id_semestre", $id_semestre);
            $query->where("id_rol", 5);

            if ($ap_now->id_rol == 3) {
                $query->where("id_sa", $ap_now->id_sa);
            }

            if ($ap_now->id_rol == 2) {
                $query->whereHas("seccion_academica", function ($q) use (
                    $ap_now,
                ) {
                    $q->where(
                        "id_facultad",
                        $ap_now->seccion_academica->id_facultad,
                    );
                });
            }

            if (
                $request->filled("facultad") ||
                $request->filled("escuela") ||
                $request->filled("seccion")
            ) {
                $query->whereHas("seccion_academica", function ($q) use (
                    $request,
                ) {
                    if ($request->filled("facultad")) {
                        $q->where("id_facultad", $request->facultad);
                    }
                    if ($request->filled("escuela")) {
                        $q->where("id_escuela", $request->escuela);
                    }
                    if ($request->filled("seccion")) {
                        $q->where("id", $request->seccion);
                    }
                });
            }
        });

        $estudiantes = $estQuery
            ->with([
                "asignacion_persona" => function ($q) use ($id_semestre) {
                    $q->where("id_semestre", $id_semestre);
                },
                "asignacion_persona.matricula.archivos" => function ($query) {
                    $query
                        ->select(
                            "id",
                            "archivo_id",
                            "tipo",
                            "estado_archivo",
                            "created_at",
                        )
                        ->orderBy("created_at", "desc");
                },
                "asignacion_persona.seccion_academica.escuela",
                "asignacion_persona.semestre",
            ])
            ->orderBy("apellidos", "asc")
            ->get();

        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where("id", $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        // Mapeo Estándar para Vista Unificada
        $rows = $estudiantes->map(function ($user) {
            $ap = $user->asignacion_persona;
            $mat = $ap->matricula->first();
            $files = $mat ? $mat->archivos : collect();

            $getEstado = function ($tipo) use ($files, $mat, $user) {
                $f = $files
                    ->where("tipo", $tipo)
                    ->sortByDesc("created_at")
                    ->first();
                return [
                    "exists" => (bool) $f,
                    "estado" => $f ? $f->estado_archivo : "Falta",
                    "id" => $mat->id ?? null,
                    "type" => $tipo,
                    "name" => $user->apellidos . " " . $user->nombres,
                ];
            };

            return [
                "id" => $ap->id, // Fallback ID
                "unique_id" => $ap->id,
                "people" => $user->apellidos . ", " . $user->nombres,
                "avatar" =>
                    substr($user->nombres, 0, 1) .
                    substr($user->apellidos, 0, 1),
                "semestre" => $ap->semestre->codigo ?? "",
                "escuela" => $ap->seccion_academica->escuela->name ?? "",
                "facultad" =>
                    $ap->seccion_academica->escuela->facultad->name ?? "",
                "seccion" => $ap->seccion_academica->seccion ?? "",

                // Columnas dinámicas
                "archivos" => [
                    "ficha" => $getEstado("ficha"),
                    "record" => $getEstado("record"),
                ],
            ];
        });

        $config = [
            "view_title" => "Validación de Matrículas",
            "view_subtitle" =>
                "Gestionar documentos de matrícula del estudiante",
            "msj_button" => null, // Si no hay botón de registro
            "route_button" => null,
            "api_endpoint" => "/api/matricula", // Standard endpoint without trailing slash
            "form_action" => route("actualizar.estado.archivo.mat"),
            "columns" => [
                [
                    "key" => "ficha",
                    "label" => "Ficha M.",
                    "icon" => "bi-file-earmark-person",
                ],
                [
                    "key" => "record",
                    "label" => "Récord A.",
                    "icon" => "bi-journal-check",
                ],
            ],
        ];

        return view(
            "acreditacion.validacionDocente",
            compact("facultades", "rows", "config"),
        );
    }

    public function getMatricula($id)
    {
        $matricula = Matricula::findOrFail($id)->with("archivos")->get();
        return $matricula;
    }

    public function getArchivosPorTipo($id_ap, $tipo)
    {
        try {
            $mat = Matricula::where("id_ap", $id_ap)->first();
            $archivos = Archivo::where("archivo_type", Matricula::class)
                ->where("archivo_id", $mat->id)
                ->where("tipo", $tipo)
                ->latest()
                ->get();

            $archivos->transform(function ($archivo) {
                $relativePath = str_replace(url("/"), "", $archivo->ruta);
                $relativePath = ltrim($relativePath, "/");

                $fullPath = public_path($relativePath);

                $archivo->peso =
                    file_exists($fullPath) && !is_dir($fullPath)
                        ? $this->formatBytes(filesize($fullPath))
                        : "N/A";
                $archivo->extension = strtoupper(
                    pathinfo($archivo->ruta, PATHINFO_EXTENSION),
                );
                return $archivo;
            });

            return response()->json($archivos);
        } catch (\Exception $e) {
            Log::error(
                "Error obteniendo documentos de acreditación: " .
                    $e->getMessage(),
            );
            return response()->json(
                ["error" => "Error interno al obtener documentos"],
                500,
            );
        }
    }

    public function actualizarEstadoFicha(Request $request, $id)
    {
        $matricula = Matricula::findOrFail($id);
        $matricula->estado_ficha = $request->estado_ficha;
        $matricula->save();

        return back()->with(
            "success",
            "Estado de ficha actualizado correctamente",
        );
    }

    public function actualizarEstadoRecord(Request $request, $id)
    {
        $matricula = Matricula::findOrFail($id);
        $matricula->estado_record = $request->estado_record;
        $matricula->save();

        return back()->with(
            "success",
            "Estado de récord académico actualizado correctamente",
        );
    }

    function formatBytes($bytes, $precision = 2)
    {
        $units = ["B", "KB", "MB", "GB", "TB"];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Calculate bytes
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . " " . $units[$pow];
    }
}

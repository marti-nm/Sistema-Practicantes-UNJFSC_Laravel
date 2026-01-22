<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Facultad;
use App\Models\grupo_estudiante;
use App\Models\grupo_practica;
use App\Models\asignacion_persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class supervisorDashboardController extends Controller
{
    public function indexsupervisor(Request $request)
    {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();
        $ap_now = $authUser->persona->asignacion_persona;
        
        $facultades = Facultad::all();
        $escuelas = collect();
        $semestres = collect();

        $facultadId = $request->facultad_id;
        $escuelaId = $request->escuela_id;
        $semestreCodigo = $request->semestre_codigo;

        if ($facultadId) {
            $escuelas = Escuela::where('facultad_id', $facultadId)->get();
        }

        if ($escuelaId) {
            $semestres = DB::table('grupo_practica')
                ->where('id_escuela', $escuelaId)
                ->join('semestres', 'grupo_practica.id_semestre', '=', 'semestres.id')
                ->select('semestres.codigo')
                ->distinct()
                ->get();
        }

        // Obtener grupos del supervisor
        $grupos = grupo_practica::with(['modulo', 'seccion_academica.escuela'])
            ->where('id_supervisor', $ap_now ? $ap_now->id : null)
            ->where('id_sa', $ap_now->id_sa)
            ->get();

        $ids_grupos = $grupos->pluck('id');

        // Alumnos supervisados
        $alumnos = asignacion_persona::with(['persona', 'evaluaciones', 'practicas.empresa', 'evaluacion_practica.modulo'])
            ->whereHas('grupo_estudiante', function($q) use ($ids_grupos) {
                $q->whereIn('id_gp', $ids_grupos);
            })
            ->get()
            ->map(function($ap) {
                $eval = $ap->evaluaciones->first();
                $practica = $ap->practicas->first();
                $evalPractica = $ap->evaluacion_practica->sortByDesc('id_modulo')->first();
                
                return [
                    'id' => $ap->id,
                    'nombres' => $ap->persona->nombres,
                    'apellidos' => $ap->persona->apellidos,
                    'foto' => $ap->persona->ruta_foto,
                    'empresa' => $practica->empresa->razon_social ?? 'No registrada',
                    'modulo' => $evalPractica->modulo->name ?? 'Módulo 1',
                    'anexo_7' => ($eval && $eval->anexo_7) ? 'Completado' : 'Pendiente',
                    'anexo_8' => ($eval && $eval->anexo_8) ? 'Completado' : 'Pendiente',
                    'anexo_7_pdf' => $eval->anexo_7 ?? null,
                    'anexo_8_pdf' => $eval->anexo_8 ?? null,
                    'anexo_6_pdf' => $eval->anexo_6 ?? null,
                ];
            });

        $totalEstudiantes = $alumnos->count();
        $totalAnexo7 = $alumnos->where('anexo_7', 'Completado')->count();
        $totalAnexo8 = $alumnos->where('anexo_8', 'Completado')->count();
        
        // Módulo actual (promedio o el más común de los grupos)
        $currentModule = $grupos->first()->modulo->name ?? 'N/A';
        
        // Progreso general (anexo 7 y 8 completados)
        $totalItems = $totalEstudiantes * 2; // 2 anexos clave: 7 y 8
        $totalCompletados = $totalAnexo7 + $totalAnexo8;
        $progressGeneral = $totalItems > 0 ? round(($totalCompletados / $totalItems) * 100) : 0;

        return view('dashboard.dashboardSupervisor', compact(
            'facultades', 'escuelas', 'semestres',
            'facultadId', 'escuelaId', 'semestreCodigo', 
            'alumnos', 'totalEstudiantes', 'totalAnexo7', 'totalAnexo8',
            'currentModule', 'progressGeneral', 'grupos'
        ));
    }



public function obtenerSemestresPorEscuela($escuelaId)
{
    $semestres = DB::table('grupos_practicas')
        ->where('id_escuela', $escuelaId)
        ->join('semestres', 'grupos_practicas.id_semestre', '=', 'semestres.id')
        ->select('semestres.id', 'semestres.codigo')
        ->distinct()
        ->get();

    return response()->json($semestres);
}


}

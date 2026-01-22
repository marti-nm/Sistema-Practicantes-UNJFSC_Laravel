<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\asignacion_persona;
use App\Models\Escuela;
use App\Models\Matricula;
use App\Models\Archivo;
use App\Models\grupo_estudiante;
use App\Models\grupo_practica;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class DashboardDocenteController extends Controller
{
    public function index()
    {
        
    $id_semestre = session('semestre_actual_id');
    $authUser = auth()->user();

    $ap_now = $authUser->persona->asignacion_persona;
    
    $facultades = DB::table('facultades')->get();

    
    $id_escuela = $ap_now->seccion_academica->id_escuela;

    $escuelaIdDocente = $id_escuela;
    
    $escuelas = Escuela::where('id', $id_escuela)
    ->select('id', 'name')
    ->get();

    Log::info('ID de la escuela del docente: ' . $id_escuela);


    // Base query filtrada por los valores seleccionados
    // 1. Inicia en asignacion_persona (que es la tabla principal del modelo)
    $baseQuery = asignacion_persona::where('asignacion_persona.id_semestre', $id_semestre)
        ->where('asignacion_persona.id_sa', $ap_now->id_sa)
        ->join('seccion_academica as sa', 'sa.id', '=', 'asignacion_persona.id_sa'); // Agrega el join a SA
        //->where('id_escuela', $id_escuela);

    /*if ($escuelaId) {
        $baseQuery->where('gp.id_escuela', $escuelaId);
    }

    if ($semestreCodigo) {
        $baseQuery->join('semestres as s', 'gp.id_semestre', '=', 's.id')
                  ->where('s.codigo', $semestreCodigo);
    }

    if ($supervisorId) {
        $baseQuery->where('ge.id_supervisor', $supervisorId);
    }*/

    $totalEstudiantes = (clone $baseQuery)->where('id_rol', 5)->count();
    $asignacionesIds = (clone $baseQuery)->pluck('asignacion_persona.id');

    $totalFichasValidadas = Matricula::whereIn('id_ap', $asignacionesIds)
            ->where('estado_matricula', 'Completo')
            ->count();
    $totalSupervisores = asignacion_persona::where('id_rol', 4)
                ->where('id_semestre', $id_semestre)
                ->count('id_persona');

    // Métricas de matrícula por estado
    $matriculaStats = Matricula::whereIn('id_ap', $asignacionesIds)
        ->select('estado_matricula', DB::raw('COUNT(*) as total'))
        ->groupBy('estado_matricula')
        ->get()
        ->pluck('total', 'estado_matricula');

    $totalMatriculados = $matriculaStats->get('Completo', 0);
    $totalNoMatriculados = $asignacionesIds->count() - $totalMatriculados;

    // 2. Clona y añade los joins faltantes y selecciona
    $estudiantesPorEscuela = (clone $baseQuery)
        ->join('escuelas as e', 'sa.id_escuela', '=', 'e.id') // 'sa' ya está definida en el join anterior
        ->select('e.name as escuela', DB::raw('COUNT(asignacion_persona.id) as total'))
        ->groupBy('e.name')
        ->get();

    $estadoFichas = (clone $baseQuery)
        ->join('matriculas as m', 'asignacion_persona.id', '=', 'm.id_ap')
        ->select('m.estado_matricula', DB::raw('COUNT(*) as total'))
        ->groupBy('m.estado_matricula')
        ->get();

    // Grupos de práctica con información de módulo y supervisor
    $groupsData = grupo_practica::with(['supervisor.persona', 'seccion_academica.escuela', 'modulo'])
        ->whereHas('seccion_academica', function($q) use ($id_escuela) {
            $q->where('id_escuela', $id_escuela);
        })
        ->withCount('grupo_estudiante')
        ->get()
        ->map(function($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'school' => $group->seccion_academica->escuela->name ?? 'N/A',
                'supervisor' => ($group->supervisor && $group->supervisor->persona) 
                    ? $group->supervisor->persona->nombres . ' ' . $group->supervisor->persona->apellidos 
                    : 'Sin asignar',
                'students' => $group->grupo_estudiante_count,
                'modulo' => $group->modulo->name ?? 'Módulo 1',
                'modulo_numero' => $group->id_modulo ?? 1,
                'status' => $group->state == 1 ? 'Activo' : 'Inactivo'
            ];
        });


    $fichasPorMes = Archivo::select(
            DB::raw('MONTHNAME(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
        ->where('archivo_type', 'ficha')
        ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
        ->orderBy(DB::raw('MONTH(created_at)'))
        ->get();

    $supervisoresRanking = (clone $baseQuery)
        ->join('personas as p', 'asignacion_persona.id_persona', '=', 'p.id')
        ->select('p.nombres', DB::raw('COUNT(asignacion_persona.id) as total'))
        ->groupBy('p.nombres')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

        /*$chartData = (clone $baseQuery)
        ->join('personas as p', 'asignacion_persona.id_persona', '=', 'p.id')
        ->join('grupos_practicas as gp', 'p.id', '=', 'gp.id_docente')
        ->join('grupo_estudiante as ge', 'gp.id', '=', 'ge.id_grupo_practica')
        ->join('escuelas as e', 'gp.id_escuela', '=', 'e.id')
        ->join('grupo_estudiante as sup', 'ge.id_supervisor', '=', 'sup.id')
        ->join('users as usup', 'sup.id', '=', 'usup.id')
        ->select(
            'gp.nombre_grupo as grupo',
            'usup.name as supervisor',
            DB::raw('COUNT(ge.id_estudiante) as total')
        )
        ->groupBy('gp.nombre_grupo', 'usup.name')
        ->get();*/
    $chartData = grupo_practica::
    select(
        'name as grupo',
        'id_docente as supervisor', // Columna no agregada
        DB::raw('COUNT(id) as total')
    )
    ->groupBy('name', 'id_docente') // ¡CORRECCIÓN: Incluir id_docente aquí!
    ->get();

    $listaEstudiantes = Matricula::whereHas('asignacion_persona', function ($query) use ($baseQuery) {
            // Especificamos 'asignacion_persona.id' para resolver la ambigüedad
            $query->whereIn('id', (clone $baseQuery)->pluck('asignacion_persona.id')); 
        })->with('asignacion_persona.persona', 'asignacion_persona.semestre', 'asignacion_persona.seccion_academica.escuela', 'asignacion_persona.seccion_academica.facultad')->get();



    return view('dashboard.dashboardDocente', compact(
        'totalEstudiantes', 'totalFichasValidadas', 'totalSupervisores',
        'totalMatriculados', 'totalNoMatriculados', 'matriculaStats',
        'estudiantesPorEscuela', 'estadoFichas', 'groupsData', 'fichasPorMes',
        'supervisoresRanking', 'escuelas','chartData','listaEstudiantes', 'facultades', 'escuelaIdDocente'
    ));

    }

public function getSemestres($escuelaId)
{
    $semestres = DB::table('grupos_practicas as gp')
        ->join('semestres as s', 'gp.id_semestre', '=', 's.id')
        ->where('gp.id_escuela', $escuelaId)
        ->select('s.codigo')
        ->distinct()
        ->get();

    return response()->json($semestres); // <<< AÑADIDO
}

public function getSupervisores($escuelaId)
{
    $supervisores = DB::table('grupo_estudiante as ge')
        ->join('grupos_practicas as gp', 'ge.id_grupo_practica', '=', 'gp.id')
        ->join('personas as p', 'ge.id_supervisor', '=', 'p.id')
        ->where('gp.id_escuela', $escuelaId)
        ->select('p.id', 'p.nombres')
        ->distinct()
        ->get();

    return response()->json($supervisores); // <<< AÑADIDO
}

}

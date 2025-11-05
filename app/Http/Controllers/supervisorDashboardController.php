<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Facultad;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class supervisorDashboardController extends Controller
{
    public function indexsupervisor(Request $request)
{
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
        $semestres = DB::table('grupos_practicas')
            ->where('id_escuela', $escuelaId)
            ->join('semestres', 'grupos_practicas.id_semestre', '=', 'semestres.id')
            ->select('semestres.codigo')
            ->distinct()
            ->get();
    }

    $supervisorId = auth()->user()->id;

    $baseQuery = DB::table('grupo_estudiante as ge')
        ->join('grupos_practicas as gp', 'ge.id_grupo_practica', '=', 'gp.id')
        ->join('personas as p', 'ge.id_estudiante', '=', 'p.id')
        ->join('escuelas as e', 'gp.id_escuela', '=', 'e.id')
        ->leftJoin('evaluaciones as ev', 'ge.id_estudiante', '=', 'ev.alumno_id')
        ->join('facultades as f', 'e.facultad_id', '=', 'f.id')
        ->join('semestres as s', 'gp.id_semestre', '=', 's.id')
        ->where('ge.id_supervisor', $supervisorId);

    if ($facultadId) {
        $baseQuery->where('f.id', $facultadId);
    }

    if ($escuelaId) {
        $baseQuery->where('e.id', $escuelaId);
    }

    if ($semestreCodigo) {
        $baseQuery->where('s.codigo', $semestreCodigo);
    }

    $alumnos = $baseQuery
    ->select(
        'p.nombres',
        'p.apellidos',
        'e.name as escuela',
        'ev.anexo_6',
        'ev.anexo_7',
        'ev.anexo_8',
        DB::raw("CASE WHEN ev.anexo_7 IS NOT NULL AND ev.anexo_7 != '' THEN 'Registrado' ELSE 'Sin registrar' END as estado_anexo_7"),
        DB::raw("CASE WHEN ev.anexo_8 IS NOT NULL AND ev.anexo_8 != '' THEN 'Registrado' ELSE 'Sin registrar' END as estado_anexo_8")
    )
    ->get();


    $totalFiltrados = $alumnos->count();

    $totalCompletos = $alumnos->filter(function ($alumno) {
        return  $alumno->estado_anexo_7 === 'Registrado'
            && $alumno->estado_anexo_8 === 'Registrado';
    })->count();

        return view('dashboard.dashboardSupervisor', compact(
            'facultades', 'escuelas', 'semestres',
            'facultadId', 'escuelaId', 'semestreCodigo', 'alumnos','totalFiltrados','totalCompletos'
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

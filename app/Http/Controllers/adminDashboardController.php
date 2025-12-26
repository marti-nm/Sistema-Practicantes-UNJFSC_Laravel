<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\asignacion_persona;
use App\Models\Matricula;
use App\Models\Semestre;
use App\Models\Facultad;
use App\Models\Escuela;
use App\Models\seccion_academica;
use App\Models\Archivo;
use Illuminate\Support\Facades\Log;

class adminDashboardController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $authUser = auth()->user();
        $userRolId = $authUser->getRolId();
        
        // Usar la asignación del semestre activo. ¡Esto debe ser implementado!
        // $asignacionActual = $authUser->getAsignacionActual();
        $asignacionActual = $authUser->persona->asignacion_persona; // Manteniendo la lógica original por ahora
        $idFacultadSubAdmin = ($userRolId == 2) ? $asignacionActual->seccion_academica->facultad->id : null;
        $semestreActivoId = session('semestre_actual_id');

        // Cargar facultades para el filtro
        $facultadesQuery = Facultad::query();
        if ($userRolId == 2) {
            $facultadesQuery->where('id', $idFacultadSubAdmin);
        }
        $facultades = $facultadesQuery->get();

        $escuelas = Escuela::all();
        $semestres = Semestre::all();

        $secciones = seccion_academica::where('id_semestre', $semestreActivoId)
            ->where('id_escuela', 1)
            ->get();

        // Consulta base con Eloquent
        $baseAsignacionQuery = asignacion_persona::where('id_semestre', $semestreActivoId);
            //->where('id_rol', 5); // Rol de Estudiante

        if ($userRolId == 2) {
            $baseAsignacionQuery->whereHas('seccion_academica', function ($q) use ($idFacultadSubAdmin) {
                $q->where('id_facultad', $idFacultadSubAdmin);
            });
        }

        if ($request->filled('facultad')) {
            $baseAsignacionQuery->whereHas('seccion_academica', function ($q) use ($request) {
                $q->where('id_facultad', $request->facultad);
            });
        }

        if ($request->filled('escuela')) {
            $baseAsignacionQuery->whereHas('seccion_academica', function ($q) use ($request) {
                $q->where('id_escuela', $request->escuela);
            });
        }

        if ($request->filled('seccion')) {
            $baseAsignacionQuery->where('id_sa', $request->seccion);
        }

        // Lista de estudiantes, su matricula y su estado, Juntar asignacion_persona con Matricula -> Archivo
        $listaEstudiantes = Matricula::whereHas('asignacion_persona', function ($query) use ($baseAsignacionQuery) {
            // Clonamos la consulta base para no afectarla
            $query->whereIn('id', (clone $baseAsignacionQuery)->pluck('id'));
        })->with('asignacion_persona.persona', 'asignacion_persona.semestre', 'asignacion_persona.seccion_academica.escuela', 'asignacion_persona.seccion_academica.facultad')->get();

        // --- Recálculo de Métricas con Eloquent ---

        // Total de estudiantes según filtros
        $totalEstudiantes = (clone $baseAsignacionQuery)->where('id_rol', 5)->count();

        // IDs de las asignaciones filtradas
        $asignacionesIds = (clone $baseAsignacionQuery)->pluck('id');

        // Total matriculados (que tienen registro en Matricula)
        $totalMatriculados = Matricula::whereIn('id_ap', $asignacionesIds)->count();

        // Fichas completas
        $completos = Matricula::whereIn('id_ap', $asignacionesIds)
            ->where('estado_matricula', 'Completo')
            ->count();

        // Supervisores del semestre activo (no se ve afectado por filtros de facultad/escuela de estudiantes)
        $totalSupervisores = asignacion_persona::where('id_rol', 4)
                ->where('id_semestre', $semestreActivoId)
                ->count('id_persona');
        
        $totalSupervisores = (clone $baseAsignacionQuery)->where('id_rol', 4)->count();

        // Pendientes

        $pendientes = $totalMatriculados - $completos;

        $totalPorEscuelaEnSemestre = $totalEstudiantes; // Ya está filtrado

        // Gráfico de Fichas por Escuela
        $fichasPorEscuela = Escuela::select('escuelas.name as escuela', DB::raw('count(matriculas.id) as total'))
            // 1. Unir Escuela con Seccion_Academica (sa)
            // (La tabla 'escuelas' ya está implícita al iniciar la consulta)
            ->join('seccion_academica as sa', 'sa.id_escuela', '=', 'escuelas.id')
            
            // 2. Unir Seccion_Academica (sa) con Asignacion_Persona (ap)
            ->join('asignacion_persona as ap', 'ap.id_sa', '=', 'sa.id')
            
            // 3. Unir Asignacion_Persona (ap) con Matriculas
            ->join('matriculas', 'ap.id', '=', 'matriculas.id_ap')
            
            ->where('ap.id_semestre', $semestreActivoId)
            ->groupBy('escuelas.id', 'escuelas.name', 'matriculas.estado_matricula')
            ->get();
        
        // buscar en Archivos y contar las fichas por mes, agrupar por archivo y solo del tipo ficha
        $fichasPorMes = Archivo::select(
            DB::raw('MONTHNAME(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
        ->where('archivo_type', 'ficha')
        ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
        ->orderBy(DB::raw('MONTH(created_at)'))
        ->get();


        return view('dashboard.dashboardAdmin', compact(
            'facultades', 'escuelas', 'semestres', 'secciones',
            'totalMatriculados', 'totalSupervisores',
            'completos', 'pendientes', 'totalPorEscuelaEnSemestre',
            'totalEstudiantes', 'listaEstudiantes','fichasPorEscuela','fichasPorMes'
        )); 
    }
}
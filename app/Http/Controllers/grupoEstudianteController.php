<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Facultad;
use App\Models\grupo_estudiante;
use App\Models\grupo_practica;
use App\Models\EvaluacionPractica;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\SincronizaGrupoTrait;



class grupoEstudianteController extends Controller
{
    use SincronizaGrupoTrait;

    public function index(Request $request)
    {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();
        $ap_now = $authUser->persona->asignacion_persona;

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

        $gp = $gpQuery->with([
            'docente.persona',
            'supervisor.persona'
        ])->get();

        $facQuery = Facultad::query();
        if ($ap_now->id_rol == 2) {
            $facQuery->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        return view('asignatura.grupoAsignatura', compact(
            'gp',
            'facultades'
        ));
    }

    public function asignarAlumnos(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupo_practica,id',
            'estudiantes' => 'required|array',
            'estudiantes.*' => 'exists:asignacion_persona,id', // Validar que cada ID de estudiante exista en asignacion_persona
        ]);

        $grupoId = $request->grupo_id;
        $estudiantesIds = $request->estudiantes;
        $asignadosCount = 0;

        foreach ($estudiantesIds as $estudianteApId) {
            // Usamos firstOrCreate para evitar duplicados.
            // Si la combinación de grupo y estudiante ya existe, no hace nada.
            // Si no existe, la crea.
            $asignacion = grupo_estudiante::firstOrCreate([
                'id_gp' => $grupoId,
                'id_estudiante' => $estudianteApId, // El ID del estudiante es el ID de asignacion_persona
            ]);

            // Si el estudiante fue recién asignado, creamos sus 4 registros de evaluación.
            if ($asignacion->wasRecentlyCreated) {
                $asignadosCount++;
                // Crear los 4 módulos en estado pendiente para el estudiante.
                for ($i = 1; $i <= 4; $i++) {
                    EvaluacionPractica::firstOrCreate(
                        ['id_ap' => $estudianteApId, 'id_modulo' => $i],
                        ['state' => 0, 'estado_evaluacion' => 'Pendiente']
                    );
                }
            }
        }
        Log::info('Se asignaron ' . $asignadosCount . ' estudiantes al grupo ' . $grupoId);

        // Sincronizar el módulo del grupo basado en el progreso de sus integrantes
        try {
            $this->sincronizarModuloGrupo($grupoId);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al sincronizar el módulo del grupo: ' . $e->getMessage());
        }


        return redirect()->back()->with('success', "Se asignaron {$asignadosCount} nuevos estudiantes al grupo correctamente.");
    }

public function destroy($id)
{
    try {
        $registro = grupo_estudiante::findOrFail($id);
        $grupoId = $registro->id_gp;
        $registro->delete();

        // Sincronizar el módulo del grupo tras eliminar a un estudiante
        $this->sincronizarModuloGrupo($grupoId);
    } catch (\Exception $e) {

        return back()->with('error', 'Error al eliminar el estudiante del grupo: ' . $e->getMessage());
    }

    return back()->with('success', 'Estudiante eliminado del grupo correctamente.');
}


} 

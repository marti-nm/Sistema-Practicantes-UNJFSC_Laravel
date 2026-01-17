<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Facultad;
use App\Models\grupo_practica;
use App\Models\grupo_estudiante;
use App\Models\Persona;
use App\Models\asignacion_persona;
use App\Models\Semestre;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;

class AsignacionController extends Controller
{
    //as 
    public function index(Request $request){
        $user = auth()->user();
        $userRolId = $user->getRolId();
        $id_semestre = session('semestre_actual_id');
        $ap = $user->persona->asignacion_persona()->first();

        $facQuery = Facultad::query();
        if ($userRolId == 2) {
            $facQuery->where('id', $ap->seccion_academica->id_facultad);
        }
        
        $facultades = $facQuery->get();

        $gpQuery = grupo_practica::whereHas('seccion_academica', function($query) use ($request, $ap, $id_semestre){
            $query->where('id_semestre', $id_semestre);
            if($request->filled('facultad')){
                $query->where('id_facultad', ($ap->id_rol == 2) ? $ap->seccion_academica->id_facultad : $request->facultad);
            }
            if($request->filled('escuela')){
                $query->where('id_escuela', $request->escuela);
            }
            if($request->filled('seccion')){
                $query->where('id', $request->seccion);
            }
        });
        
        if($ap->id_rol == 3) {
            $gpQuery->where('id_sa', $ap->id_sa);
        }

        $grupos_practica = $gpQuery->with([
            'seccion_academica.facultad',
            'seccion_academica.escuela',
            'seccion_academica.semestre',
            'docente.persona',
            'supervisor.persona'
        ])
        ->get();

        if($grupos_practica->isEmpty()) {
            $grupos_practica = collect();
        }

        return view('asignatura.asignatura', compact('ap', 'facultades', 'grupos_practica'));
    } 

    public function store(Request $request)
    {
        $request->validate([
            'dtitular' => 'required|exists:asignacion_persona,id',
            'dsupervisor' => 'required|exists:asignacion_persona,id',
            'seccion' => 'required|exists:seccion_academica,id',
            'nombre_grupo' => 'required|string|max:50'
        ]);

        Log::info('Request ALL: '.json_encode($request->all()));


        $existe = grupo_practica::where('id_docente', $request->dtitular)
            ->where('id_supervisor', $request->dsupervisor)
            ->where('id_sa', $request->seccion)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Ya existe un grupo con el mismo semestre y escuela.');
        }

        // Crear el grupo si no existe
        grupo_practica::create([
            'name' => $request->nombre_grupo,
            'id_docente' => $request->dtitular,
            'id_supervisor' => $request->dsupervisor,
            'id_sa' => $request->seccion,
            'state' => true,
        ]);

        return redirect()->back()->with('success', 'Grupo de práctica registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Validar solo los campos que se permiten editar. 'dsupervisor' => 'required|exists:asignacion_persona,id',
        $request->validate([
            'nombre_grupo' => 'required|string|max:50',
            
        ]);

        // Actualizar solo el nombre y el supervisor. Si dsupervisor no se envia, no se actualiza.
        $grupo = grupo_practica::where('id', $id)->first();
        $grupo->name = $request->nombre_grupo;
        if ($request->filled('dsupervisor')) {
            $grupo->id_supervisor = $request->dsupervisor;
        }
        $grupo->save();

        return redirect()->back()->with('success', 'Grupo actualizado correctamente.');
    }

    public function eliminar($id)
    { 
        try {
            grupo_practica::destroy($id);
        return redirect()->back()->with('success', 'Grupo eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar el grupo. Puede que tenga estudiantes asignados.');
        }
        
    }

    public function getGrupo($id) {
        $grupo = grupo_practica::with([
            'seccion_academica.facultad',
            'seccion_academica.escuela',
            'seccion_academica.semestre',
            'docente.persona',
            'supervisor.persona'
        ])->findOrFail($id);
        return response()->json($grupo);
    }
}

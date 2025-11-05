<?php

namespace App\Http\Controllers;

use App\Models\Evaluacione;
use App\Models\grupo_estudiante;
use App\Models\Pregunta;
use App\Models\Persona;
use App\Models\asignacion_persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluacionController extends Controller
{
    /**
     * Mostrar evaluación para estudiantes asignados al docente o supervisor.
     */
    public function index(Request $request)
{
    $user = auth()->user();
    $userId = $user->id;
    $userRol = $user->getRolId();

    if ($userRol == 1) {
        // Si es admin: ver todos los estudiantes asignados en algún grupo
        $grupoEstudiantes = grupo_estudiante::with([
                'estudiante.asignacion_persona.escuela',
                'estudiante.evaluacione',
                'estudiante.respuestas.pregunta',
                'grupo.escuela',
                'grupo.docente'
            ])->get();

        $alumnos = $grupoEstudiantes->pluck('estudiante')->unique('id')->values();
    } else {
        // Docente o supervisor: solo sus asignados
        $grupoEstudiantes = grupo_estudiante::with([
                'estudiante.asignacion_persona.escuela',
                'estudiante.evaluacione',
                'estudiante.respuestas.pregunta',
                'grupo.escuela',
                'grupo.docente'
            ])
            ->where(function ($q) use ($userId) {
                $q->where('id_supervisor', $userId)
                  ->orWhereHas('grupo', function ($g) use ($userId) {
                      $g->where('id_docente', $userId);
                  });
            })
            ->get();

        $alumnos = $grupoEstudiantes->pluck('estudiante')->unique('id')->values();
    }

    // Búsqueda por nombre
    if ($request->filled('buscar')) {
        $alumnos = $alumnos->filter(function ($alumno) use ($request) {
            return stripos($alumno->nombres, $request->buscar) !== false ||
                   stripos($alumno->apellidos, $request->buscar) !== false;
        })->values();
    }

    // Preguntas según usuario (solo para docentes/supervisores)
    $preguntas = Pregunta::where('user_create', $userId)
                        ->where('estado', true)
                        ->orderBy('id')
                        ->get();

    return view('evaluacion.index', compact('alumnos', 'preguntas'));
}


    /**
     * Guardar o actualizar anexos (6, 7 y 8).
     */
    public function storeAnexos(Request $request)
{
    $request->validate([
        'alumno_id' => 'required|exists:personas,id',
        'anexo_6'   => 'nullable|file|mimes:pdf',
        'anexo_7'   => 'nullable|file|mimes:pdf',
        'anexo_8'   => 'nullable|file|mimes:pdf',
    ]);

    // Buscar al alumno para nombrar los archivos
    $alumno = \App\Models\Persona::findOrFail($request->alumno_id); // Asegúrate de importar el modelo correctamente

    $evaluacion = Evaluacione::firstOrNew(['alumno_id' => $alumno->id]);

    // Función para generar nombres personalizados
    $nombreBase = str_replace(' ', '_', $alumno->nombres . '_' . $alumno->apellidos);

    if ($request->hasFile('anexo_6')) {
        if ($evaluacion->anexo_6) {
            Storage::disk('public')->delete($evaluacion->anexo_6);
        }
        $nombreArchivo = 'anexo_6_' . $nombreBase . '.' . $request->file('anexo_6')->extension();
        $evaluacion->anexo_6 = $request->file('anexo_6')->storeAs('anexos', $nombreArchivo, 'public');
    }

    if ($request->hasFile('anexo_7')) {
        if ($evaluacion->anexo_7) {
            Storage::disk('public')->delete($evaluacion->anexo_7);
        }
        $nombreArchivo = 'anexo_7_' . $nombreBase . '.' . $request->file('anexo_7')->extension();
        $evaluacion->anexo_7 = $request->file('anexo_7')->storeAs('anexos', $nombreArchivo, 'public');
    }

    if ($request->hasFile('anexo_8')) {
        if ($evaluacion->anexo_8) {
            Storage::disk('public')->delete($evaluacion->anexo_8);
        }
        $nombreArchivo = 'anexo_8_' . $nombreBase . '.' . $request->file('anexo_8')->extension();
        $evaluacion->anexo_8 = $request->file('anexo_8')->storeAs('anexos', $nombreArchivo, 'public');
    }

    if (!$evaluacion->exists) {
        $evaluacion->user_create = auth()->user()->name ?? 'admin';
        $evaluacion->date_create = now();
        $evaluacion->estado = true;
    } else {
        $evaluacion->user_update = auth()->user()->name ?? 'admin';
        $evaluacion->date_update = now();
    }

    $evaluacion->save();

    return redirect()->route('evaluacion.index', ['open' => $alumno->id])
                     ->with('success', 'Anexos guardados correctamente.');
}


}

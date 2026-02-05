<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JefeInmediato;
use App\Models\Practica;
use App\Models\grupos_practica;
use App\Models\grupo_estudiante;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JefeInmediatoController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $rol = $user->getRolId();
        $persona = $user;
        if($rol == 1){
            $jefes = JefeInmediato::all();
        }else if($rol == 3){
            $jefes = collect();
            $grupos_practicas = grupos_practica::where('id_docente', $persona->id)->get();
            foreach ($grupos_practicas as $grupo) {
                // Obtiene todos los estudiantes del grupo
                $grupo_estudiantes = grupo_estudiante::where('id_grupo_practica', $grupo->id)->get();
    
                foreach ($grupo_estudiantes as $ge) {
                    // Busca la práctica del estudiante
                    $practica = Practica::where('estudiante_id', $ge->id_estudiante)->first();
    
                    if ($practica) {
                        // Busca el jefe inmediato asociado a la práctica
                        $jefe = JefeInmediato::where('id_practica', $practica->id)->first();
    
                        if ($jefe && !$jefes->contains('id', $jefe->id)) {
                            // Agrega el jefe inmediato si no está ya en la colección
                            $jefes->push($jefe);
                        }
                    }
                }
            }
        } else if($rol == 4){
            $jefes = collect();
            $grupo_estudiantes = grupo_estudiante::where('id_supervisor', $persona)->get();
    
            foreach ($grupo_estudiantes as $ge) {
                // Busca la práctica del estudiante
                $practica = Practica::where('estudiante_id', $ge->id_estudiante)->first();

                if ($practica) {
                    // Busca el jefe inmediato asociado a la práctica
                    $jefe = JefeInmediato::where('id_practica', $practica->id)->first();

                    if ($jefe && !$jefes->contains('id', $jefe->id)) {
                        // Agrega el jefe inmediato si no está ya en la colección
                        $jefes->push($jefe);
                    }
                }
            }
        }
        return view('auxiliares.jefe_inmediato', compact('jefes'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request, $practicas_id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:8',
            'cargo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255'
        ]);

        JefeInmediato::create([
            'nombres' => $validated['name'],
            'dni' => $validated['dni'],
            'cargo' => $validated['cargo'],
            'area' => $validated['area'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['email'],
            'web' => $validated['sitio_web'] ?? null,
            'id_practica' => $practicas_id,
            'state' => 2,
        ]);

        return redirect()->back()->with('success', 'Jefe Inmediato registrado exitosamente');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $jefeInmediato = JefeInmediato::findOrFail($id);
        $practica = Practica::findOrFail($jefeInmediato->id_practica);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:8',
            'cargo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255'
        ]);

        $jefeInmediato->update([
            'nombres' => $validated['name'],
            'dni' => $validated['dni'],
            'cargo' => $validated['cargo'],
            'area' => $validated['area'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['email'],
            'web' => $validated['sitio_web'] ?? null,
            'state' => 2,
        ]);

        /*$practica->update([
            'estado_practica' => 'en proceso',
            'state' => 1,
        ]);*/

        return redirect()->back()->with('success', 'Jefe Inmediato actualizado exitosamente');
    }

    public function destroy($id)
    {
        //
    }

    // API: obtener jefe inmediato por id_practica (para fetch desde frontend)
    public function getJefeInmediato($practica)
    {
        $jefe = JefeInmediato::where('id_practica', $practica)->first();
        return response()->json($jefe);
    }

    public function actualizarEstadoJefeInmediato(Request $request) {
        Log::emergency('ENTRANDO A actualizarEstadoJefeInmediato - Request: '.json_encode($request->all()));
        try {
            $option = $request->option;
            if($option == 1) {
                $empresa = Empresa::findOrFail($request->id);
                $empresa->comentario = $request->comentario;
                $empresa->state = ($request->estado == 'Aprobado') ? 1 : 3;
                $empresa->save();
            } else if($option == 2) {
                $jefe = JefeInmediato::findOrFail($request->id);
                $jefe->comentario = $request->comentario;
                $jefe->state = ($request->estado == 'Aprobado') ? 1 : 3;
                $jefe->save();
            }

            if($request->estado == 'Aprobado') {
                $empresa = Empresa::where('id_practica', $jefe->id_practica)->first();
                
                if($empresa && $empresa->state == 1) {
                    $practica = Practica::findOrFail($jefe->id_practica);
                    $practica->state++;
                    $practica->save();
                    Log::info('Practica actualizada al siguiente estado: '.$practica->id);
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error al actualizar estado de jefe inmediato: '.$th->getMessage());
            return back()->with('error', 'Error al actualizar estado de jefe inmediato');
        }
        return back()->with('success', 'Jefe Inmediato actualizado exitosamente');
    }
}

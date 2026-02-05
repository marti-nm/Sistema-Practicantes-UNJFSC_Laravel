<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Practica;
use App\Models\grupos_practica;
use App\Models\grupo_estudiante;
use App\Models\JefeInmediato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $rol = $user->getRolId();
        $persona = $user;
        if($rol == 1 || $rol == 2){
            $empresas = Empresa::all();
        }else if($rol == 3){
            $empresas = collect();
            $grupos_practicas = grupos_practica::where('id_docente', $persona->id)->get();
            foreach ($grupos_practicas as $grupo) {
                // Obtiene todos los estudiantes del grupo
                $grupo_estudiantes = grupo_estudiante::where('id_grupo_practica', $grupo->id)->get();
    
                foreach ($grupo_estudiantes as $ge) {
                    // Busca la práctica del estudiante
                    $practica = Practica::where('estudiante_id', $ge->id_estudiante)->first();
    
                    if ($practica) {
                        // Busca la empresa asociada a la práctica
                        $empresa = Empresa::where('practicas_id', $practica->id)->first();
    
                        if ($empresa && !$empresas->contains('id', $empresa->id)) {
                            // Agrega la empresa si no está ya en la colección
                            $empresas->push($empresa);
                        }
                    }
                }
            }
        } else if($rol == 4){
            $empresas = collect();
            $grupo_estudiantes = grupo_estudiante::where('id_supervisor', $persona->id)->get();
    
            foreach ($grupo_estudiantes as $ge) {
                // Busca la práctica del estudiante
                $practica = Practica::where('estudiante_id', $ge->id_estudiante)->first();

                if ($practica) {
                    // Busca la empresa asociada a la práctica
                    $empresa = Empresa::where('practicas_id', $practica->id)->first();

                    if ($empresa && !$empresas->contains('id', $empresa->id)) {
                        // Agrega la empresa si no está ya en la colección
                        $empresas->push($empresa);
                    }
                }
            }
        }
        return view('auxiliares.empresa', compact('empresas'));
    }

    public function create($practicas_id)
    {
        //
    }

    public function store(Request $request, $practicas_id)
    {
        $validated = $request->validate([
            'empresa' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255'
        ]);

        Empresa::create([
            'nombre' => $validated['empresa'],
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['email'],
            'sitio_web' => $validated['sitio_web'] ?? null,
            'id_practica' => $practicas_id,
            'state' => 2,
        ]);

        return redirect()->back()->with('success', 'Empresa registrada exitosamente');
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
        $empresa = Empresa::findOrFail($id);
        $practica = Practica::findOrFail($empresa->id_practica);
        
        $validated = $request->validate([
            'empresa' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255'
        ]);

        Log::info('EMpresaa: '.$empresa);

        $empresa->update([
            'nombre' => $validated['empresa'],
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['email'],
            'sitio_web' => $validated['sitio_web'] ?? null,
            'state' => 2,
        ]);

        /*$practica->update([
            'estado_practica' => 'en proceso',
        ]);*/
        
        return redirect()->back()->with('success', 'Empresa actualizada exitosamente');
    }

    public function destroy($id)
    {
        //
    }

    public function getEmpresa($practica) {
        $empresa = Empresa::where('id_practica', $practica)
            //->select('id', 'state')
            ->first();
        return response()->json($empresa);
    }

    public function actualizarEstadoEmpresa(Request $request) {
        Log::info('Empresa: '.json_encode($request->all()));
        try {
            $empresa = Empresa::findOrFail($request->id);
            $empresa->comentario = $request->comentario;
            $empresa->state = ($request->estado == 'Aprobado') ? 1 : 3;
            $empresa->save();

            if($request->estado == 'Aprobado') {
                $jefe = JefeInmediato::where('id_practica', $empresa->id_practica)->first();
                
                $practica = Practica::findOrFail($empresa->id_practica);
                if($jefe && $jefe->state == 1 && $practica->state == 1) {
                    $practica->state++;
                    $practica->save();
                } else {
                    return back()->with('error', 'Error al actualizar estado de empresa');
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error al actualizar estado de empresa: '.$th->getMessage());
            return back()->with('error', 'Error al actualizar estado de empresa');
        }
        return back()->with('success', 'Empresa actualizada exitosamente');
    }
}

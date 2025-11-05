<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Practica;
use App\Models\grupos_practica;
use App\Models\grupo_estudiante;
use Illuminate\Support\Facades\Auth;

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
            'practicas_id' => $practicas_id,
            'estado' => 1,
            
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
        $practica = Practica::findOrFail($empresa->practicas_id);
        
        $validated = $request->validate([
            'empresa' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255'
        ]);

        $empresa->update([
            'nombre' => $validated['empresa'],
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['email'],
            'sitio_web' => $validated['sitio_web'] ?? null,
            'estado' => 1,
        ]);

        $practica->update([
            'estado_proceso' => 'en proceso',
        ]);

        return redirect()->back()->with('success', 'Empresa actualizada exitosamente');
    }

    public function destroy($id)
    {
        //
    }
}

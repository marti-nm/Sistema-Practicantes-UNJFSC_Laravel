<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use App\Models\JefeInmediato;
use App\Models\Practica;
use App\Models\Persona;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticaController extends Controller
{
    public function lst_supervision(){
        $personas = Persona::with([
                'practica.empresa', 
                'practica.jefeInmediato'
            ])
            ->whereHas('asignacion_persona', function ($query) {
                $query->where('id_rol', 5);
            })
            ->get();
            /*->whereHas('rol', function ($query) {
                $query->where('id', 4);
            })
            ->get();*/
    
            //dd($personas);
            
        return view('practicas.admin.supervision', compact('personas'));
    }

    public function show($id){
        $practica = Practica::with(['empresa', 'jefeInmediato'])->findOrFail($id);
        return response()->json($practica);
    }

    public function proceso(Request $request) {
        $id = $request->id;
        $nuevoEstado = $request->estado; // "aprobado" o "rechazado"
        
        $practica = Practica::findOrFail($id);
        
        // Verificar condiciones según el estado actual
        $cumpleCondiciones = false;
        
        switch ($practica->estado) {
            case 1:
                $cumpleCondiciones = $practica->empresa()->exists() && $practica->jefeInmediato()->exists();
                break;
            case 2:
                if ($practica->tipo_practica == 'desarrollo') {
                    $cumpleCondiciones = !is_null($practica->ruta_fut) && 
                                        !is_null($practica->ruta_carta_presentacion);
                } elseif ($practica->tipo_practica == 'convalidacion') {
                    $cumpleCondiciones = !is_null($practica->ruta_fut) && 
                                         !is_null($practica->ruta_carta_aceptacion);
                }
                break;
            case 3:
                if ($practica->tipo_practica == 'desarrollo') {
                    $cumpleCondiciones = !is_null($practica->ruta_carta_aceptacion) && 
                                         !is_null($practica->ruta_plan_actividades);
                } elseif ($practica->tipo_practica == 'convalidacion') {
                    $cumpleCondiciones = !is_null($practica->ruta_registro_actividades) && 
                                         !is_null($practica->ruta_control_actividades);
                }
                break;
            case 4:
                $cumpleCondiciones = !is_null($practica->ruta_constancia_cumplimiento) && !is_null($practica->ruta_informe_final);
                break;
            default:
                $cumpleCondiciones = false;
        }
        
        if (!$cumpleCondiciones && $nuevoEstado === 'aprobado') {
            $mensajeError = 'No se puede aprobar: ';
            if ($practica->estado == 1) {
                $mensajeError .= 'debe registrar la empresa y jefe inmediato primero.';
            } else {
                $mensajeError .= 'faltan documentos requeridos para este estado.';
            }
            return back()->with('error', $mensajeError);
        }        
        
        if ($nuevoEstado === 'aprobado') {
            $practica->estado += 1;
            //$practica->estado_proceso = 'completo';
        } elseif ($nuevoEstado === 'rechazado') {
            $practica->estado_proceso = 'rechazado';
            if ($request->test) {
                $empresa = Empresa::where('practicas_id', $id)->first();
                $empresa->update([
                    'estado' => 2,
                ]);
                
                $jefeInmediato = JefeInmediato::where('practicas_id', $id)->first();
                $jefeInmediato->update([
                    'estado' => 2,
                ]);
            }
        }

        if($practica->estado === 5) {
            $practica->estado_proceso = 'completo';
        }
        
        $practica->save();
        
        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function storeDesarrollo(Request $request){
        $user = Auth::user();
        $ed = $request->ed;
        
        // Validación rápida
        if (!$user || !$user->persona) {
            return response()->json(['error' => 'Usuario no válido'], 400);
        }

        if ($ed == 1) {
            Practica::create([
                'estudiante_id' => $user->persona->id,
                'estado_proceso' => 'en proceso',
                'tipo_practica' => 'desarrollo',
                'estado' => 1,
                'date_create' => now(),
                'date_update' => now(),
            ]);
        }elseif ($ed == 2) {
            Practica::create([
                'estudiante_id' => $user->persona->id,
                'estado_proceso' => 'en proceso',
                'tipo_practica' => 'convalidacion',
                'estado' => 1,
                'date_create' => now(),
                'date_update' => now(),
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function desarrollo(){
        $user = Auth::user();
        if (!$user || !$user->persona) {
            abort(403, 'Usuario no autorizado');
        }

        $practicaData = Practica::with(['empresa', 'jefeInmediato'])
        ->where('estudiante_id', $user->persona->id)
        ->where('tipo_practica', 'desarrollo')
        ->first();

        if (!$practicaData) {
            // En lugar de abortar, podrías redirigir o manejar de otra forma
            return redirect()->back()->with('error', 'Práctica no encontrada');
        }

        $grupo_estudiante = grupo_estudiante::where('id_estudiante', $user->persona->id)->first();
        $grupo_practica = grupos_practica::findOrFail($grupo_estudiante->id_grupo_practica);

        $semestre = Semestre::findOrFail($grupo_practica->id_semestre);

        $docente = Persona::findOrFail($grupo_practica->id_docente);
        $supervisor = Persona::findOrFail($grupo_estudiante->id_supervisor);

        
        // Validar existencia de registros relacionados
        $empresaExiste = !is_null($practicaData->empresa);
        $jefeExiste = !is_null($practicaData->jefeInmediato);
        //dd($practicaData);
        return view('practicas.admin.desarrollo', compact('practicaData', 'empresaExiste', 'jefeExiste', 'docente', 'supervisor', 'semestre'));
    }

    public function convalidacion(){
        $user = Auth::user();
        if (!$user || !$user->persona) {
            abort(403, 'Usuario no autorizado');
        }

        $practicaData = Practica::with(['empresa', 'jefeInmediato'])
        ->where('estudiante_id', $user->persona->id)
        ->where('tipo_practica', 'convalidacion')
        ->first();

        if (!$practicaData) {
            // En lugar de abortar, podrías redirigir o manejar de otra forma
            return redirect()->back()->with('error', 'Práctica no encontrada');
        }
        
        // Validar existencia de registros relacionados
        $empresaExiste = !is_null($practicaData->empresa);
        $jefeExiste = !is_null($practicaData->jefeInmediato);
        //dd($practicaData);
        return view('practicas.admin.convalidacion', compact('practicaData', 'empresaExiste', 'jefeExiste'));
    }

    public function storeFut(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'fut' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'fut_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('fut')->storeAs('futs', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_fut' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Formulario de Trámite (FUT) subido correctamente.');
    }

    public function storeCartaPresentacion(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'carta_presentacion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'carta_presentacion_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('carta_presentacion')->storeAs('cartas_presentacion', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_carta_presentacion' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Carta de Presentación subida correctamente.');
    }

    public function storeCartaAceptacion(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'carta_aceptacion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'carta_aceptacion_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('carta_aceptacion')->storeAs('cartas_aceptacion', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_carta_aceptacion' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Carta de Aceptación subida correctamente.');
    }

    public function storePlanActividadesPPP(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'plan_actividades_ppp' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'plan_actividades_ppp_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('plan_actividades_ppp')->storeAs('plan_actividades_ppp', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_plan_actividades' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Plan de Actividades de las PPP subido correctamente.');
    }

    public function storeConstanciaCumplimiento(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'constancia_cumplimiento' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'constancia_cumplimiento_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('constancia_cumplimiento')->storeAs('constancia_cumplimiento', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_constancia_cumplimiento' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Constancia de Cumplimiento subida correctamente.');
    }

    public function storeInformeFinalPPP(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'informe_final_ppp' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'informe_final_ppp_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('informe_final_ppp')->storeAs('informe_final_ppp', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_informe_final' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Informe Final de PPP subido correctamente.');
    }
    
    public function storeRegistroActividades(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'registro_actividades' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'registro_actividades_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('registro_actividades')->storeAs('registro_actividades', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_registro_actividades' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Registro de Actividades subido correctamente.');
    }

    public function storeControlMensualActividades(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'control_mensual_actividades' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'control_mensual_actividades_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('control_mensual_actividades')->storeAs('control_mensual_actividades', $nombre, 'public');

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($personaId);
        $practica->update([
            'ruta_control_actividades' => 'storage/' . $ruta,
            'estado_proceso' => 'en proceso',
        ]);

        return back()->with('success', 'Control Mensual de Actividades subido correctamente.');
    }
}

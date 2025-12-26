<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use App\Models\JefeInmediato;
use App\Models\Practica;
use App\Models\Persona;
use App\Models\Facultad;
use App\Models\Semestre;
use App\Models\Archivo;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PracticaController extends Controller
{
    public function lst_supervision(Request $request){
        $user = auth()->user();
        $userRolId = $user->getRolId();
        $id_semestre = session('semestre_actual_id');
        $ap = $user->persona->asignacion_persona()->where('id_semestre', $id_semestre)->first();

        $facQuery = Facultad::query();
        if ($userRolId == 2) {
            $facQuery->where('id', $ap->seccion_academica->id_facultad);
        }

        $facultades = $facQuery->get();

        $pQuery = Persona::whereHas('asignacion_persona.seccion_academica', function ($query) use ($request, $ap, $id_semestre) {
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
        
        $personas = $pQuery->with([
                'asignacion_persona' => function($query) use ($id_semestre) {
                    $query->where('id_semestre', $id_semestre)
                          ->with(['seccion_academica.escuela', 'practicas.jefeInmediato']);
                }
            ])
            ->whereHas('asignacion_persona', function ($query) use ($ap, $id_semestre) {
                $query->where('id_rol', 5)
                      ->where('id_semestre', $id_semestre);
                if($ap->id_rol == 3) $query->where('id_sa', $ap->id_sa);
            })
            ->get();

        return view('practicas.admin.supervision', compact('personas', 'facultades'));
    }

    public function show($id){
        $practica = Practica::with(['empresa', 'jefeInmediato'])->findOrFail($id);
        return response()->json($practica);
    }

    public function showTypeFile($type, $id){
        $archivo = Practica::where('id', $id)->with('archivos')->get();
        /*->whereHas('archivos', function ($query) {
            $query->where('tipo', $type);
        })->get();*/
        return response()->json($archivo);
    }

    public function status($id) {
        $practica = Practica::select('state', 'calificacion')->findOrFail($id);
        return response()->json($practica);
    }

    public function calificar(Request $request) {
        $request->validate([
            'practica_id' => 'required|exists:practicas,id',
            'calificacion' => 'required|numeric|min:0|max:20',
        ]);

        $practica = Practica::findOrFail($request->practica_id);
        
        $practica->update([
            'calificacion' => $request->calificacion,
            'state' => 6,
            'estado_practica' => 'completo' 
        ]);

        return back()->with('success', 'Calificación registrada correctamente.');
    }

    public function getCalificacion($id) {
        $practica = Practica::findOrFail($id); // <-- Solo calificacion
        return response()->json($practica);
    }

    public function solicitud_nota(Request $request) {
        $request->validate([
            'id' => 'required|exists:practicas,id',
            'motivo' => 'required|string',
        ]);

        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $practica = Practica::findOrFail($request->id);
        
        $practica->update([
            'state' => 7,
            'estado_practica' => 'pendiente' 
        ]);

        $solicitud = Solicitud::create([
            'id_ap_solicitante' => $ap_now->id,
            'solicitudable_id' => $practica->id,
            'solicitudable_type' => Practica::class,
            'tipo' => 'rectificacion_nota',
            'motivo' => $request->motivo,
            'justificacion' => '',
            'state' => 0,
        ]);

        return back()->with('success', 'Solicitud de nota registrada correctamente.');
    }

    public function getSolicitudNota($id_ap) {
        $practica = Practica::findOrFail($id);
        return response()->json($practica);
    }

    public function proceso(Request $request) {
        $id = $request->id;
        $nuevoEstado = $request->estado; // "aprobado" o "rechazado"
        Log::info('Id now: '.$id);
        Log::info('Estado now: '.$nuevoEstado);
        $practica = Practica::findOrFail($id);
        
        // Verificar condiciones según el estado actual
        $cumpleCondiciones = false;
        
        switch ($practica->state) {
            case 1:
                $cumpleCondiciones = $practica->empresa()->exists() && $practica->jefeInmediato()->exists();
                Log::info('AQUIII');
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

        //Log::info('Data cumple '.$cumpleCondiciones);
        
        if (!$cumpleCondiciones && $nuevoEstado === 'aprobado') {
            $mensajeError = 'No se puede aprobar: ';
            if ($practica->estado == 1) {
                $mensajeError .= 'debe registrar la empresa y jefe inmediato primero.';
            } else {
                $mensajeError .= 'faltan documentos requeridos para este estado.';
            }
            Log::info('ADFF: '.$mensajeError);
            return back()->with('error', $mensajeError);
        }
        
        if ($nuevoEstado === 'aprobado') {
            $practica->state += 1;
            $practica->estado_practica = 'completo';
        } elseif ($nuevoEstado === 'rechazado') {
            $practica->estado_practica = 'rechazado';
            if ($request->test) {
                $empresa = Empresa::where('id_practica', $id)->first();
                if ($empresa) {
                    $empresa->update([
                        'state' => 2,
                    ]);
                }
                
                $jefeInmediato = JefeInmediato::where('id_practica', $id)->first();
                if ($jefeInmediato) {
                    $jefeInmediato->update([
                        'state' => 2,
                    ]);
                }
            }
        }

        if($practica->state === 5) {
            $practica->estado_practica = 'completo';
        }
        
        $practica->save();
        
        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function storeDesarrollo(Request $request){
        $user = Auth::user();
        $ap = $user->persona->asignacion_persona;
        $ed = $request->ed;
        
        // Validación rápida
        if (!$user || !$user->persona) {
            return redirect()->back()->with('error', 'Usuario no válido');
        }

        if ($ed == 1) {
            Practica::create([
                'id_ap' => $ap->id,
                'estado_practica' => 'En Proceso',
                'tipo_practica' => 'desarrollo',
                'state' => 1
            ]);
        }elseif ($ed == 2) {
            Practica::create([
                'id_ap' => $ap->id,
                'estado_practica' => 'En Proceso',
                'tipo_practica' => 'convalidacion',
                'state' => 1
            ]);
        }
        return redirect()->back()->with('success', 'Tipo de práctica seleccionado correctamente.');
    }

    public function desarrollo(){
        $user = Auth::user();
        if (!$user || !$user->persona) {
            abort(403, 'Usuario no autorizado');
        }

        $ap = $user->persona->asignacion_persona;

        $practicaData = Practica::with(['empresa', 'jefeInmediato'])
        ->where('id_ap', $ap->id)
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
            'practica' => 'required|exists:practicas,id',
            'fut' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_p = $request->practica;

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($id_p);

        // Guardar el archivo
        $file = $request->file('fut');
        $nombre = 'fut_' . $practica->id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('futs', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        //$nombre = 'fut_' . $practica->id_ap . '_' . time() . '.pdf';
        //$ruta = $request->file('fut')->storeAs('futs', $nombre, 'public');

        
        $practica->update([
            'estado_practica' => 'en proceso',
        ]);

        Archivo::create([
            'archivo_id' => $practica->id,
            'archivo_type' => Practica::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'fut',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $practica->id_ap,
            'state' => 1
        ]);

        return back()->with('success', 'Formulario de Trámite (FUT) subido correctamente.');
    }

    public function storeCartaPresentacion(Request $request){
        $request->validate([
            'practica' => 'required|exists:practicas,id',
            'carta_presentacion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_p = $request->practica;

        // Buscar o crear la matrícula
        $practica = Practica::findOrFail($id_p);

        // Guardar el archivo
        $file = $request->file('carta_presentacion');
        $nombre = 'carta_presentacion_' . $practica->id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('carta_presentacion', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        $practica->update([
            'estado_proceso' => 'en proceso',
        ]);

        Archivo::create([
            'archivo_id' => $practica->id,
            'archivo_type' => Practica::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'carta_presentacion',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $practica->id_ap,
            'state' => 1
        ]);

        return back()->with('success', 'Carta de Presentación subida correctamente.');
    }

    public function storeCartaAceptacion(Request $request){
        $request->validate([
            'practica' => 'required|exists:practicas,id',
            'carta_aceptacion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_p = $request->practica;

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

<?php

namespace App\Http\Controllers;

use App\Models\asignacion_persona;
use App\Models\Empresa;
use App\Models\grupo_estudiante;
use App\Models\grupo_practica;
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
    public function index(Request $request) {
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        
        // 1. Obtener la asignación del usuario PARA EL SEMESTRE SELECCIONADO
        $ap = asignacion_persona::where('id_persona', $user->persona->id)
            ->where('id_semestre', $id_semestre)
            ->first();

        // 2. Si no hay asignación en este semestre (ej: admin global o no asignado aún)
        // buscamos la última como fallback para determinar el ROL base
        if (!$ap) {
            $ap = $user->getAP();
        }

        $userRolId = $ap->id_rol ?? $user->getRolId();
        Log::info('Semestre seleccionado: '.$id_semestre . ' | Rol detectado: ' . $userRolId);

        $facQuery = Facultad::query();
        if ($userRolId == 2 && $ap && $ap->seccion_academica) {
            $facQuery->where('id', $ap->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        // 3. Consulta de Personas (Estudiantes)
        $pQuery = Persona::whereHas('asignacion_persona', function ($query) use ($id_semestre) {
                $query->where('id_rol', 5)
                      ->where('id_semestre', $id_semestre);
            });

        // Filtros dinámicos de búsqueda
        $pQuery->whereHas('asignacion_persona.seccion_academica', function ($query) use ($request, $ap, $id_semestre, $userRolId) {
            $query->where('id_semestre', $id_semestre);
            
            if($request->filled('facultad')){
                $facId = ($userRolId == 2 && $ap && $ap->seccion_academica) 
                         ? $ap->seccion_academica->id_facultad 
                         : $request->facultad;
                $query->where('id_facultad', $facId);
            }
            if($request->filled('escuela')) $query->where('id_escuela', $request->escuela);
            if($request->filled('seccion')) $query->where('id', $request->seccion);
        });

        // 4. Restricción por Supervisor (Rol 3) o Docente (Rol 2)
        if ($userRolId == 3 && $ap) {
            $pQuery->whereHas('asignacion_persona', function($q) use ($ap) {
                $q->where('id_sa', $ap->id_sa);
            });
        }

        $personas = $pQuery->with([
                'asignacion_persona' => function($query) use ($id_semestre) {
                    $query->where('id_semestre', $id_semestre)
                          ->with(['seccion_academica.escuela', 'practicas.jefeInmediato']);
                }
            ])->get();

        Log::info('Estudiantes encontrados: '. $personas->count());
        
        return view('practicas.admin.practica', compact('personas', 'facultades'));
    }

    public function lst_supervision(Request $request){
        $user = auth()->user();
        $id_semestre = session('semestre_actual_id');
        
        $ap = asignacion_persona::where('id_persona', $user->persona->id)
            ->where('id_semestre', $id_semestre)
            ->first();

        if (!$ap) {
            $ap = $user->getAP();
        }

        $userRolId = $ap->id_rol ?? $user->getRolId();

        $facQuery = Facultad::query();
        if ($userRolId == 2 && $ap && $ap->seccion_academica) {
            $facQuery->where('id', $ap->seccion_academica->id_facultad);
        }
        $facultades = $facQuery->get();

        $pQuery = Persona::whereHas('asignacion_persona', function ($query) use ($id_semestre) {
                $query->where('id_rol', 5)
                      ->where('id_semestre', $id_semestre);
            });

        $pQuery->whereHas('asignacion_persona.seccion_academica', function ($query) use ($request, $ap, $id_semestre, $userRolId) {
            $query->where('id_semestre', $id_semestre);
            if($request->filled('facultad')){
                $facId = ($userRolId == 2 && $ap && $ap->seccion_academica) ? $ap->seccion_academica->id_facultad : $request->facultad;
                $query->where('id_facultad', $facId);
            }
            if($request->filled('escuela')) $query->where('id_escuela', $request->escuela);
            if($request->filled('seccion')) $query->where('id', $request->seccion);
        });

        if ($userRolId == 3 && $ap) {
            $pQuery->whereHas('asignacion_persona', function($q) use ($ap) {
                $q->where('id_sa', $ap->id_sa);
            });
        }

        $personas = $pQuery->with([
                'asignacion_persona' => function($query) use ($id_semestre) {
                    $query->where('id_semestre', $id_semestre)
                          ->with(['seccion_academica.escuela', 'practicas.jefeInmediato']);
                }
            ])->get();

        return view('practicas.admin.supervision', compact('personas', 'facultades'));
    }

    public function detalle_supervision($id){
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autorizado');
        }

        // Logic similar to desarrollo() but for a specific ID passed in URL
        $practicaData = Practica::with([
            'empresa', 
            'jefeInmediato', 
            'asignacion_persona.persona', 
            'asignacion_persona.seccion_academica.escuela', 
            'asignacion_persona.seccion_academica.semestre'
        ])->findOrFail($id);
        
        // Log::info('QUE SALEEE: '.$practicaData);

        $semestre = $practicaData->asignacion_persona->seccion_academica->semestre;
        $escuela = $practicaData->asignacion_persona->seccion_academica->escuela;
        $estudiante = $practicaData->asignacion_persona->persona;
        
        // Find docente and supervisor
        $grupo_estudiante = grupo_estudiante::where('id_estudiante', $practicaData->asignacion_persona->id)->first();
        $docente = null;
        $supervisor = null;

        if($grupo_estudiante){
             $grupo_practica = grupo_practica::find($grupo_estudiante->id_grupo_practica);
             if($grupo_practica) {
                $docente = Persona::find($grupo_practica->id_docente);
             }
             if($grupo_estudiante->id_supervisor) {
                $supervisor = Persona::find($grupo_estudiante->id_supervisor);
             }
        }

        return view('practicas.admin.detalle_supervision', compact('practicaData', 'estudiante', 'semestre', 'escuela', 'docente', 'supervisor'));
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
        $ap = $user->getAP();
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
}

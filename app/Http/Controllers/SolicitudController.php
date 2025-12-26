<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Practica;
use App\Models\asignacion_persona;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    //

    public function getSolicitudNota($id_practica)
    {
        try {
            $solicitud = Solicitud::where('solicitudable_id', $id_practica)
                                 ->where('solicitudable_type', Practica::class)
                                 ->latest()
                                 ->first();
            return response()->json($solicitud);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // post
    public function setSolicitudNota(Request $request)
    {
        try {
            $id_semestre = session('semestre_actual_id');
            $authUser = auth()->user();

            $ap_now = $authUser->persona->asignacion_persona;


            $solicitud = Solicitud::where('solicitudable_id', $request->id_practica)
                                 ->where('solicitudable_type', Practica::class)
                                 ->first();
            $solicitud->id_ap_revisor = $ap_now->id;
            $solicitud->justificacion = $request->justificacion;
            $solicitud->state = $request->estado;
            $solicitud->save();

            $practica = Practica::findOrFail($request->id_practica);

            if($solicitud->state == 1) {
                $practica->state = 5;
            } else {
                $practica->state = 6;
            }
            $practica->save();

            Log::info('Request all: '. json_encode($request->all()));


            return back()->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar solicitud: '.$e->getMessage());
        }
    }

    public function solicitud_ap(Request $request) {
        try {
            $id_semestre = session('semestre_actual_id');
            $authUser = auth()->user();

            $ap_now = $authUser->persona->asignacion_persona;

            Log::info('Request all AP: '. json_encode($request->all()));

            if($request->opcion == 1){
                $opcion = 'deshabilitar';
            }else if($request->opcion == 2){
                $opcion = 'eliminar';
            }else if($request->opcion == 3){
                $opcion = 'habilitar';
            }

            $ap = asignacion_persona::findOrFail($request->id_ap);

            $solicitud = Solicitud::create([
                'id_ap_solicitante' => $ap_now->id,
                'solicitudable_id' => $request->id_ap,
                'solicitudable_type' => asignacion_persona::class,
                'tipo' => 'rectificacion_ap',
                'motivo' => $request->comentario,
                'justificacion' => '',
                'data' => [
                    'opcion' => $opcion,
                    'state' => $ap->state
                ],
                'state' => 0
            ]);

            if($request->opcion != 0){
                $asignacion = asignacion_persona::findOrFail($request->id_ap);
                $asignacion->state = 3;
                $asignacion->save();
            }
            return back()->with('success', 'Asignación '.$opcion.' correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar solicitud: '.$e->getMessage());
        }
    }

    public function getSolicitudAp($id_ap) {
        try {
            $solicitud = Solicitud::where('solicitudable_id', $id_ap)
                                 ->where('solicitudable_type', asignacion_persona::class)
                                 ->latest()
                                 ->first();
            return response()->json($solicitud);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function setSolicitudAp(Request $request) {
        try {
            Log::info('Request all: '. json_encode($request->all()));
            $solicitud = Solicitud::findOrFail($request->id_sol);
            $solicitud->state = $request->estado;
            $solicitud->save();

            if($request->estado == 1){
                $ap = asignacion_persona::where('id', $solicitud->solicitudable_id)->first();
                if($solicitud->data['opcion'] == 'deshabilitar'){
                    $ap->state = 4;
                    $ap->save();
                }else if($solicitud->data['opcion'] == 'eliminar'){
                    // Verificamos si la persona tiene otras asignaciones
                    $persona = $ap->persona;
                    $asignacionesCount = asignacion_persona::where('id_persona', $persona->id)->count();

                    if ($asignacionesCount == 1) {
                        // Es única, verificar si tiene registros dependientes en asignacion_persona
                        $hasDependencies = $ap->practicas()->exists() ||
                                        $ap->matricula()->exists() ||
                                        $ap->grupo_estudiante()->exists() ||
                                        $ap->evaluaciones()->exists() ||
                                        $ap->evaluacion_practica()->exists();

                        if (!$hasDependencies) {
                            // No tiene nada, eliminar AP, Persona y User
                            $user = $persona->user;
                            $ap->delete();
                            $persona->delete(); // El boot en Persona debería eliminar al user, pero por seguridad revisamos
                            if ($user) {
                                $user->delete();
                            }
                        } else {
                            // Tiene registros, solo cambiar state a 0

                            $ap->state = 0;
                            $ap->save();
                        }
                    } else {
                        // Es única, verificar si tiene registros dependientes en asignacion_persona
                        $hasDependencies = $ap->practicas()->exists() ||
                                        $ap->matricula()->exists() ||
                                        $ap->grupo_estudiante()->exists() ||
                                        $ap->evaluaciones()->exists() ||
                                        $ap->evaluacion_practica()->exists();
                        if (!$hasDependencies) {
                            // No tiene nada, eliminar AP
                            $ap->delete();
                        } else {
                            $ap->state = 0;
                            $ap->save();
                        }
                    }
                }else if($solicitud->data['opcion'] == 'habilitar'){
                    $ap->state = 1;
                    $ap->save();
                }
            }
            if($request->estado == 2){
                $ap = asignacion_persona::where('id', $solicitud->solicitudable_id)->first();
                $ap->state = $solicitud->data['state'];
                $ap->save();
            }

            return back()->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar solicitud: '.$e->getMessage());
        }
    }
}

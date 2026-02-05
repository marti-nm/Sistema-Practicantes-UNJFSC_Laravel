<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Matricula;
use App\Models\Archivo;

class matriculaController extends Controller
{
    public function index(){
        $user = Auth::user(); // Usuario autenticado s
        $persona = $user->persona; // Relación uno a uno
        $matricula = $persona?->matricula; // Puede ser null si aún no tiene
        return view('matricula.indexM', compact('matricula', 'persona'));
    }

    public function modal(){
        $user = Auth::user(); // Usuario autenticado s
        $persona = $user->persona; // Relación uno a uno
        $matricula = $persona?->matricula; // Puede ser null si aún no tiene
        return view('matricula.view_estu', compact('matricula', 'persona'));
    }

    public function getMatricula($id, $tipo)
    {
        try {
            //Log::info('ID: ' . $id . ', Tipo: ' . $tipo);
            $matricula = Matricula::where('id_ap', $id)->first();
            
            // si no matricula que retorne vacio
            if (!$matricula) {
                $matricula = collect();
                return response()->json($matricula);
            }

            $archivos = $matricula->archivos()->where('tipo', $tipo)->latest()->get();
            return response()->json($archivos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno al obtener documentos'], 500);
        }
    }

    public function actualizarEstadoArchivo(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->getAp();

        $archivo = Archivo::findOrFail($request->id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = $ap_now->id;
        $archivo->save();

        if($request->estado == 'Aprobado') {
            $matricula = Matricula::where('id', $archivo->archivo_id)->first();
            if($matricula){
                $matricula->state++;
                if($matricula->state >= 2) {
                    $matricula->estado_matricula = 'Completo';

                    $ap_est = $matricula->asignacion_persona;
                    $ap_est->state = 1;
                    $ap_est->save();
                }
                $matricula->save();
            }
        }

        return redirect()->back()->with('success', 'Documento validado correctamente.');
    }
}

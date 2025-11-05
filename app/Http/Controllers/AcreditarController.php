<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Semestre;
use App\Models\Escuela;
use App\Models\Acreditar;
use App\Models\Archivo;
use App\Models\asignacion_persona;


class AcreditarController extends Controller
{
    //
    public function index()
    {
        //
    }

    public function aDTitular()
    {
        $semestre_id = session('semestre_actual_id');

        // debe reunir los datos de la tabla
        $acreditar = Acreditar::whereHas('asignacion_persona', function ($query) use ($semestre_id) {
            $query->where('id_semestre', $semestre_id);
            $query->where('id_rol', 3);
        })->with(['asignacion_persona.persona', 'asignacion_persona.semestre', 'asignacion_persona.escuela'])->get();
        return view('ValidacionAcreditacion.ValidacionDocente', compact('acreditar'));
    }

    public function ADSupervisor() {
        $semestre_id = session('semestre_actual_id');
    
        // Obtener las acreditaciones de los supervisores para el semestre actual
        $acreditar = Acreditar::whereHas('asignacion_persona', function ($query) use ($semestre_id) {
            $query->where('id_semestre', $semestre_id);
            $query->where('id_rol', 4); // Rol de Supervisor
        })->with([
            'asignacion_persona.persona', 
            'asignacion_persona.semestre', 
            'asignacion_persona.escuela'
        ])->get();

        if (!$acreditar) {
            $acreditar = new Acreditar(['ap_id' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
        }
    
        return view('ValidacionAcreditacion.ValidacionDocente', compact('acreditar'));
    }

    public function acreditarDTitular()
    {
        // Tengo que enviar el id de la persona en la vista
        $persona_id = auth()->user()->persona->id;
        $semestre_id = session('semestre_actual_id');

        $ap = asignacion_persona::where('id_persona', $persona_id)
            ->where('id_semestre', $semestre_id)
            ->first();

        $acreditacion = Acreditar::where('ap_id', $ap->id)
            ->where(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if(!$acreditacion) {
            $acreditacion = new Acreditar(['ap_id' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
        }

        return view('acreditacion.acreditacionDocente', compact('ap', 'acreditacion'));
    }

    public function acreditarDSupervisor()
    {
        // Tengo que enviar el id de la persona en la vista
        $persona_id = auth()->user()->persona->id;
        $semestre_id = session('semestre_actual_id');
        $ap = asignacion_persona::where('id_persona', $persona_id)
            ->where('id_semestre', $semestre_id)
            ->first();
            
        $acreditacion = Acreditar::where('ap_id', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if(!$acreditacion) {
            $acreditacion = new Acreditar(['ap_id' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
        }

        return view('acreditacion.acreditacionDocente', compact('ap', 'acreditacion'));
    }

    public function actualizarEstadoArchivo(Request $request, $id) {
        $archivo = Archivo::findOrFail($id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = auth()->user()->getRolId();
        $archivo->save();

        return back()->with('success', 'Estado del archivo actualizado correctamente');
    }

    /*
    public function actualizarEstadoCL(Request $request, $id) {
        $archivo = Archivo::findOrFail($id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = auth()->user()->getRolId();
        $archivo->save();

        return back()->with('success', 'Estado de carga lectiva actualizado correctamente');
    }

    public function actualizarEstadoHorario(Request $request, $id) {
        $archivo = Archivo::findOrFail($id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = auth()->user()->getRolId();
        $archivo->save();

        return back()->with('success', 'Estado de horario actualizado correctamente');
    }

    public function actualizarEstadoResolucion(Request $request, $id) {
        $archivo = Archivo::findOrFail($id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = auth()->user()->getRolId();
        $archivo->save();

        return back()->with('success', 'Estado de la resolución actualizado correctamente');
    }
    */
}

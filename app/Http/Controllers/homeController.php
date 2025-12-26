<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Escuela;
use App\Models\grupo_estudiante;
use App\Models\grupos_practica;
use App\Models\Persona;
use App\Models\Practica;
use App\Models\Semestre;
use App\Models\asignacion_persona;
use App\Models\Matricula;
use Illuminate\Support\Facades\Log;

class homeController extends Controller
{
    public function index(){
        
        return view('panel.index2'); 
    }

    public function index_estudiante(){
        
        $semestre = session('semestre_actual_id');
        $persona = auth()->user()->persona;
        if (!$persona) {
            return redirect()->route('home')->with('error', 'No se encontró la persona asociada al usuario.');
        }
        $ap = asignacion_persona::where('id_persona', $persona->id)
            ->where('id_rol', 5)
            ->with([
                'persona',
                'seccion_academica.escuela',
                'seccion_academica.semestre'
            ])
            ->first();
        $id_escuela = $ap?->id_escuela;
        $id_semestre = $ap?->id_semestre;
        $escuela = Escuela::find($id_escuela);
        $semestre = Semestre::find($id_semestre);

        // Matricula tien el id ap_id de asignacion_persona
        //$matricula = Matricula::where('ap_id', $ap->id)->first();
        $matricula = Matricula::where('id_ap', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$matricula) {
            $matricula = new Matricula(['ap_id' => $ap->id, 'estado_matricula' => 'Pendiente']);
        }

        //Log::info('MATRICULA:'.$matricula);
        
        // docente y estudiante esten en le mismo ciclo y escuela
        $docente = asignacion_persona::where('id_sa', $ap->id_sa)
                    ->where('id_rol', 3)
                    ->first()?->persona;
        
        $supervisor = asignacion_persona::where('id_sa', $ap->id_sa)
                    ->where('id_rol', 4)
                    ->first()?->persona;

        // si no hay docente todavia
        if (!$docente) {
            $docente = new Persona(['nombres' => 'Pendiente', 'apellidos' => 'Asignación de docente en proceso']);
        }

        $practicas = Practica::where('id_ap', $ap->id)
            ->with([
                'empresa',
                'jefeInmediato'
            ])
            ->first();

        if(!$practicas) {
            $practicas = new Practica(['estado_practica' => 'No hay prácticas registradas']);
        }


        /*
        $grupo_estudiante = grupo_estudiante::where('id_estudiante', $persona->id)->first();
    
        if (!$escuela || !$grupo_estudiante) {
            auth()->logout(); // cierra sesión
            return redirect()->route('login')->with('error', 'Aún no tienes acceso al sistema. Contacta al administrador.');
        }
    
        $grupo_practica = grupos_practica::find($grupo_estudiante->id_grupo_practica);
        $docente = Persona::find($grupo_practica?->id_docente);
        $semestre = Semestre::find($grupo_practica?->id_semestre);
    
        // Si alguno de estos aún falta, también redirige
        if (!$grupo_practica || !$docente || !$semestre) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Aún no tienes acceso al sistema. Contacta al administrador.');
        }*/
        
        return view('dashboard.estudianteDashboard', compact('ap', 'practicas', 'escuela', 'semestre', 'docente', 'supervisor', 'matricula')); 
    }

    public function matriculaEstudiante(){
        $id_ap = auth()->user()->persona->asignacion_persona->id;
        $matricula = Matricula::where('id_ap', $id_ap)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();

        // Inicializar variables de archivo
        $ficha = null;
        $record = null;

        if ($matricula) {
            // obtener el archivo más reciente de cada tipo
            $ficha = $matricula->archivos->where('tipo', 'ficha')->first();
            $record = $matricula->archivos->where('tipo', 'record')->first();
        } else {
            // Si no existe, creamos una instancia vacía para no romper la vista
            $matricula = new Matricula(['id_ap' => $id_ap, 'estado_matricula' => 'Pendiente']);
        }
        
        return view('matricula.estudiante', compact('matricula', 'ficha', 'record'));
    }

    public function practicasEstudiante(){
        $semestre = session('semestre_actual_id');
        $persona = auth()->user()->persona;
        if (!$persona) {
            return redirect()->route('home')->with('error', 'No se encontró la persona asociada al usuario.');
        }
        $ap = asignacion_persona::where('id_persona', $persona->id)
            ->where('id_rol', 5)
            ->with([
                'persona',
                'seccion_academica.escuela',
                'seccion_academica.semestre'
            ])
            ->first();
        $id_escuela = $ap?->id_escuela;
        $id_semestre = $ap?->id_semestre;
        $escuela = Escuela::find($id_escuela);
        $semestre = Semestre::find($id_semestre);

        // Matricula tien el id ap_id de asignacion_persona
        //$matricula = Matricula::where('ap_id', $ap->id)->first();
        $matricula = Matricula::where('id_ap', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$matricula) {
            $matricula = new Matricula(['ap_id' => $ap->id, 'estado_matricula' => 'Pendiente']);
        }

        //Log::info('MATRICULA:'.$matricula);
        
        // docente y estudiante esten en le mismo ciclo y escuela
        $docente = asignacion_persona::where('id_sa', $ap->id_sa)
                    ->where('id_rol', 3)
                    ->first()?->persona;
        
        $supervisor = asignacion_persona::where('id_sa', $ap->id_sa)
                    ->where('id_rol', 4)
                    ->first()?->persona;

        // si no hay docente todavia
        if (!$docente) {
            $docente = new Persona(['nombres' => 'Pendiente', 'apellidos' => 'Asignación de docente en proceso']);
        }

        $practicas = Practica::where('id_ap', $ap->id)
            ->with([
                'empresa',
                'jefeInmediato'
            ])
            ->first();


        /*
        $grupo_estudiante = grupo_estudiante::where('id_estudiante', $persona->id)->first();
    
        if (!$escuela || !$grupo_estudiante) {
            auth()->logout(); // cierra sesión
            return redirect()->route('login')->with('error', 'Aún no tienes acceso al sistema. Contacta al administrador.');
        }
    
        $grupo_practica = grupos_practica::find($grupo_estudiante->id_grupo_practica);
        $docente = Persona::find($grupo_practica?->id_docente);
        $semestre = Semestre::find($grupo_practica?->id_semestre);
    
        // Si alguno de estos aún falta, también redirige
        if (!$grupo_practica || !$docente || !$semestre) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Aún no tienes acceso al sistema. Contacta al administrador.');
        }*/
        return view('practicas.estudiante.index', compact('ap', 'practicas', 'escuela', 'semestre', 'docente', 'supervisor', 'matricula'));
    }
}

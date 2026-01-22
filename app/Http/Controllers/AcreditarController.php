<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Semestre;
use App\Models\Facultad;
use App\Models\Escuela;
use App\Models\Acreditar;
use App\Models\Archivo;
use App\Models\Matricula;
use App\Models\asignacion_persona;
use Illuminate\Support\Facades\Log;


class AcreditarController extends Controller
{
    //
    // funcion que retorne las facultades

    public function ADTitular(Request $request)
    {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $queryFac = Facultad::where('state', 1);
        if ($ap_now->id_rol == 2) {
            $queryFac->where('id', $ap_now->id_facultad);
        }
        $facultades = $queryFac->get();

        // Filtros dinámicos
        $facultad_id = $request->input('facultad');
        $escuela_id = $request->input('escuela');
        $seccion_id = $request->input('seccion');

        $usuarios = Persona::whereHas('asignacion_persona', function ($query) use ($id_semestre, $facultad_id, $escuela_id, $seccion_id) {
            $query->where('id_semestre', $id_semestre);
            $query->where('id_rol', 3); // Rol de Titular

            if ($facultad_id) {
                $query->whereHas('seccion_academica.escuela', function($q) use ($facultad_id) {
                    $q->where('id_facultad', $facultad_id);
                });
            }
            if ($escuela_id) {
                $query->whereHas('seccion_academica', function($q) use ($escuela_id) {
                    $q->where('id_escuela', $escuela_id);
                });
            }
            if ($seccion_id) {
                $query->whereHas('seccion_academica', function($q) use ($seccion_id) {
                    $q->where('id', $seccion_id);
                });
            }
        })->with([
            'asignacion_persona' => function($query) {
                $query->where('id_semestre', session('semestre_actual_id'));
            }, 
            'asignacion_persona.semestre', 
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.acreditacion.archivos' => function($query) {
                $query->select('id', 'archivo_id', 'tipo', 'estado_archivo', 'created_at')
                      ->orderBy('created_at', 'desc');
            }
        ])->get();

        $option = 1;

        if (!$usuarios) {
            $usuarios = collect();
        }

        $msj = 'Docente';

        return view('ValidacionAcreditacion.ValidacionDocente', compact('msj', 'facultades','usuarios', 'option'));
    }

    public function ADSupervisor(Request $request) {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $queryFac = Facultad::where('state', 1);
        if ($ap_now->id_rol == 2) {
            $queryFac->where('id', $ap_now->id_facultad);
        }
        $facultades = $queryFac->get();
    
        // Filtros dinámicos
        $facultad_id = $request->input('facultad');
        $escuela_id = $request->input('escuela');
        $seccion_id = $request->input('seccion');

        $usuarios = Persona::whereHas('asignacion_persona', function ($query) use ($id_semestre, $facultad_id, $escuela_id, $seccion_id) {
            $query->where('id_semestre', $id_semestre);
            $query->where('id_rol', 4); // Rol de Supervisor

            if ($facultad_id) {
                $query->whereHas('seccion_academica.escuela', function($q) use ($facultad_id) {
                    $q->where('id_facultad', $facultad_id);
                });
            }
            if ($escuela_id) {
                $query->whereHas('seccion_academica', function($q) use ($escuela_id) {
                    $q->where('id_escuela', $escuela_id);
                });
            }
            if ($seccion_id) {
                $query->whereHas('seccion_academica', function($q) use ($seccion_id) {
                    $q->where('id', $seccion_id);
                });
            }
        })->with([
            'asignacion_persona', 
            'asignacion_persona.semestre', 
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.acreditacion.archivos' => function($query) {
                $query->select('id', 'archivo_id', 'tipo', 'estado_archivo', 'created_at')
                      ->orderBy('created_at', 'desc');
            }
        ])->get();

        $option = 2;

        if (!$usuarios) {
            $usuarios = collect();
        }

        $msj = 'Supervisor';
    
        return view('acreditacion.validacionDocente', compact('msj', 'facultades', 'usuarios', 'option'));
    }

    public function acreditar() {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();
        $ap = $authUser->persona->asignacion_persona;

        $acreditacion = Acreditar::where('id_ap', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if(!$acreditacion) {
            $acreditacion = new Acreditar(['id_ap' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
        }

        return view('acreditacion.acreditacionDocente', compact('ap', 'acreditacion'));
    }

    public function acreditarDTitular()
    {
        // Tengo que enviar el id de la persona en la vista
        $persona_id = auth()->user()->persona->id;
        $semestre_id = session('semestre_actual_id');

        $ap = asignacion_persona::where('id_persona', $persona_id)
            ->where('id_semestre', $semestre_id)
            ->first();

        $acreditacion = Acreditar::where('id_ap', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if(!$acreditacion) {
            $acreditacion = new Acreditar(['id_ap' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
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
            
        $acreditacion = Acreditar::where('id_ap', $ap->id)
            ->with(['archivos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if(!$acreditacion) {
            $acreditacion = new Acreditar(['id_ap' => $ap->id, 'estado_acreditacion' => 'Pendiente']);
        }

        return view('acreditacion.acreditacionDocente', compact('ap', 'acreditacion'));
    }

    public function getArchivosPorTipo($id, $tipo) {
        try {
            $archivos = Archivo::where('archivo_type', Acreditar::class)
                ->where('archivo_id', $id)
                ->where('tipo', $tipo)
                ->latest()
                ->get();

            // Agregar metadata adicional (peso y extensión)
            $archivos->transform(function($archivo) {
                // Si la ruta es una URL completa, obtener el path relativo
                // Ej: http://127.0.0.1:8000/storage/pdf.pdf -> storage/pdf.pdf
                $relativePath = str_replace(url('/'), '', $archivo->ruta);
                $relativePath = ltrim($relativePath, '/');

                $fullPath = public_path($relativePath);
                
                $archivo->peso = (file_exists($fullPath) && !is_dir($fullPath)) 
                    ? $this->formatBytes(filesize($fullPath)) 
                    : 'N/A';
                $archivo->extension = strtoupper(pathinfo($archivo->ruta, PATHINFO_EXTENSION));
                return $archivo;
            });

            return response()->json($archivos);
        } catch (\Exception $e) {
            Log::error('Error obteniendo documentos de acreditación: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener documentos'], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function actualizarEstadoArchivo(Request $request) {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $archivo = Archivo::findOrFail($request->id);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = $ap_now->id;
        $archivo->save();

        $acreditacion = Acreditar::findOrFail($request->acreditacion);
        
        $ap = asignacion_persona::findOrFail($acreditacion->id_ap);
        
        // Verificar si cumple con todos los requisitos para aprobar la acreditación
        $this->verificarEstadoAcreditacion($acreditacion->id, $ap->id_rol);
        
        return back()->with('success', 'Estado de acreditación actualizado correctamente');
    }

    public function actualizarEstadoArchivoMat(Request $request, $id_archivo) {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $archivo = Archivo::findOrFail($id_archivo);
        $archivo->estado_archivo = $request->estado;
        $archivo->comentario = $request->comentario;
        $archivo->revisado_por_user_id = $ap_now->id;
        $archivo->save();
        
        $id_file = $archivo->archivo_id;

        $ap = asignacion_persona::findOrFail($request->ap_id);

        if($ap->id_rol == 3 || $ap->id_rol == 4) {
            $this->verificarEstadoAcreditacion($archivo->archivo_id, $ap->id_rol);
        } else if($ap->id_rol == 5) {
            // la funciona debe retornar true o false para actualizar el state de ap a 1
            if($this->verificarEstadoMatricula($archivo->archivo_id)) {
                $ap->state = 1;
                $ap->save();
            }
        }

        return back()->with('success', 'Estado del archivo actualizado correctamente');
    }

    public function verificarEstadoMatricula($id_file) {
        $matricula = Matricula::findOrFail($id_file);
        $doc_requeridos = ['ficha', 'record'];
        $todos_los_archivos = Archivo::where('archivo_id', $id_file)
        ->where('archivo_type', Matricula::class) // Asegurar la relación polimórfica
        ->orderBy('created_at', 'desc')
        ->get();

        $archivos_por_tipo = $todos_los_archivos->groupBy('tipo')
        ->map(function ($group) {
            return $group->first(); // El 'first()' es el más reciente debido al orderBy('desc') anterior
        });

        $archivo_aprob = true;

        foreach ($doc_requeridos as $tipo_requerido) {
            $latest_file = $archivos_por_tipo->get($tipo_requerido); 
            
            if (is_null($latest_file)) {
                $archivo_aprob = false;
                break;
            }

            if ($latest_file->estado_archivo != 'Aprobado') {
                $archivo_aprob = false;
                break;
            }
        }

        if($archivo_aprob && $matricula->estado_archivo != 'Completo') {
            $matricula->estado_matricula = 'Completo';
            $matricula->save();
            return true;
        }

        return false;
    }

    public function verificarEstadoAcreditacion($id_acreditacion, $rol) {
        $acreditacion = Acreditar::findOrFail($id_acreditacion);
        $doc_requeridos = [];
        if($rol == 3) {
            $doc_requeridos = ['carga_lectiva', 'horario'];
        } else if($rol == 4) {
            $doc_requeridos = ['carga_lectiva', 'horario', 'resolucion'];
        }

        $todos_los_archivos = Archivo::where('archivo_id', $id_acreditacion)
        ->where('archivo_type', Acreditar::class) // Asegurar la relación polimórfica
        ->orderBy('created_at', 'desc') // Los más nuevos primero
        ->get(); // <-- CLAVE: Ejecuta la consulta y trae la Colección

        $archivos_por_tipo = $todos_los_archivos->groupBy('tipo')
        ->map(function ($group) {
            return $group->first(); // El 'first()' es el más reciente debido al orderBy('desc') anterior
        });

        $archivos_vig = $todos_los_archivos->unique('tipo');
        $archivo_aprob = true;

        foreach ($doc_requeridos as $tipo_requerido) {
            $latest_file = $archivos_por_tipo->get($tipo_requerido); 
            
            if (is_null($latest_file)) {
                $archivo_aprob = false;
                break;
            }

            if ($latest_file->estado_archivo != 'Aprobado') {
                $archivo_aprob = false;
                break;
            }
        }

        if($archivo_aprob && $acreditacion->estado_acreditacion != 'Aprobado') {
            $acreditacion->estado_acreditacion = 'Aprobado';
            $acreditacion->f_acreditacion = now();
            $acreditacion->save();
            $this->actualizarEstadoAsignacionPersona($acreditacion->id_ap);
        } else if(!$archivo_aprob && $acreditacion->estado_acreditacion != 'Pendiente') {
            $acreditacion->estado_acreditacion = 'Pendiente';
            $acreditacion->save();
        }
    }

    public function actualizarEstadoAsignacionPersona($id_ap) {
        $ap = asignacion_persona::findOrFail($id_ap);
        $ap->state = 1;
        $ap->save();
    }
}

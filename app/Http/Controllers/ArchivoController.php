<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Acreditar;
use App\Models\EvaluacionPractica;
use App\Models\evaluacion_archivo;
use App\Models\Archivo;
use App\Models\Practica;
use App\Models\type_users;
use App\Models\Facultad;
use App\Models\grupo_estudiante;
use App\Models\grupo_practica;
use App\Models\Recurso;
use App\Models\asignacion_persona;
use Illuminate\Http\Request;
// Log
use Illuminate\Support\Facades\Log;
use App\Traits\SincronizaGrupoTrait;


class ArchivoController extends Controller
{
    use SincronizaGrupoTrait;

    public function subirFicha(Request $request)
    {   
        Log::info('=== INICIO subirFicha ===');
        Log::info('Request data: ' . json_encode($request->all()));
        
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'ficha' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $matricula = Matricula::firstOrCreate(
            ['id_ap' => $id_ap],
            [
                'estado_matricula' => 'Pendiente',
                'state' => 0
            ]
        );

        $file = $request->file('ficha');
        $nombre = 'ficha_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('fichas', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $matricula->id,
            'archivo_type' => Matricula::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'ficha',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        $matricula->ruta_ficha = $rutaCompleta;
        
        Log::info('Ficha subida exitosamente');
        return back()->with('success', 'Ficha subida correctamente.');
    }

    public function subirRecord(Request $request)
    {
        Log::info('=== INICIO subirRecord ===');
        Log::info('Request data: ' . json_encode($request->all()));
        
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'record' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $matricula = Matricula::firstOrCreate(
            ['id_ap' => $id_ap],
            [
                'estado_matricula' => 'Pendiente',
                'state' => 0
            ]
        );

        $file = $request->file('record');
        $nombre = 'record_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('records', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $matricula->id,
            'archivo_type' => Matricula::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'record',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        $matricula->save();
        
        Log::info('Record subido exitosamente');
        return back()->with('success', 'Record subida correctamente.');
    }

    public function subirCLectiva(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'carga_lectiva' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['id_ap' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'state' => 1
            ]
        );

        $file = $request->file('carga_lectiva');
        $nombre = 'cl_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('cargas_lectivas', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'carga_lectiva',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        $acreditacion->save;

        return back()->with('success', 'Constancia de carga lectiva subida correctamente.');
    }

    public function subirHorario(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'horario' => 'required|file|mimes:pdf|max:20480',
        ]);
        
        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['id_ap' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'state' => 1
            ]
        );

        $file = $request->file('horario');
        $nombre = 'horario_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('horarios', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'horario',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        $acreditacion->save;
        return back()->with('success', 'Horario subida correctamente.');
    }

    public function subirResolucion(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'resolucion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['id_ap' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'estado' => 1
            ]
        );

        $file = $request->file('resolucion');
        $nombre = 'resolucion_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('resoluciones', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'resolucion',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        $acreditacion->save;
        return back()->with('success', 'Resolución subida correctamente.');
    }

    public function subirAnexo(Request $request)
    {
        // Lógica para subir anexos N
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'nota' => 'required|numeric|min:0|max:20',
            'number' => 'required|integer|min:1',
            // anexo es required solo si no existe rutaAnexo
            'rutaAnexo' => 'nullable|string',
            'anexo' => 'nullable|file|mimes:pdf|max:20480',
            'modulo' => 'required|integer|min:1',
            //'anexo' => 'required|file|mimes:pdf|max:20480',
        ]);
        $id_ap = $request->ap_id;
        $number = $request->number;

        // buscar la evaluacion Practica
        $evaluacionPractica = EvaluacionPractica::where('id_ap', $id_ap)->where('id_modulo', $request->modulo)->first();
        if (!$evaluacionPractica) {
            return back()->with('error', 'No se encontró la evaluación práctica para el AP ID: ' . $id_ap . ' y el módulo: ' . $request->modulo);
        }

        $evaluacion_archivo = evaluacion_archivo::create([
            'id_evaluacion' => $evaluacionPractica->id,
            'nota' => $request->nota,
            'observacion' => null,
            'state' => 1
        ]);

        if(!$request->hasFile('anexo') && !$request->rutaAnexo) {
            return back()->with('error', 'Debe proporcionar un archivo de anexo o una ruta existente.');
        }

        if($request->hasFile('anexo')) {
            $file = $request->file('anexo');
            $nombre = 'anexo_' . $number . '_' . $id_ap . '_' . time() . '.pdf';
            $ruta = $file->storeAs('anexos', $nombre, 'public');
            $rutaCompleta = 'storage/' . $ruta;
        } else {
            $rutaCompleta = $request->rutaAnexo;
        }

        Archivo::create([
            'archivo_id' => $evaluacion_archivo->id,
            'archivo_type' => evaluacion_archivo::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'anexo_' . $number,
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'state' => 1
        ]);

        return back()->with('success', 'Anexo subido correctamente.');
    }

    public function subirDocumentoPractica(Request $request) {
        $rutas = [
            'fut' => 'futs',
            'carta_presentacion' => 'cartas_presentacion',
            'carta_aceptacion' => 'cartas_aceptacion',
            'plan_actividades_ppp' => 'plan_actividades_ppp',
            'registro_actividades' => 'registro_actividades',
            'control_actividades' => 'control_actividades',
            'constancia_cumplimiento' => 'constancias_cumplimiento',
            'informe_final_ppp' => 'informes_final_ppp'
        ];

        $request->validate([
            'practica' => 'required|exists:practicas,id',
            'tipo' => 'required|in:' . implode(',', array_keys($rutas)),
            'archivo' => 'required|file|mimes:pdf|max:20480',
        ]);

        $practica = Practica::findOrFail($request->practica);

        
        $file = $request->file('archivo');
        $nombre = $request->tipo . '_' . $practica->id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs($rutas[$request->tipo], $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $practica->id,
            'archivo_type' => Practica::class,
            'estado_archivo' => 'Enviado',
            'tipo' => $request->tipo,
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $practica->id_ap,
            'state' => 1
        ]);

        return back()->with('success', 'Documento subido correctamente.');
    }

    public function actualizarEstadoArchivo(Request $request) {

        $archivo = Archivo::findOrFail($request->id);
        $archivo->estado_archivo = $request->estado;
        $archivo->state = ($request->estado === 'Aprobado') ? 2 : 0;
        $archivo->save();

        // --- Lógica para avanzar de etapa automáticamente ---
        if ($request->estado === 'Aprobado' && $archivo->archivo_type === Practica::class) {
            $practica = Practica::find($archivo->archivo_id);
            
            if ($practica) {
                $currentState = intval($practica->state);
                $tipoPractica = $practica->tipo_practica; // 'desarrollo' o 'convalidacion'
                $allApproved = false;

                // Definir documentos requeridos por etapa y tipo
                $requiredDocs = [];

                if ($currentState == 2) {
                    if ($tipoPractica == 'desarrollo') {
                        $requiredDocs = ['fut', 'carta_presentacion'];
                    } elseif ($tipoPractica == 'convalidacion') {
                        $requiredDocs = ['fut', 'carta_aceptacion', 'carta_aceptacion'];
                    }
                } elseif ($currentState == 3) {
                    if ($tipoPractica == 'desarrollo') {
                        $requiredDocs = ['carta_aceptacion', 'plan_actividades_ppp'];
                    } elseif ($tipoPractica == 'convalidacion') {
                        $requiredDocs = ['plan_actividades_ppp', 'registro_actividades', 'control_actividades'];
                    }
                } elseif ($currentState == 4) {
                    $requiredDocs = ['constancia_cumplimiento', 'informe_final_ppp'];
                }

                if (!empty($requiredDocs)) {
                    // Verificar si TODOS los documentos requeridos tienen al menos un archivo con estado 'Aprobado'
                    $approvedCount = 0;
                    foreach ($requiredDocs as $docType) {
                        $hasApproved = Archivo::where('archivo_type', Practica::class)
                            ->where('archivo_id', $practica->id)
                            ->where('tipo', $docType)
                            ->where('estado_archivo', 'Aprobado')
                            ->exists();
                        
                        if ($hasApproved) {
                            $approvedCount++;
                        }
                    }

                    if ($approvedCount === count($requiredDocs)) {
                        $allApproved = true;
                    }
                }

                // Si todos están aprobados, avanzar etapa
                if ($allApproved) {
                    // Avanzar estado (sin pasar de 5, asumiendo 5 es completado final)
                    $practica->state = min(5, $currentState + 1);
                    
                    if ($practica->state == 5) {
                        $practica->estado_practica = 'completo';
                    }
                    
                    $practica->save();
                    
                    return back()->with('success', 'Estado de archivo actualizado y etapa completada.');
                }
            }
        }

        return back()->with('success', 'Estado de archivo actualizado correctamente.');
    }

    public function actualizarEstadoAnexo(Request $request)
    {
        //
        $validated = $request->validate([
            // ... campos anteriores ...
            'estado' => 'required|in:Enviado,Aprobado,Corregir',@
            'evaluacion' => 'required|exists:evaluacion_archivo,id',
            'archivo' => 'required|exists:archivos,id',
            'correccionTipo' => 'nullable|in:2,3,4', // Los valores son 2, 3, 4 según tu HTML
            'comentario' => 'nullable|string',
        ]);

        $estado = $request->estado;
        // obtener evaluacion_archivo donde tipo de archivos sea $request->anexo
        if($estado == 'Enviado') {
            return back()->with('error', 'El estado "Enviado" no es válido para la actualización.');
        }

        $evaluacion_archivo = evaluacion_archivo::findOrFail($request->evaluacion);
        $evaluacion_archivo->state = ($estado == 'Aprobado') ? 5 : $request->correccionTipo;
        $evaluacion_archivo->observacion = $request->comentario;
        $evaluacion_archivo->save();

        $archivo = Archivo::findOrFail($request->archivo);
        $archivo->estado_archivo = $request->estado;
        $archivo->save();

        if($estado == 'Aprobado') {
            $ep = EvaluacionPractica::findOrFail($evaluacion_archivo->id_evaluacion);
            // Aumentar contador de archivos aprobados para esta evaluación (asumimos 2 anexos: 7 y 8)
            $ep->state = intval($ep->state) + 1;

            if($ep->state >= 2) {
                // marcar evaluación práctica como aprobada
                $ep->estado_evaluacion = 'Aprobado';
                $ep->f_evaluacion = now();
                $ep->save();

                // Ahora comprobar si todo el grupo ya aprobó este mismo módulo
                try {
                    // Buscar el grupo al que pertenece este asignacion_persona
                    $ge = grupo_estudiante::where('id_estudiante', $ep->id_ap)->first();
                    if ($ge) {
                        $group = grupo_practica::find($ge->id_gp);
                        if ($group) {
                            // Sincronizar el módulo del grupo basándose en el progreso real de todos los sus estudiantes
                            $this->sincronizarModuloGrupo($group->id);
                        }

                    }
                } catch (\Exception $e) {
                    Log::error('Error comprobando progreso de grupo después de aprobar evaluación práctica: ' . $e->getMessage());
                }
            } else {
                // aún no tiene ambos anexos aprobados, solo guardar el contador
                $ep->save();
            }
        }

        return back()->with('success', 'Anexo actualizado correctamente.');
        
    }

    public function getArchivosPorTipo($id_ap, $tipo) {
        Log::info('ID de la práctica: ' . $id_ap);
        Log::info('Tipo de archivo: ' . $tipo);
        try {
            $practica = Practica::where('id_ap', $id_ap)->first();
            Log::info('Practica encontrada: ' . $practica);
            $archivos = Archivo::where('archivo_type', Practica::class)
                ->where('archivo_id', $practica->id)
                ->where('tipo', $tipo)
                ->latest()
                ->get();

            Log::info('Archivos encontrados: ' . $archivos);

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
            Log::error('Error obteniendo documentos de práctica: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener documentos'], 500);
        }
    }

    public function getDocumentoPractica($practica, $type) {
        try {
            $archivos = Archivo::where('archivo_type', Practica::class)
                ->where('archivo_id', $practica)
                ->where('tipo', $type)
                ->select('id', 'estado_archivo', 'ruta', 'created_at', 'state')
                ->latest() // <-- Alternativa para ordenar por created_at DESC
                ->get();

            return response()->json($archivos);
        } catch (\Exception $e) {
            Log::error('Error obteniendo documentos de práctica: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener documentos'], 500);
        }
    }

    public function showPDF($path)
    {
        if (!auth()->check()) {
            abort(403, 'No autorizado');
        }

        if (!$path) {
            abort(404, 'Documento no especificado');
        }

        // Limpiar el prefijo 'storage/' si viene en la ruta de la DB
        $documento = str_replace('storage/', '', $path);

        // Prevenir directory traversal básico
        if (str_contains($documento, '..')) {
            abort(403, 'Acceso inválido');
        }

        $filePath = storage_path('app/public/' . $documento);
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    /**
     * Definir tipos de recursos permitidos por rol
     */
    /**
     * Definir tipos de recursos permitidos por rol (Mapa completo)
     */
    private function getAllTiposMap()
    {
        return [
            // Admin y SubAdmin 
            1 => [
                'otros', 'carga_lectiva', 'horario', 'resolucion', 
                'ficha', 'record', 'fut', 'carta_presentacion', 'carta_aceptacion', 
                'plan_actividades_ppp', 'constancia_cumplimiento', 'informe_final_ppp',
                'anexo_7', 'anexo_8'
            ],
            2 => [
                'otros', 'carga_lectiva', 'horario', 'resolucion', 
                'ficha', 'record', 'fut', 'carta_presentacion', 'carta_aceptacion', 
                'plan_actividades_ppp', 'constancia_cumplimiento', 'informe_final_ppp',
                'anexo_7', 'anexo_8'
            ],
            // Docente
            3 => [
                'otros', 'carga_lectiva', 'horario', 'resolucion',
                'ficha', 'record', 'fut', 'carta_presentacion', 'carta_aceptacion',
                'plan_actividades_ppp', 'constancia_cumplimiento', 'informe_final_ppp'
            ],
            // Supervisor
            4 => ['otros', 'anexo_7', 'anexo_8'],
            // Estudiante
            5 => []
        ];
    }

    /**
     * Definir tipos de recursos permitidos por rol
     */
    private function getTiposPorRol($rolId)
    {
        $tipos = $this->getAllTiposMap();
        return $tipos[$rolId] ?? [];
    }

    /**
     * Definir qué tipos de recursos se le pueden asignar a cada rol (Destinatario)
     */
    private function getMapTiposPorDestinatario()
    {
        return [
            // Admin y SubAdmin: Gestión interna
            1 => ['otros', 'resolucion', 'memrandum', 'oficio'], 
            2 => ['otros', 'resolucion', 'memorandum', 'oficio'],

            // Docente: Académico
            3 => [
                'otros', 'carga_lectiva', 'horario', 'resolucion', 'constancia_cumplimiento'
            ],

            // Supervisor: Seguimiento
            4 => ['otros', 'anexo_7', 'anexo_8'],

            // Estudiante: Trámites y Prácticas
            5 => [
                'otros', 'ficha', 'record', 'fut', 'carta_presentacion', 'carta_aceptacion', 
                'plan_actividades_ppp', 'informe_final_ppp'
            ]
        ];
    }

    /**
     * Mostrar lista de recursos
     */
    public function indexRecursos()
    {
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap = $authUser->persona->asignacion_persona;
        
        // Obtener datos académicos del usuario actual
        $mySa = $ap->seccion_academica;
        $myFacultadId = $mySa ? $mySa->id_facultad : null;
        $myEscuelaId = $mySa ? $mySa->id_escuela : null;
        $mySeccionId = $mySa ? $mySa->id : null;
        $myRolId = $authUser->getRolId();

        $rolesQuery = type_users::where('state', 1)
            ->where('name', '!=', 'admin');
        
        if ($authUser->getRolId() == 2) { 
            $rolesQuery->where('name', '!=', 'sub admin');
        }

        if ($authUser->getRolId() == 3) { 
            $rolesQuery->where('name', '!=', 'docente titular');
            $rolesQuery->where('name', '!=', 'sub admin');
        }

        $roles = $rolesQuery->get();

        // Tipos permitidos para este rol (el que sube)
        // Esto define SI PUEDO SUBIR ALGO en general
        $tiposPermitidos = $this->getTiposPorRol($authUser->getRolId());
        
        // Mapa de tipos por DESTINATARIO (para el select dinámico)
        $mapaTiposDestinatario = $this->getMapTiposPorDestinatario();

        $queryFac = Facultad::where('state', 1);
        if($authUser->getRolId() == 2 || $authUser->getRolId() == 3){ 
            if ($myFacultadId) {
                $queryFac->where('id', $myFacultadId);
            }
        }
        $facultades = $queryFac->get();

        $tipoLabels = [
            'otros' => 'Otros',
            'carga_lectiva' => 'Carga Lectiva',
            'horario' => 'Horario',
            'resolucion' => 'Resolución',
            'ficha' => 'Ficha',
            'record' => 'Record',
            'fut' => 'FUT',
            'carta_presentacion' => 'Carta de Presentación',
            'carta_aceptacion' => 'Carta de Aceptación',
            'plan_actividades_ppp' => 'Plan de Actividades PPP',
            'constancia_cumplimiento' => 'Constancia de Cumplimiento',
            'informe_final_ppp' => 'Informe Final PPP',
            'anexo_7' => 'Anexo 7',
            'anexo_8' => 'Anexo 8',
            'memrandum' => 'Memorándum',
            'oficio' => 'Oficio',
            'memorandum' => 'Memorándum',
        ];

        return view('recursos.index', compact(
            'tiposPermitidos', 
            'ap', 
            'facultades', 
            'roles',
            'mapaTiposDestinatario',
            'tipoLabels'
        ));
    }

    /**
     * Guardar un nuevo recurso
     */
    public function storeRecurso(Request $request)
    {
        $user = auth()->user();
        $rolId = $user->getRolId();
        $tiposPermitidos = $this->getTiposPorRol($rolId);
        $id_semestre = session('semestre_actual_id');

        if (empty($tiposPermitidos)) {
            return back()->with('error', 'No tienes permisos para subir recursos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:' . implode(',', $tiposPermitidos),
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
            'descripcion' => 'nullable|string',
            'id_rol' => 'nullable|exists:type_users,id',
            'facultad' => 'nullable', // ID Facultad
            'escuela' => 'nullable',  // ID Escuela
            'seccion' => 'nullable',  // ID Seccion Academica
        ]);

        // Determinar Nivel y ID_SA
        $nivel = 1; // Default Global
        $id_sa_referencial = null;
        
        // Lógica de jerarquía
        if ($request->filled('seccion')) {
            $nivel = 4; // Sección
            $id_sa_referencial = $request->seccion;
        } elseif ($request->filled('escuela')) {
            $nivel = 3; // Escuela
            // Buscar un sa cualquiera de esta escuela y semestre para referencia
            $sa = seccion_academica::where('id_escuela', $request->escuela)
                ->where('id_semestre', $id_semestre)
                ->first();
            $id_sa_referencial = $sa ? $sa->id : null;
        } elseif ($request->filled('facultad')) {
            $nivel = 2; // Facultad
            // Buscar un sa cualquiera de esta facultad y semestre
            $sa = seccion_academica::where('id_facultad', $request->facultad)
                ->where('id_semestre', $id_semestre)
                ->first();
            $id_sa_referencial = $sa ? $sa->id : null;
        } else {
            $nivel = 1; // Global
            // Opcional: poner un sa cualquiera del semestre si se requiere constraint, pero es nullable
            // Dejamos null
        }
        
        // Validación extra: Si es nivel 2 o 3 y no se encontró id_sa (ej. escuela sin secciones aun), 
        // podríamos tener problemas si 'id_sa' fuera estrictamente requerido para filtrar.
        // Pero en indexRecursos usamos whereHas('seccionAcademica'), así que necesitamos que id_sa EXISTA y apunte a algo correcto.
        // Si no existe ninguna sección creada para esa escuela/facultad en este semestre, no podremos enlazarla.
        // En ese caso, creamos una falla o advertencia? 
        // Asumiremos que existen secciones. Si no, $id_sa_referencial será null y el recurso podría no aparecer en filtros que dependen de id_sa.
        
        if (($nivel == 2 || $nivel == 3) && !$id_sa_referencial) {
             return back()->with('error', 'No se encontraron secciones académicas activas para la facultad/escuela seleccionada en este semestre. No se puede vincular el recurso.');
        }


        // Obtener asignacion_persona del usuario
        $ap = asignacion_persona::where('id_persona', $user->persona->id)
            ->where('id_semestre', $id_semestre)
            ->first();

        if (!$ap) {
            // Fallback si el usuario (ej admin) no tiene asignacion en este semestre especifico
            // Ojo: Admin suele tener asignación? Si no, 'subido_por_ap' fallará por FK.
            // Asumimos que quien sube TIENE asignación.
            return back()->with('error', 'No se encontró tu asignación para este semestre.');
        }

        // Subir archivo
        $file = $request->file('archivo');
        $extension = $file->getClientOriginalExtension();
        $nombre = 'recurso_' . $request->tipo . '_' . time() . '.' . $extension;
        $ruta = $file->storeAs('recursos', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        // Crear recurso
        Recurso::create([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'ruta' => $rutaCompleta,
            'descripcion' => $request->descripcion,
            'subido_por_ap' => $ap->id,
            'id_sa' => $id_sa_referencial,
            'nivel' => $nivel,
            'id_semestre' => $id_semestre,
            'id_rol' => $request->id_rol, // Puede ser null (Todos)
            'state' => 1
        ]);

        return back()->with('success', 'Recurso subido correctamente.');
    }

    /**
     * Eliminar (desactivar) un recurso
     */
    public function destroyRecurso($id)
    {
        $user = auth()->user();
        $rolId = $user->getRolId();

        // Solo Admin y SubAdmin pueden eliminar
        if (!in_array($rolId, [1, 2])) {
            return back()->with('error', 'No tienes permisos para eliminar recursos.');
        }

        $recurso = Recurso::findOrFail($id);
        $recurso->state = 0;
        $recurso->save();

        return back()->with('success', 'Recurso eliminado correctamente.');
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

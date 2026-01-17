<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use App\Models\type_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Facultad;
use App\Models\Escuela;
use App\Models\Semestre;
use App\Models\asignacion_persona;
use App\Models\solicitud_baja;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredencialesUsuario;

class PersonaController extends Controller
{
    public function lista_usuarios(Request $request){
        $users = Persona::all();
        return view('list_users.usuarios', compact('users'));        
    }
    
    public function lista_subadmins(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $facultades = Facultad::where('state', 1)->get();

        $escuelas = Escuela::where('state', 1)->get();

        $query = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre, $request, $ap_now){
            $query->where('id_rol', 2);
            $query->where('id_semestre', $id_semestre);

            $query->whereHas('seccion_academica', function($qsa) use ($request, $ap_now){
                if($request->filled('facultad')){
                    $qsa->where('id_facultad', $request->facultad);
                }
                if($request->filled('escuela')){
                    $qsa->where('id_escuela', $request->escuela);
                }
                if($request->filled('seccion')){
                    $qsa->where('id', $request->seccion);
                }
            });
        });

        $personas = $query->with([
            'asignacion_persona' => function($q) use ($id_semestre) {
                $q->where('id_semestre', $id_semestre)
                  ->where('id_rol', 2); 
            },
            'asignacion_persona.seccion_academica.facultad',
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.seccion_academica'
        ])->get();

        $cargo = "Sub Administrador";
        $rutaFilter = "subadmin";

        return view('list_users.estudiante', compact('personas', 'facultades', 'escuelas', 'cargo', 'rutaFilter'));
    }

    public function lista_docentes(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $queryFac = Facultad::where('state', 1);
        if ($ap_now->id_rol == 2) {
            $queryFac->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $queryFac->get();


        $escuelas = Escuela::where('state', 1)->get();

        // 1. Filtrar Personas que tengan asignación de rol 3 en ESTE semestre
        $query = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre, $request, $ap_now){
            $query->where('id_rol', 3);
            $query->where('id_semestre', $id_semestre);

            $query->whereHas('seccion_academica', function($qsa) use ($request, $ap_now){
                if($request->filled('facultad')){
                    $qsa->where('id_facultad', ($ap_now->id_rol == 2) ? $ap_now->id_facultad : $request->facultad);
                }
                if($request->filled('escuela')){
                    $qsa->where('id_escuela', $request->escuela);
                }
                if($request->filled('seccion')){
                    $qsa->where('id', $request->seccion);
                }
            });
        });

        // 2. Eager Loading con FILTRO para traer solo la asignación del semestre actual
        $personas = $query->with([
            'asignacion_persona' => function($q) use ($id_semestre) {
                $q->where('id_semestre', $id_semestre)
                  ->where('id_rol', 3); 
            },
            'asignacion_persona.seccion_academica.facultad',
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.seccion_academica'
        ])->get();

        $cargo = "Docente";
        $rutaFilter = "docente";

        return view('list_users.estudiante', compact('personas', 'facultades', 'escuelas', 'cargo', 'rutaFilter'));
    }

    public function lista_supervisores(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $queryFac = Facultad::where('state', 1);
        if ($ap_now->id_rol == 2) {
            $queryFac->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $queryFac->get();

        $escuelas = Escuela::where('state', 1)->get();

        $query = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre, $ap_now, $request){
            $query->where('id_rol', 4);
            $query->where('id_semestre', $id_semestre);
            
            $query->whereHas('seccion_academica', function($qsa) use ($ap_now, $request){
                if($ap_now->id_rol == 3) {
                    $qsa->where('id', $ap_now->id_sa);
                }
                if($request->filled('facultad')){
                    $qsa->where('id_facultad', $request->facultad);
                }
                if($request->filled('escuela')){
                    $qsa->where('id_escuela', $request->escuela);
                }
                if($request->filled('seccion')){
                    $qsa->where('id', $request->seccion);
                }
            });
        });

        $personas = $query->with([
            'asignacion_persona' => function($q) use ($id_semestre) {
                $q->where('id_semestre', $id_semestre)
                  ->where('id_rol', 4); 
            },
            'asignacion_persona.seccion_academica.facultad',
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.seccion_academica'
        ])->get();

        $cargo = "Supervisor";
        $rutaFilter = "supervisor";

        return view('list_users.estudiante', compact('personas', 'facultades', 'escuelas', 'cargo', 'rutaFilter'));
    }

    public function lista_estudiantes(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $queryFac = Facultad::where('state', 1);
        if ($ap_now->id_rol == 2) {
            $queryFac->where('id', $ap_now->seccion_academica->id_facultad);
        }
        $facultades = $queryFac->get();

        Log::info('USUARIO ACTUAL: '.$ap_now);

        $escuelas = Escuela::where('state', 1)->get();

        $query = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre, $request, $ap_now){
            $query->where('id_rol', 5);
            $query->where('id_semestre', $id_semestre);
            $query->where('state', '>', 0);

            $query->whereHas('seccion_academica', function($qsa) use ($ap_now, $request){
                if($ap_now->id_rol == 2) {
                    $qsa->where('id_facultad', $ap_now->seccion_academica->id_facultad);
                }
                if($ap_now->id_rol == 3) {
                    $qsa->where('id', $ap_now->seccion_academica->id);
                }
                if($request->filled('facultad')){
                    $qsa->where('id_facultad', $request->facultad);
                }
                if($request->filled('escuela')){
                    $qsa->where('id_escuela', $request->escuela);
                }
                if($request->filled('seccion')){
                    $qsa->where('id', $request->seccion);
                }
                //$qsa->where('id', $ap_now->id_sa);
            });
        });

        $personas = $query->with([
            'asignacion_persona' => function($q) use ($id_semestre) {
                $q->where('id_semestre', $id_semestre)
                  ->where('id_rol', 5); 
            },
            'asignacion_persona.seccion_academica.facultad',
            'asignacion_persona.seccion_academica.escuela',
            'asignacion_persona.seccion_academica'
        ])->get();

        $cargo = "Estudiante";
        $rutaFilter = "estudiante";

        return view('list_users.estudiante', compact('personas', 'facultades', 'escuelas', 'cargo', 'rutaFilter'));
    }

    public function lista_grupos_estudiantes(){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        $grupo = DB::table('grupo_practica')
            ->where('id_supervisor', $ap_now->id)
            ->select('grupo_practica.name', 'grupo_practica.id')
            ->first();

        $grupo_estudiante = DB::table('grupo_estudiante as ge')
            ->join('personas as p', 'ge.id_estudiante', '=', 'p.id')
            ->join('grupo_practica as gp', 'ge.id_gp', '=', 'gp.id')
            ->where('gp.id_supervisor', $ap_now->id)
            ->select('ge.*', 'p.nombres', 'p.apellidos', 'gp.name as grupo_nombre')
            ->get();

        return view('list_users.grupo_estudiante', compact('grupo', 'grupo_estudiante'));
    }

    public function edit($id){
        $persona = Persona::findOrFail($id);
        return response()->json($persona);
    }

    public function users(){
        // Obtener el usuario logeado
        $user = auth()->user();
        
        // Obtener la persona asociada al usuario
        $persona = $user->persona;

        $facultades = Facultad::where('state', 1)->get();
        
        return view('segmento.perfil', compact('persona', 'facultades'));
    }

    public function changePasswordView()
    {
        $user = auth()->user();
        return view('segmento.change-password', compact('user'));
    }

    public function registro(){
        $user = auth()->user();
        $persona = $user->persona;
        $userRolId = $user->getRolId();
        $id_semestre_actual = session('semestre_actual_id');
        $ap = asignacion_persona::where('id_persona', $persona->id)
                                ->where('id_semestre', $id_semestre_actual)
                                ->with([
                                    'seccion_academica.facultad', 
                                    'seccion_academica.escuela', 
                                    'seccion_academica'])
                                ->first();

        $rolesQuery = type_users::where('state', 1)
            ->where('name', '!=', 'admin');
        
        if ($userRolId == 2) { // Sub Admin no puede crear otros Sub Admins
            $rolesQuery->where('name', '!=', 'sub admin');
        }

        if ($userRolId == 3) { // Docente no puede crear Sub Admins ni otros Docentes
            $rolesQuery->where('name', '!=', 'docente titular');
            $rolesQuery->where('name', '!=', 'sub admin');
        }

        $roles = $rolesQuery->get();

        $queryFac = Facultad::where('state', 1);
        if($userRolId == 2 || $userRolId == 3){ // Si es Sub Admin o Docente, filtrar por su facultad
            $queryFac->where('id', $ap->seccion_academica->id_facultad);
        }
        $facultades = $queryFac->get();

        $escuelasQuery = Escuela::where('state', 1);
        if($userRolId == 3){ // Si es Docente, filtrar también por su escuela
            $escuelasQuery->where('id', $ap->id_escuela);
        }
        $escuelas = $escuelasQuery->get();

        $semestres = Semestre::where('state', 1)->orderBy('ciclo', 'desc')->get();
            
        return view('segmento.registrar', compact('roles', 'facultades', 'escuelas', 'persona', 'semestres', 'ap'));
    }

    public function getEscuelas($facultad_id){
        $escuelas = Escuela::where('facultad_id', $facultad_id)
            ->where('estado', 1)
            ->get();

        return response()->json($escuelas);
    }

    public function getDocentesTitulares($id_sa) {
        try {
            $docentes = DB::table('personas')
                ->join('asignacion_persona as ap', 'personas.id', '=', 'ap.id_persona')
                ->leftJoin('grupo_practica as gp', 'ap.id', '=', 'gp.id_docente')
                ->where('ap.id_rol', 3) // Rol docente titular
                ->where('ap.id_sa', $id_sa)
                ->where('ap.state', 1)
                ->select('ap.id as id', 'personas.nombres as nombres', 'personas.apellidos as apellidos')
                ->get();

            return response()->json($docentes);
            
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al cargar docentes'], 500);
        }
    }

    public function getDocentesSupervisores($id_sa) {
        try {
            $docentes = DB::table('personas')
                ->join('asignacion_persona as ap', 'personas.id', '=', 'ap.id_persona')
                ->leftJoin('grupo_practica as gp', 'ap.id', '=', 'gp.id_supervisor')
                ->where('ap.id_rol', 4) // Rol docente titular
                ->where('ap.id_sa', $id_sa)
                ->where('ap.state', 1)
                ->whereNull('gp.id') // Solo docentes sin grupo asignado
                ->select('ap.id as id', 'personas.nombres as nombres', 'personas.apellidos as apellidos')
                ->get();

            return response()->json($docentes);
            
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al cargar docentes'], 500);
        }
    }

    public function destroy($id){
        $persona = Persona::findOrFail($id);
        $persona->delete();
        
        return redirect()->back()->with('success', 'Persona eliminada correctamente.');
    }

    public function verificar($email) {
        try {
            Log::info('Email recibido: ' . $email);
            $persona = Persona::where('correo_inst', $email)->first();
            if ($persona) {
                // El usuario existe, ahora verificamos si ya está asignado a este semestre
                $asignacionExistente = asignacion_persona::where('id_persona', $persona->id)
                    ->where('id_semestre', session('semestre_actual_id'))
                    ->exists();

                Log::info('Persona encontrada:'.$persona);

                // tambien enviar nombre Facultad, Escuela y Seccion
                $ap = asignacion_persona::where('id_persona', $persona->id)
                    ->where('id_semestre', session('semestre_actual_id'))
                    ->with([
                        'seccion_academica.facultad', 
                        'seccion_academica.escuela', 
                        'seccion_academica'])
                    ->first();

                return response()->json([
                    'persona' => $persona,
                    'asignacionExistente' => $asignacionExistente,
                    'ap' => $ap
                ]);
            }
            return response()->json([
                'persona' => null,
                'asignacionExistente' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar persona: ' . $e->getMessage());
            return response()->json(['error' => 'Error al verificar persona: ' . $e->getMessage(), 500]);
        }
    }
    
    public function store(Request $request){
        $persona_id = $request->input('persona_id');
        
        $request->validate([
            'rol' => 'required|exists:type_users,id',
            'id_semestre' => 'required|exists:semestres,id',
            'facultad' => 'required|exists:facultades,id',
            //'escuela' => 'required|exists:escuelas,id'
        ]);

        try {
            if (empty($persona_id)) {
                $persona = $this->crearNuevaPersona($request);
                $persona_id_final = $persona->id;
                $success_message = 'Persona creada y asignada al semestre actual correctamente.';
            } else {
                $persona = Persona::find($persona_id);
                $persona_id_final = $persona_id;
                $success_message = 'Usuario existente asignado al semestre actual correctamente.';
            }

            $success = $this->asignarPersonaASemestre($persona_id_final, $request);
            // Enviar correo de confirmación de asignación (siempre, ya que es por semestre)
            if ($success) {
                /*try {
                    $semestre = Semestre::find($request->id_semestre);
                    $password = ($request->rol == 5) ? $persona->codigo : '12345678';
                    
                    Mail::to($persona->correo_inst)->send(new CredencialesUsuario([
                        'nombre' => $persona->nombres . ' ' . $persona->apellidos,
                        'usuario' => $persona->codigo,
                        'password' => $password,
                        'semestre' => $semestre ? $semestre->codigo : 'Actual',
                        'mensaje_extra' => ($request->rol == 5) ? 'Recuerde que su usuario es su código de estudiante.' : 'Si ya ha cambiado su contraseña anteriormente, ignore el campo de password.'
                    ]));
                    $success_message .= ' (Nota: El correo de confirmación llegará en breve)';
                } catch (\Exception $e) {
                    Log::error('Error al encolar correo de confirmación: ' . $e->getMessage());
                    $success_message .= ' (Nota: El correo de confirmación llegará en breve)';
                }*/
            }

            return back()->with('success', $success_message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Devolver los errores de validación a la vista anterior con los datos de entrada.
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Capturar cualquier otro error y devolverlo a la vista.
            Log::error('Error en PersonaController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al registrar. Por favor, intente de nuevo.');
        }
    }

    private function crearNuevaPersona(Request $request)
    {
        $validatedData = $request->validate([
            'dni' => 'nullable|string|size:8|unique:personas,dni',
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'celular' => 'nullable|string|size:9',
            'correo_inst' => 'required|email|max:150|unique:personas,correo_inst',
            'sexo' => 'required|in:M,F',
            'provincia' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:50'
        ]);

        $username = explode('@', $validatedData['correo_inst'])[0];
        $isStudent = $request->input('rol') == 5;

        $user = User::create([
            'name' => $username,
            'email' => $validatedData['correo_inst'],
            'password' => Hash::make($isStudent ? $request->codigo : '12345678'),
        ]);

        return Persona::create([
            'codigo' => $username,
            'usuario_id' => $user->id,
            'departamento' => 'Lima Provincias',
            'state' => 1
        ] + $validatedData);
    }

    private function asignarPersonaASemestre($persona_id, Request $request)
    {
        $asignacionExistente = asignacion_persona::where('id_persona', $persona_id)
            ->where('id_semestre', $request->id_semestre)
            ->first();

        if ($asignacionExistente) {
            throw new \Exception('Esta persona ya tiene una asignación en este semestre.');
        }

        $rol = $request->rol;
        $is_admin_or_subadmin = in_array($rol, [1]);
        $is_doc_or_sup = in_array($rol, [3, 4]);

        return asignacion_persona::create([
            'id_semestre' => $request->id_semestre,
            'id_persona' => $persona_id,
            'id_rol' => $rol,
            'id_sa' => $is_admin_or_subadmin ? null :$request->seccion,
            'state' => $is_doc_or_sup ? 2 : 1 // 2: pendiente, 1: activo
        ]);
    }

    public function store_masivo(Request $request){
        $request->validate([
            'archivo' => 'file|mimes:csv,txt|max:2048',
            'rol' => 'exists:type_users,id',
            'escuela' => 'exists:escuelas,id',
        ]);

        $id_semestre = session('semestre_actual_id');

        $rol = $request->rol;
        
        // log de facultad, escuela, seccion
        Log::info('Facultad: ' . $request->facultad);
        
        try {
            $archivo = $request->file('archivo');
            $contenido = file($archivo->path());
            
            if($rol == 5){
                $this->formatoRegistroEstudiante($request, $rol, $id_semestre);
            } else {
                $this->formatoRegistroAdministrativo($request, $rol, $id_semestre);
            }
            
            return back()->with('success', 'Formulario de Trámite (FUT) subido correctamente.');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function formatoRegistroAdministrativo($request, $rol, $id_semestre) {
        Log::info('Formato de registro administrativo');
        $contenido = file($request->file('archivo')->path());
        array_shift($contenido); // Línea 1
        $segundaFila = str_getcsv(array_shift($contenido));

        //array_shift($contenido); // Línea 2
        $headers = str_getcsv(array_shift($contenido));
        Log::info('Headers: ' . json_encode($headers));
        $campoMap = [
            'codigo' => 'codigo',
            'persona' => 'nombres',
            'correo_inst' => 'correo_inst'
        ];

        $usuariosCreados = 0;
        $errores = [];

        foreach ($contenido as $linea) {
            $datos = str_getcsv($linea);

            if (count($datos) !== count($headers)) {
                $errores[] = "Formato incorrecto en la línea " . ($usuariosCreados + 1);
                continue;
            }

            $usuarioData = [];
            foreach ($headers as $index => $header) {
                if (isset($campoMap[$header])) {
                    if($header === 'persona') {
                        $nombresCompletos = $datos[$index];
                        $partes = explode(' ', $nombresCompletos);
                        
                        $usuarioData['apellidos'] = implode(' ', array_slice($partes, 0, 2));
                        $usuarioData['nombres'] = implode(' ', array_slice($partes, 2));

                        Log::info('Nombres: ' . $usuarioData['nombres'] . ', Apellidos: ' . $usuarioData['apellidos']);
                    } else {
                        $usuarioData[$campoMap[$header]] = $datos[$index];
                    }
                }
            }

            try {
                $this->createPersonaMasivo($usuarioData, $rol, $id_semestre, $request->seccion);
                $usuariosCreados++;
            } catch (\Exception $e) {
                $errores[] = "Error al crear usuario en la línea " . ($usuariosCreados + 1) . ": " . $e->getMessage();
            }
        }
    }

    public function formatoRegistroEstudiante($request, $rol, $id_semestre) {
        $contenido = file($request->file('archivo')->path());
        array_shift($contenido); // Línea 1
        $segundaFila = str_getcsv(array_shift($contenido));

        $nombreDocente = $segundaFila[0];
        $escuela = $segundaFila[1];
        $seccion = $segundaFila[2];
    
        array_shift($contenido); // Línea 3
        $headers = str_getcsv(array_shift($contenido));

        $campoMap = [
            'CodigoUniversitario' => 'codigo',
            'Alumno' => 'nombres',
            'Textbox4' => 'correo_inst'
        ];
        
        $usuariosCreados = 0;
        $errores = [];

        foreach ($contenido as $linea) {
            $datos = str_getcsv($linea);
            
            if (count($datos) !== count($headers)) {
                $errores[] = "Formato incorrecto en la línea " . ($usuariosCreados + 1);
                continue;
            }

            $usuarioData = [];
            foreach ($headers as $index => $header) {
                if (isset($campoMap[$header])) {
                    if ($header === 'Alumno') {
                        // Separar apellidos y nombres
                        $nombresCompletos = $datos[$index];
                        $partes = explode(' ', $nombresCompletos);
                        
                        // Tomar las dos primeras palabras como apellidos
                        $apellidos = implode(' ', array_slice($partes, 0, 2));
                        // Tomar el resto como nombres
                        $nombres = implode(' ', array_slice($partes, 2));
                        
                        $usuarioData['apellidos'] = $apellidos;
                        $usuarioData['nombres'] = $nombres;

                        Log::info('Nombres: ' . $nombres . ', Apellidos: ' . $apellidos);
                    } else {
                        $usuarioData[$campoMap[$header]] = $datos[$index];
                    }
                }
            }

            try {
                // Crear usuario
                // Asegurar que el código tenga formato correcto (máx 10 chars)
                // Si viene sin cero a la izquierda y tiene 9 digitos, se le agrega.
                // Si ya tiene 10, se deja igual. Si tiene más, se corta.
                
                $rawCodigo = trim($usuarioData['codigo']);
                if (strlen($rawCodigo) < 10) {
                    $usuarioData['codigo'] = str_pad($rawCodigo, 10, '0', STR_PAD_LEFT);
                } else {
                    $usuarioData['codigo'] = substr($rawCodigo, 0, 10);
                }

                $this->createPersonaMasivo($usuarioData, $rol, $id_semestre, $request->seccion);
                $usuariosCreados++;
            } catch (\Exception $e) {
                Log::error("Error al procesar línea " . ($usuariosCreados + 1) . ": " . $e->getMessage());
                $errores[] = "Error al crear usuario en la línea " . ($usuariosCreados + 1) . ": " . $e->getMessage();
            }
        }
    }

    public function createPersonaMasivo($usuarioData, $rol, $id_semestre, $seccion) {
        // 1. Buscar o Crear Usuario (Evita error por duplicado)
        $user = User::where('email', $usuarioData['correo_inst'])->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $usuarioData['codigo'],
                'email' => $usuarioData['correo_inst'],
                'password' => Hash::make(($rol != 5) ? '12345678' : $usuarioData['codigo']),
            ]);
            //Log::info('Usuario nuevo creado: ' . $user->id);
        } else {
            //Log::info('Usuario ya existía: ' . $user->id);
        }

        // 2. Buscar o Crear Persona
        $persona = Persona::where('usuario_id', $user->id)->first();
        
        // Fallback: buscar por correo si no se encuentra por usuario_id
        if (!$persona) {
            $persona = Persona::where('correo_inst', $usuarioData['correo_inst'])->first();
        }

        if (!$persona) {
            //Log::info("Intentando crear persona. Código: " . $usuarioData['codigo'] . " (Len: " . strlen($usuarioData['codigo']) . ")");
            
            $persona = new Persona([
                'codigo' => $usuarioData['codigo'],
                'nombres' => $usuarioData['nombres'],
                'apellidos' => $usuarioData['apellidos'],
                'correo_inst' => $usuarioData['correo_inst'],
                'departamento' => 'Lima Provincias',
                'usuario_id' => $user->id,
                'date_create' => now(),
                'date_update' => now(),
                'state' => 1
            ]);
            
            //Log::info("Objeto persona instanciado. Guardando...");
            $persona->save();
            //Log::info('Persona nueva creada: ' . $persona->id);
        } else {
            //Log::info('Persona ya existía: ' . $persona->id);
            // Asegurar que esté vinculada al usuario
            if ($persona->usuario_id !== $user->id) {
                $persona->usuario_id = $user->id;
                $persona->save();
            }
        }

        // 3. Crear Asignación (Si no existe para este semestre)
        $asignacion = asignacion_persona::where('id_persona', $persona->id)
                                        ->where('id_semestre', $id_semestre)
                                        ->first();

        if (!$asignacion) {
            $asignacionPersona = new asignacion_persona([
                'id_persona' => $persona->id,
                'id_rol' => $rol,
                'id_semestre' => $id_semestre,
                'id_sa' => ($rol != 1) ? $seccion : null,
                'date_create' => now(),
                'date_update' => now(),
                'state' => 2
            ]);
            $asignacionPersona->save();
            Log::info('Asignación creada correctamente para: ' . $persona->nombres);
        } else {
            Log::info('El usuario ya estaba asignado a este semestre.');
        }
    }

    public function checkDni($dni){
        $persona = Persona::where('dni', $dni)->first();
        return response()->json([
            'exists' => !is_null($persona)
        ]);
    }

    public function checkEmail($email){
        $persona = Persona::where('correo_inst', $email)->first();
        return response()->json([
            'exists' => !is_null($persona)
        ]);
    }

    public function getDataPersona($id){
        $persona = Persona::where('id', $id)->first();
        return response()->json([
            'persona' => $persona
        ]);
    }

    public function update(Request $request){
        try {
            $persona = Persona::findOrFail($request->persona_id);
    
            /*$request->validate([
                'nombres' => 'required|string|max:50',
                'apellidos' => 'required|string|max:50',
                'dni' => 'required|string|size:8|unique:personas,dni,' . $persona->id,
                'celular' => 'nullable|string|size:9',
                'sexo' => 'required|in:M,F',
                'provincia' => 'nullable|string|max:50',
                'distrito' => 'nullable|string|max:50',
                'departamento' => 'nullable|string|max:50',
                'ruta_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            ]);*/
    
            $data = $request->only([
                'nombres', 'apellidos', 'dni', 'celular', 'sexo', 'provincia', 'distrito', 'departamento'
            ]);
            
            // Filter out null values (disabled inputs don't send data, but 'only' fills them with null)
            $data = array_filter($data, function($value) { return !is_null($value); });
            $data['date_update'] = now();
        
            if ($request->hasFile('ruta_foto')) {
                $nombre = 'foto_' . $persona->id . '_' . time() . '.' . $request->file('ruta_foto')->getClientOriginalExtension();
                $ruta = $request->file('ruta_foto')->storeAs('fotos', $nombre, 'public');
                $data['ruta_foto'] = 'storage/' . $ruta;
            }
        
            if (!empty($data)) {
                $persona->update($data);
            }

            // Update Assignment if provided and allowed
            if ($request->filled('seccion_id')) {
                $ap = $persona->asignacion_persona; // Assumes model relationship uses hasOne or similar
                if ($ap && $ap->state != 1) { // Only if not validated
                    $ap->id_sa = $request->seccion_id;
                    $ap->save();
                }
            }
    
            return back()->with('success', 'Perfil actualizado correctamente.');
    
        } catch (\Exception $e) {
            Log::error("Error al actualizar persona: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al actualizar: ' . $e->getMessage());
        }
    }

    public function storeFoto(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'foto' => 'required|file|mimes:jpg,jpeg,png|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'foto_' . $personaId . '_' . time() . '.' . $request->file('foto')->getClientOriginalExtension();
        $ruta = $request->file('foto')->storeAs('fotos', $nombre, 'public');

        // Buscar o crear la matrícula
        $persona = Persona::findOrFail($personaId);
        $persona->update([
            'ruta_foto' => 'storage/' . $ruta,
        ]);

        return back()->with('success', 'Foto subida correctamente.');
    }

    // actualizar contraseña
    public function updatePassword(Request $request){
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        $user = auth()->user();

        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'La contraseña actual que ingresaste es incorrecta.');
        }

        // Verificar que la nueva contraseña no sea igual a la actual
        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('error', 'La nueva contraseña no puede ser igual a la actual.');
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->password_changed_at = now(); // Marcar la contraseña como cambiada
        $user->save();

        return back()->with('success', '¡Tu contraseña ha sido actualizada exitosamente!');
    }

    public function disabledPersona($id){
        $persona = Persona::findOrFail($id);
        $persona->state = 0;
        $persona->save();
        return back()->with('success', 'Persona deshabilitada correctamente.');
    }

    public function enabledPersona($id){
        $persona = Persona::findOrFail($id);
        $persona->state = 1;
        $persona->save();
        return back()->with('success', 'Persona habilitada correctamente.');
    }

    public function disabledAsignacion(Request $request){
        $id_semestre = session('semestre_actual_id');
        $authUser = auth()->user();

        $ap_now = $authUser->persona->asignacion_persona;

        if($request->opcion == 1){
            $opcion = 'deshabilitar';
        }else if($request->opcion == 2){
            $opcion = 'eliminar';
        }else if($request->opcion == 3){
            $opcion = 'habilitar';
        }

        $solicitud_baja = solicitud_baja::create([
            'id_ap_delete' => $request->id_ap,
            'id_sa' => $request->id_sa,
            'id_ap_sol' => $ap_now->id,
            'justification_sol' => $request->comentario,
            'tipo_sol' => $opcion,
            'estado_sol' => 'pendiente',
            'state' => 0,
        ]);
        if($request->opcion != 3){
            $asignacion = asignacion_persona::findOrFail($request->id_ap);
            $asignacion->state = 3;
            $asignacion->save();
        }
        return back()->with('success', 'Asignación '.$opcion.' correctamente.');
    }

    public function enabledAsignacion($id){
        $asignacion = asignacion_persona::findOrFail($id);
        $asignacion->state = 1;
        $asignacion->save();
        return back()->with('success', 'Asignación habilitada correctamente.');
    }

    public function getSolicitudBaja($id_ap, $id_sa){
        $solicitud_baja = solicitud_baja::where('id_ap_delete', $id_ap)->where('id_sa', $id_sa)
            ->latest()
            ->first();
        return $solicitud_baja;
    }

    public function solicitudBaja(Request $request){
        Log::info($request->all());
        $solicitud_baja = solicitud_baja::where('id', $request->id_sol)->first();
        $solicitud_baja->estado_sol = ($request->estado == 1) ? 'aceptado' : 'rechazado';
        $solicitud_baja->comentario_admin = $request->comentario;
        $solicitud_baja->save();

        if($request->estado == 1){
            $ap = asignacion_persona::where('id', $solicitud_baja->id_ap_delete)->first();
            if($solicitud_baja->tipo_sol == 'deshabilitar'){
                $ap->state = 4;
                $ap->save();
            }else if($solicitud_baja->tipo_sol == 'eliminar'){
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
                    // Tiene otras asignaciones, solo cambiar state a 0
                    $ap->state = 0;
                    $ap->save();
                }
            }else if($solicitud_baja->tipo_sol == 'habilitar'){
                $ap->state = 1;
                $ap->save();
            }
        }
        if($request->estado == 2){
            $ap = asignacion_persona::where('id', $solicitud_baja->id_ap_delete)->first();
            $ap->state = 1;
            $ap->save();
        }
        return back()->with('success', 'Solicitud de baja enviada correctamente.');
    }

    public function getPersonaForEdit($id){
        $id_semestre = session('semestre_actual_id');
        
        $persona = Persona::with(['asignacion_persona' => function($q) use ($id_semestre) {
            $q->where('id_semestre', $id_semestre)->with(['seccion_academica.facultad', 'seccion_academica.escuela']);
        }])->find($id);
    
        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }
        
        // 'with' devuelve una colección. Necesitamos el objeto único de la asignación para el semestre actual.
        $asignacion = $persona->asignacion_persona->first();
        
        // Limpiamos la colección y establecemos el objeto único para facilitar el uso en el frontend.
        unset($persona->asignacion_persona);
        $persona->asignacion_persona = $asignacion;
    
        return response()->json($persona);
    }
}

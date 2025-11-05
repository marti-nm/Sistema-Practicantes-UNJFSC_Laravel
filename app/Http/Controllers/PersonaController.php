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
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{
    public function lista_docentes(){
        // Obtener lista de docentes del semestre actual
        $id_semestre = session('semestre_actual_id');
        $personas = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre){
            $query->where('id_rol', 3);
            $query->where('id_semestre', $id_semestre);
        })->get();
        $facultades = Facultad::where('estado', 1)->get();
        $escuelas = Escuela::where('estado', 1)->get();
        return view('list_users.docente', compact('personas', 'facultades', 'escuelas'));
    }

    public function lista_supervisores(){
        $id_semestre = session('semestre_actual_id');
        $personas = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre){
            $query->where('id_rol', 4);
            $query->where('id_semestre', $id_semestre);
            if(auth()->user()->getRolId() == 3){
                $query->where('id_escuela', auth()->user()->persona->asignacion_persona->id_escuela);
            }
            
        })->get();
        $facultades = Facultad::where('estado', 1)->get();
        $escuelas = Escuela::where('estado', 1)->get();
        return view('list_users.supervisor', compact('personas', 'facultades', 'escuelas'));
    }

    public function lista_estudiantes(){
        $id_semestre = session('semestre_actual_id');
        $personas = Persona::whereHas('asignacion_persona', function($query) use ($id_semestre){
            $query->where('id_rol', 5);
            $query->where('id_semestre', $id_semestre);
            if(auth()->user()->getRolId() == 3){
                $query->where('id_escuela', auth()->user()->persona->asignacion_persona->id_escuela);
            }
        })->get();
        $facultades = Facultad::where('estado', 1)->get();
        $escuelas = Escuela::where('estado', 1)->get();
        return view('list_users.estudiante', compact('personas', 'facultades', 'escuelas'));
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
        
        return view('segmento.perfil', compact('persona'));
    }

    public function registro(){
        $user = auth()->user();
        $persona = $user->persona;
        // Si la persona autenticada es rol 2 (docente), excluir también el tipo 'docente titular'
        $rolesQuery = type_users::where('estado', 1)
            ->where('name', '!=', 'admin');

        if ($user->getRolId() == 3) {
            $rolesQuery->where('name', '!=', 'docente titular');
            $rolesQuery->where('name', '!=', 'sub admin');
        }

        $roles = $rolesQuery->get();
        $facultades = Facultad::where('estado', 1)->get();
        $escuelas = Escuela::where('estado', 1)->get();
        $semestres = Semestre::where('estado', 1)->orderBy('ciclo', 'desc')->get();
            
        return view('segmento.registrar', compact('roles', 'facultades', 'escuelas', 'persona', 'semestres'));
    }

    public function getEscuelas($facultad_id){
        $escuelas = Escuela::where('facultad_id', $facultad_id)
            ->where('estado', 1)
            ->get();

        return response()->json($escuelas);
    }

    public function destroy($id){
        $persona = Persona::findOrFail($id);
        $persona->delete();
        
        return redirect()->back()->with('success', 'Persona eliminada correctamente.');
    }

    public function verificar(Request $request) {
        $type = $request->input('type');
        $value = $request->input('value');
        $semestre_id = $request->input('semestre_id');

        $persona = Persona::where($type, $value)->first();

        if ($persona) {
            // El usuario existe, ahora verificamos si ya está asignado a este semestre
            $asignacionExistente = asignacion_persona::where('id_persona', $persona->id)
                ->where('id_semestre', $semestre_id)
                ->exists();

            return response()->json([
                'found' => true,
                'already_assigned' => $asignacionExistente,
                'persona' => $persona
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function asignar(Request $request){
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'rol' => 'required|exists:type_users,id',
            'semestre' => 'required|exists:semestres,id',
            'facultad' => 'nullable|exists:facultades,id',
            'escuela' => 'nullable|exists:escuelas,id',
        ]);

        // Verificar si ya existe una asignación para evitar duplicados por si acaso
        $asignacionExistente = asignacion_persona::where('id_persona', $request->persona_id)
            ->where('id_semestre', $request->semestre)
            ->first();

        if ($asignacionExistente) {
            return back()->with('error', 'Esta persona ya tiene una asignación en este semestre.');
        }

        try {
            asignacion_persona::create([
                'id_semestre' => $request->semestre,
                'id_persona' => $request->persona_id,
                'id_rol' => $request->rol,
                'id_escuela' => in_array($request->rol, [1, 2]) ? null : $request->escuela,
                'id_facultad' => $request->rol == 1 ? null : $request->facultad,
                'date_create' => now(),
                'date_update' => now(),
                'estado' => 1
            ]);
            return back()->with('success', 'Usuario asignado al semestre actual correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar el usuario: ' . $e->getMessage());
        }
    }

    public function registrarPersona(){
        $request -> validate([
            'codigo' => 'required|string|size:10|unique:personas,codigo',
            'dni' => 'required|string|size:8|unique:personas,dni',
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'celular' => 'nullable|string|size:9',
            'correo_inst' => 'nullable|email|max:150|unique:personas,correo_inst',
            'sexo' => 'nullable|in:M,F',
            'provincia' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:50'
        ]);
        try {
            $persona = new Persona([
                'codigo' => $request->codigo,
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'correo_inst' => $request->correo_inst,
                'sexo' => $request->sexo,
                'provincia' => $request->provincia,
                'distrito' => $request->distrito,
                'date_create' => now(),
                'date_update' => now(),
                'estado' => 1
            ]);
            $persona->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function store(Request $request){
        $persona_id = $request->input('persona_id');

        $request->validate([
            'rol' => 'required|exists:type_users,id',
            'id_semestre' => 'required|exists:semestres,id',
            'facultad' => 'required|exists:facultades,id',
            'escuela' => 'required|exists:escuelas,id',
        ]);

        try {
            $persona_id_final = $persona_id;
            $success_message = '';
            $persona = null;

            if (empty($persona_id)) {
                Log::info('Datos recibidos en el controller:', $request->all());
                $request->validate([
                    'codigo' => 'required|string|size:10|unique:personas,codigo',
                    'dni' => 'required|string|size:8|unique:personas,dni',
                    'nombres' => 'required|string|max:50',
                    'apellidos' => 'required|string|max:50',
                    'celular' => 'nullable|string|size:9',
                    'correo_inst' => 'nullable|email|max:150|unique:personas,correo_inst',
                    'sexo' => 'nullable|in:M,F',
                    'provincia' => 'nullable|string|max:50',
                    'distrito' => 'nullable|string|max:50'
                ]);
                
                $correo_inst = $request->correo_inst ?: $request->codigo . '@unjfsc.edu.pe';
                $sexo = $request->sexo ?: 'M';

                $user = User::create([
                    'name' => $request->codigo,
                    'email' => $correo_inst,
                    'password' => Hash::make($request->codigo),
                ]);

                $persona = Persona::create([
                    'codigo' => $request->codigo,
                    'dni' => $request->dni,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'celular' => $request->celular,
                    'sexo' => $sexo,
                    'correo_inst' => $correo_inst,
                    'departamento' => 'Lima Provincias',
                    'provincia' => $request->provincia,
                    'distrito' => $request->distrito,
                    'usuario_id' => $user->id,
                    'date_create' => now(),
                    'date_update' => now(),
                    'estado' => 1
                ]);


                $persona_id_final = $persona->id;
                $success_message = 'Persona creada y asignada al semestre correctamente.';
            } else {
                $persona_id_final = $persona_id;
                $success_message = 'Usuario existente asignado correctamente.';
            }

            $asignacionExistente = asignacion_persona::where('id_persona', $persona_id_final)
                ->where('id_semestre', $request->id_semestre)
                ->first();

            if ($asignacionExistente) {
                return back()->with('error', 'Esta persona ya tiene una asignación en este semestre.');
            }

            $is_admin_or_subadmin = in_array($request->rol, [1, 2]);
            $is_doc_or_sup = in_array($request->rol, [3, 4]);

            asignacion_persona::create([
                'id_semestre' => $request->id_semestre,
                'id_persona' => $persona_id_final,
                'id_rol' => $request->rol,
                'id_escuela' => $is_admin_or_subadmin ? null : $request->escuela,
                'id_facultad' => $request->rol == 1 ? null : $request->facultad,
                'date_create' => now(),
                'date_update' => now(),
                'estado' => $is_doc_or_sup ? 2 : 1
            ]);

            return back()->with('success', $success_message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // MUY IMPORTANTE: Devolver la excepción de validación para que Laravel la maneje.
            // Si no la devuelves, se va al catch general y pierdes el detalle del error.
            throw $e; 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store_masivo(Request $request){
        $request->validate([
            'archivo' => 'file|mimes:csv,txt|max:2048',
            'rol' => 'exists:type_users,id',
            'escuela' => 'exists:escuelas,id',
        ]);

        try {
            $archivo = $request->file('archivo');
            $contenido = file($archivo->path());
            
            // Saltar las primeras 3 líneas (headers y datos no necesarios)
            array_shift($contenido); // Línea 1
            array_shift($contenido); // Línea 2
            array_shift($contenido); // Línea 3
            
            // Obtener los headers de la línea 4
            $headers = str_getcsv(array_shift($contenido));
            
            // Mapear los campos del CSV a los campos de la base de datos
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

                // Crear un array con los datos mapeados
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
                        } else {
                            $usuarioData[$campoMap[$header]] = $datos[$index];
                        }
                    }
                }

                try {
                    // Crear usuario
                    $user = User::create([
                        'name' => $usuarioData['codigo'],
                        'email' => $usuarioData['correo_inst'],
                        'password' => Hash::make($usuarioData['codigo']),
                    ]);

                    // Crear persona
                    $persona = new Persona([
                        'codigo' => $usuarioData['codigo'],
                        'nombres' => $usuarioData['nombres'],
                        'apellidos' => $usuarioData['apellidos'],
                        'correo_inst' => $usuarioData['correo_inst'],
                        'departamento' => 'Lima Provincias',
                        'usuario_id' => $user->id,
                        'rol_id' => $request->rol, // Usar el ID del rol seleccionado
                        'date_create' => now(),
                        'date_update' => now(),
                        'estado' => 1,
                        'id_escuela' => $request->escuela,
                    ]);

                    $persona->save();
                    $usuariosCreados++;
                } catch (\Exception $e) {
                    $errores[] = "Error al crear usuario en la línea " . ($usuariosCreados + 1) . ": " . $e->getMessage();
                }
            }

            return back()->with('success', 'Formulario de Trámite (FUT) subido correctamente.');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
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

    public function update(Request $request){
        $persona = Persona::findOrFail($request->persona_id);

        /*$validated = $request->validate([
            'codigo' => 'nullable|string|size:10',
            'nombres' => 'nullable|string|max:50',
            'apellidos' => 'nullable|string|max:50',
            'dni' => 'nullable|string|size:8|unique:personas,dni,' . $id,
            'celular' => 'nullable|string|size:9',
            'correo_inst' => 'nullable|email|max:150|unique:personas,correo_inst,' . $id,
            'sexo' => 'in:M,F',
            'provincia' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:50',
        ]);*/

        try {
            $data = [
                'codigo' => $request->codigo,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'celular' => $request->celular,
                'sexo' => $request->sexo,
                'correo_inst' => $request->correo_inst,
                'provincia' => $request->provincia,
                'distrito' => $request->distrito,
                'date_update' => now(),
            ];
        
            // Solo actualizar id_escuela si viene en el request
            if ($request->filled('escuela')) {
                $data['id_escuela'] = $request->escuela;
            }
        
            $persona->update($data);

            return back()->with('success', 'Persona actualizada correctamente.');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la persona: ' . $e->getMessage()
            ], 500);
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
}

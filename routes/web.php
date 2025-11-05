<?php

use App\Http\Controllers\adminDashboardController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\asginacionController;
use App\Http\Controllers\cerrarSesionController;
use App\Http\Controllers\DashboardDocenteController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\facultadController;
use App\Http\Controllers\escuelaController;
use App\Http\Controllers\grupoEstudianteController;
use App\Http\Controllers\matriculaController;
use App\Http\Controllers\semestreController;
use App\Http\Controllers\supervisorDashboardController;
use App\Http\Controllers\AcreditarController;
use App\Http\Controllers\validacionMatriculaController;
use App\Http\Controllers\evaluacionController;
use App\Http\Requests\StoreFacultadRequest;
use App\Http\Requests\StoreEscuelaRequest;
use App\Http\Requests\StoreSemestreRequest;
use App\Http\Controllers\preguntaController;
use App\Http\Controllers\respuestaController;
use App\Models\grupo_estudiante;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PracticaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\estudianteDashboardController;
use App\Http\Controllers\JefeInmediatoController;
use App\Http\Controllers\panelPrincipal;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/  


Route::get('/', [loginController::class, 'index']);
Route::get('/panel', [panelPrincipal::class, 'dashboard'])->middleware('auth')->name('panel');
Route::get('/login', [loginController::class, 'index'])->name('login');
Route::post('/login', [loginController::class, 'login']);
Route::get('/cerrarSecion', [cerrarSesionController::class, 'cerrarSecion'])->name('cerrarSecion');
Route::get('/estudiantes', [homeController::class, 'index_estudiante'])->middleware('auth')->name('panel.estudiantes');

// ... otras rutas ...

Route::get('/segmento/perfil', [PersonaController::class, 'users'])->middleware('auth')->name('perfil');

// Rutas para personas
Route::get('/personas/check-dni/{dni}', [PersonaController::class, 'checkDni'])->middleware('auth')->name('personas.check.dni');
Route::get('/personas/check-email/{email}', [PersonaController::class, 'checkEmail'])->middleware('auth')->name('personas.check.email');

// Ruta para la carga masiva de usuarios
Route::post('/segmento/usuarios-masivos', [PersonaController::class, 'store_masivo'])->name('usuarios.masivos.store');

Route::get('/segmento/registrar', [PersonaController::class, 'registro'])->middleware('auth')->name('registrar');

Route::post('/segmento/registrar', [PersonaController::class, 'store'])->name('personas.store');

Route::get('/escuelas/{facultad_id}', [PersonaController::class, 'getEscuelas']);

Route::get('/list_users/modal-editar', function () {
    return view('list_users.edit_persona');
})->middleware('auth')->name('modal.editar');

Route::post('/personas/verificar', [PersonaController::class, 'verificar'])->middleware('auth')->name('personas.verificar');
Route::post('/personas/asignar', [PersonaController::class, 'asignar'])->middleware('auth')->name('personas.asignar');

Route::post('/persona/editar', [PersonaController::class, 'update'])->middleware('auth')->name('persona.editar');

Route::get('/list_users/docente', [PersonaController::class, 'lista_docentes'])->middleware('auth')->name('docente');

Route::get('/list_users/estudiante', [PersonaController::class, 'lista_estudiantes'])->middleware('auth')->name('estudiante');

Route::get('/list_users/supervisor', [PersonaController::class, 'lista_supervisores'])->middleware('auth')->name('supervisor');

Route::delete('/personas/{id}', [PersonaController::class, 'destroy'])->middleware('auth')->name('personas.destroy');

// Ruta para obtener los datos de un docente
Route::get('/personas/{id}', [PersonaController::class, 'edit'])->middleware('auth')->name('personas.edit');

//Bloque Academico
Route::resource('facultad',facultadController::class);

Route::resource('escuela',escuelaController::class);

Route::resource('semestre',semestreController::class);
Route::get('/semestre/{semestre}/edit', [SemestreController::class, 'edit'])->name('semestre.edit');

// Evaluación
Route::resource('evaluacion', EvaluacionController::class);
Route::post('/evaluacion/store-anexos', [EvaluacionController::class, 'storeAnexos'])->name('evaluacion.storeAnexos');
Route::post('/evaluacion/store-entrevista', [EvaluacionController::class, 'storeEntrevista'])->name('evaluacion.storeEntrevista');

//Preguntas
Route::resource('pregunta', preguntaController::class);


//Respuestas
Route::post('/respuestas', [respuestaController::class, 'store'])->name('respuestas.store');


Route::get("/matricula", [matriculaController::class, "index" ])->middleware('auth')->name("matricula_index");
Route::get("/matricula/estudiante", [matriculaController::class, "modal" ])->middleware('auth')->name("matricula_modal");
Route::post('/subir/ficha', [ArchivoController::class, 'subirFicha'])->middleware('auth')->name('subir.ficha');
Route::post('/subir/record', [ArchivoController::class, 'subirRecord'])->middleware('auth')->name('subir.record');
Route::post('/subir/clectiva', [ArchivoController::class, 'subirCLectiva'])->middleware('auth')->name('subir.clectiva');
Route::post('/subir/horario', [ArchivoController::class, 'subirHorario'])->middleware('auth')->name('subir.horario');
Route::post('/subir/resolucion', [ArchivoController::class, 'subirResolucion'])->middleware('auth')->name('subir.resolucion');

Route::get('/documentos', [ArchivoController::class, 'showPDF'])->middleware('auth')->name('documentos.show');

Route::get('/practicas/desarrollo', [PracticaController::class, 'desarrollo'])->middleware('auth')->name('desarrollo');
Route::post('/practicas', [PracticaController::class, 'storeDesarrollo'])->middleware('auth')->name('desarrollo.store');

Route::get('/practicas/convalidacion', [PracticaController::class, 'convalidacion'])->middleware('auth')->name('convalidacion');


Route::post('/empresas/{practicas_id}', [EmpresaController::class, 'store'])->name('empresas.store');

Route::post('/jefe_inmediato/{practicas_id}', [JefeInmediatoController::class, 'store'])->name('jefe_inmediato.store');

Route::post('/practicas/fut', [PracticaController::class, 'storeFut'])->middleware('auth')->name('store.fut');

Route::post('/practicas/cartapresentacion', [PracticaController::class, 'storeCartaPresentacion'])->middleware('auth')->name('store.cartapresentacion');

Route::post('/practicas/cartaaceptacion', [PracticaController::class, 'storeCartaAceptacion'])->middleware('auth')->name('store.cartaaceptacion');

Route::post('/practicas/planactividades', [PracticaController::class, 'storePlanActividadesPPP'])->middleware('auth')->name('store.planactividadesppp');

Route::post('/practicas/constanciacumplimiento', [PracticaController::class, 'storeConstanciaCumplimiento'])->middleware('auth')->name('store.constanciacumplimiento');

Route::post('/practicas/informefinalppp', [PracticaController::class, 'storeInformeFinalPPP'])->middleware('auth')->name('store.informefinalppp');

    
Route::get('/practica', function () {
    $persona = auth()->user()->persona; 
    $matriculas = $persona ? $persona->matriculas : collect();

    return view('practicas.practica', compact('persona', 'matriculas'));
})->middleware('auth')->name('practica');

Route::get('/supervision', [PracticaController::class, 'lst_supervision'])->middleware('auth')->name('supervision');

Route::get('/empresa', [EmpresaController::class, 'index'])->middleware('auth')->name('empresa');
Route::get('/jefe_inmediato', [JefeInmediatoController::class, 'index'])->middleware('auth')->name('jefes');

Route::post('/practicas/registroactividades', [PracticaController::class, 'storeRegistroActividades'])->middleware('auth')->name('store.registroactividades');
Route::post('/practicas/controlmensualactividades', [PracticaController::class, 'storeControlMensualActividades'])->middleware('auth')->name('store.controlmensualactividades');

Route::get("/asignacion", [asginacionController::class, "index" ])->name("asignacion_index");
Route::post('/grupos-practica', [asginacionController::class, 'store'])->name('grupos.store');

Route::POST('/grupos/{id}', [asginacionController::class, 'update'])->name('grupos.update');
Route::POST('/grupos_delete/{id}', [asginacionController::class, 'eliminar'])->name('grupos.destroy');

Route::get("/grupoEstudiante", [grupoEstudianteController::class, "index" ])->name("estudiante_index");

Route::post('/asignarAlumnos', [grupoEstudianteController::class, 'asignarAlumnos'])->name('grupos.asignarAlumnos');

Route::GET('/grupos/eliminar-asignado/{id}', [GrupoEstudianteController::class, 'destroy'])->name('grupos.eliminarAsignado');

// public function acreditarDocente()
Route::GET('/acreditarDTitular', [AcreditarController::class, 'acreditarDTitular'])->name('acreditar.dtitular');
Route::GET('/acreditarDSupervisor', [AcreditarController::class, 'acreditarDSupervisor'])->name('acreditar.dsupervisor');

Route::get('/vMatricula', [ValidacionMatriculaController::class, 'Vmatricula'])->name('Validacion.Matricula');
// Validar docente titular
Route::get('/aDTitular', [AcreditarController::class, 'ADTitular'])->name('Acreditar.Docente');
Route::get('/aDSupervisor', [AcreditarController::class, 'ADSupervisor'])->name('Acreditar.Supervisor');

Route::post('/acreditar/actualizar-archivo/{id}', [AcreditarController::class, 'actualizarEstadoArchivo'])->name('actualizar.estado.archivo');
Route::post('/acreditar/actualizar-cl/{id}', [AcreditarController::class, 'actualizarEstadoCL'])->name('actualizar.estado.cl');
Route::post('/acreditar/actualizar-horario/{id}', [AcreditarController::class, 'actualizarEstadoHorario'])->name('actualizar.estado.horario');
Route::post('/acreditar/actualizar-resolucion/{id}', [AcreditarController::class, 'actualizarEstadoResolucion'])->name('actualizar.estado.resolucion');
Route::post('/matricula/actualizar-ficha/{id}', [ValidacionMatriculaController::class, 'actualizarEstadoFicha'])->name('actualizar.estado.ficha');
Route::post('/matricula/actualizar-record/{id}', [ValidacionMatriculaController::class, 'actualizarEstadoRecord'])->name('actualizar.estado.record');

Route::post('/practicas/proceso', [PracticaController::class, 'proceso'])->middleware('auth')->name('proceso');

Route::post('/store.foto', [PersonaController::class, 'storeFoto'])->name('store.foto');


Route::get('/practica/{id}', [PracticaController::class, 'show'])->name('practica.show');

Route::get('/dashboard-docente', [DashboardDocenteController::class, 'index'])->name('dashboard.docente');
Route::get('/dashboardSupervisor', [supervisorDashboardController::class, 'indexsupervisor'])->name('supervisor.Dashboard');

Route::get('/dashboardAdmin', [adminDashboardController::class, 'indexAdmin'])->name('admin.Dashboard');

Route::get('/api/escuelas/{facultadId}', function ($facultadId) {
    return DB::table('escuelas')->where('facultad_id', $facultadId)->get();
});


Route::get('/api/docentes/{escuelaId}', function ($escuelaId) {
    return DB::table('personas')
        ->join('grupos_practicas', 'personas.id', '=', 'grupos_practicas.id_docente')
        ->where('grupos_practicas.id_escuela', $escuelaId)
        ->select('personas.id', DB::raw("CONCAT(personas.nombres, ' ', personas.apellidos) as nombre"))
        ->distinct()
        ->get();
});

Route::get('/api/semestres/{docenteId}', function ($docenteId) {
    return DB::table('grupos_practicas')
        ->join('semestres', 'grupos_practicas.id_semestre', '=', 'semestres.id')
        ->where('grupos_practicas.id_docente', $docenteId)
        ->select('semestres.id', 'semestres.codigo')
        ->distinct()
        ->get();
});



Route::get('/docente/semestres/{escuela}', [DashboardDocenteController::class, 'getSemestres']);
Route::get('/docente/supervisores/{escuela}', [DashboardDocenteController::class, 'getSupervisores']);

Route::get('/supervisor/semestres/{escuela}', [supervisorDashboard::class, 'obtenerSemestresPorEscuela']);


Route::get('/EstudianteDashborad', [estudianteDashboardController::class, 'index'])->name('dashboard.estudiante');

Route::post('/practica/{id}/edit', [EmpresaController::class, 'update'])->name('empresa.edit');
Route::post('/jefe_inmediato/{id}/edit', [JefeInmediatoController::class, 'update'])->name('jefe_inmediato.edit');



// Rutas para el dashboard de administrador (filtros dinámicos)
Route::get('/api/semestres/escuela/{escuelaId}', function ($escuelaId) {
    /*return DB::table('grupos_practicas')
        ->join('semestres', 'grupos_practicas.id_semestre', '=', 'semestres.id')
        ->where('grupos_practicas.id_escuela', $escuelaId)
        ->select('semestres.id', 'semestres.codigo')
        ->distinct()
        ->get();*/
        return DB::table('semestres')->get();
});

Route::get('/api/docentes/{escuelaId}/{semestreId}', function ($escuelaId, $semestreId) {
    return DB::table('personas')
        ->join('grupos_practicas', 'personas.id', '=', 'grupos_practicas.id_docente')
        ->where('grupos_practicas.id_escuela', $escuelaId)
        ->where('grupos_practicas.id_semestre', $semestreId)
        ->select('personas.id', DB::raw("CONCAT(personas.nombres, ' ', personas.apellidos) as nombre"))
        ->distinct()
        ->get();
});

// Listar los semestres de la tabla semestres
Route::get('/api/semestres', function () {
    return DB::table('semestres')->get();
});

// Actualizar semestre actual en la sesión /set-active/{id}
Route::get('/semestre/set-active/{id}', [semestreController::class, 'setActive'])->name('semestre.setActive');
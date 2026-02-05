<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\cerrarSesionController;
use App\Http\Controllers\DashboardDocenteController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\facultadController;
use App\Http\Controllers\EscuelaController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\EvaluacionPracticaController;
use App\Http\Controllers\grupoEstudianteController;
use App\Http\Controllers\matriculaController;
use App\Http\Controllers\semestreController;
use App\Http\Controllers\supervisorDashboardController;
use App\Http\Controllers\AcreditarController;
use App\Http\Controllers\validacionMatriculaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\preguntaController;
use App\Http\Controllers\respuestaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PracticaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\RevisarController;
use App\Http\Controllers\estudianteDashboardController;
use App\Http\Controllers\JefeInmediatoController;
use App\Http\Controllers\PanelPrincipal;
use App\Http\Controllers\SolicitudController;
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

Route::get("/", [LoginController::class, "landing"])->name("home");

Route::middleware("active")->group(function () {
    Route::get("/panel", [PanelPrincipal::class, "dashboard"])
        ->middleware("auth")
        ->name("panel");
});

Route::get("/login", [LoginController::class, "index"])->name("login");
Route::post("/login", [LoginController::class, "login"]);
Route::get("/cerrarSecion", [
    cerrarSesionController::class,
    "cerrarSecion",
])->name("cerrarSecion");
Route::get("/estudiantes", [homeController::class, "index_estudiante"])
    ->middleware("auth")
    ->middleware("active")
    ->name("dashboard.dashboardEstudiante");

// ... otras rutas ...

Route::get("/segmento/perfil", [PersonaController::class, "users"])
    ->middleware("auth")
    ->name("perfil");

// Ruta para mostrar la vista de cambio de contraseña
Route::get("/segmento/cambiar-contrasena", [
    PersonaController::class,
    "changePasswordView",
])
    ->middleware("auth")
    ->name("persona.change.password.view");

// Rutas para personas
Route::get("/personas/check-dni/{dni}", [PersonaController::class, "checkDni"])
    ->middleware("auth")
    ->name("personas.check.dni");
Route::get("/personas/check-email/{email}", [
    PersonaController::class,
    "checkEmail",
])
    ->middleware("auth")
    ->name("personas.check.email");

// Ruta de sidebar group Supervisión - Grupos
Route::prefix("grupo")
    ->middleware("auth")
    ->name("grupo.")
    ->group(function () {
        Route::get("/practica", [AsignacionController::class, "index"])->name(
            "practica",
        );
        Route::get("/estudiante", [
            grupoEstudianteController::class,
            "index",
        ])->name("estudiante");
    });
// Ruta de sidebar group Supervisión - Seguimiento
Route::prefix("seguimiento")
    ->middleware("auth")
    ->name("seguimiento.")
    ->group(function () {
        Route::get("/ppp", [
            PracticaController::class,
            "lst_supervision",
        ])->name("ppp");
        Route::get("/practica", [PracticaController::class, "index"])->name(
            "practica",
        );
        Route::get("/evaluation", [
            EvaluacionPracticaController::class,
            "index",
        ])->name("evaluation");
        Route::get("/revisar", [RevisarController::class, "index"])->name(
            "revisar",
        );
        Route::get("/revisar-v1", [RevisarController::class, "index_v1"])->name(
            "revisar.v1",
        );
        Route::get("/evaluar", [EvaluacionController::class, "index"])->name(
            "evaluar",
        );
    });

// Ruta de sidebar group Gestión - Usuario
Route::prefix("usuario")
    ->middleware("auth")
    ->name("usuario.")
    ->group(function () {
        Route::get("registrar", [PersonaController::class, "registro"])->name(
            "registrar",
        );
        Route::get("general", [
            PersonaController::class,
            "lista_usuarios",
        ])->name("general");
        Route::get("subadmins", [
            PersonaController::class,
            "lista_subadmins",
        ])->name("subadmins");
        Route::get("dtitulares", [
            PersonaController::class,
            "lista_docentes",
        ])->name("dtitulares");
        Route::get("dsupervisores", [
            PersonaController::class,
            "lista_supervisores",
        ])->name("dsupervisores");
        Route::get("estudiantes", [
            PersonaController::class,
            "lista_estudiantes",
        ])->name("estudiantes");
    });

// Ruta de sidebar group Gestión - Académico
Route::prefix("academico")
    ->middleware("auth")
    ->name("academico.")
    ->group(function () {
        Route::resource("semestre", semestreController::class);

        Route::resource("facultad", facultadController::class);

        Route::resource("escuela", EscuelaController::class);

        Route::resource("seccion", SeccionController::class);
    });

// Ruta para la carga masiva de usuarios
Route::post("/segmento/usuarios-masivos", [
    PersonaController::class,
    "store_masivo",
])->name("usuarios.masivos.store");

Route::post("/segmento/registrar", [PersonaController::class, "store"])
    ->middleware(["auth", "active"])
    ->name("personas.store");

Route::get("/escuelas/{facultad_id}", [
    PersonaController::class,
    "getEscuelas",
]);

Route::get("/list_users/modal-editar", function () {
    return view("list_users.edit_persona");
})
    ->middleware("auth")
    ->name("modal.editar");

Route::post("/personas/verificar", [PersonaController::class, "verificar"])
    ->middleware(["auth", "active"])
    ->name("personas.verificar");
Route::post("/personas/asignar", [PersonaController::class, "asignar"])
    ->middleware(["auth", "active"])
    ->name("personas.asignar");

Route::post("/persona/editar", [PersonaController::class, "update"])
    ->middleware(["auth", "active"])
    ->name("persona.editar");

// Ruta para actualizar contraseña
Route::post("/persona/update-password", [
    PersonaController::class,
    "updatePassword",
])
    ->middleware("auth")
    ->name("persona.update.password");

Route::get("/list_users/grupo_estudiante", [
    PersonaController::class,
    "lista_grupos_estudiantes",
])
    ->middleware("auth")
    ->middleware("active")
    ->name("grupo_estudiante");

Route::delete("/personas/{id}", [PersonaController::class, "destroy"])
    ->middleware(["auth", "active"])
    ->name("personas.destroy");

Route::post("/personas/solicitud_baja", [
    PersonaController::class,
    "solicitudBaja",
])
    ->middleware(["auth", "active"])
    ->name("personas.solicitud_baja");
//Route::post('/personas/disable', [PersonaController::class, 'disabledAsignacion'])->middleware('auth')->name('personas.disable');
// Ruta para obtener los datos de un docente
Route::get("/personas/{id}", [PersonaController::class, "edit"])
    ->middleware("auth")
    ->name("personas.edit");

//Bloque Academico

//Route::resource('evaluacionPractica', EvaluacionPracticaController::class);

//Route::resource('revisar', RevisarController::class)->name('revisar');

Route::get("/semestre/{semestre}/edit", [
    SemestreController::class,
    "edit",
])->name("semestre.edit");
Route::put("/semestre/{semestre}/finalizar", [
    SemestreController::class,
    "finalizar",
])->name("semestre.finalizar");
Route::put("/semestre/{semestre}/retroceder", [
    SemestreController::class,
    "retroceder",
])->name("semestre.retroceder");

// Evaluación

//Route::resource("evaluacion", EvaluacionController::class);
Route::post("/evaluacion/store-anexos", [
    EvaluacionController::class,
    "storeAnexos",
])->name("evaluacion.storeAnexos");
Route::post("/evaluacion/store-entrevista", [
    EvaluacionController::class,
    "storeEntrevista",
])->name("evaluacion.storeEntrevista");

//Preguntas
Route::middleware("active")->group(function () {
    Route::resource("pregunta", preguntaController::class);
});

//Respuestas
Route::post("/respuestas", [respuestaController::class, "store"])->name(
    "respuestas.store",
);

Route::get("/matricula", [matriculaController::class, "index"])
    ->middleware("auth")
    ->name("matricula_index");
Route::get("/matricula/estudiante", [matriculaController::class, "modal"])
    ->middleware("auth")
    ->name("matricula_modal");
Route::post("/subir/ficha", [ArchivoController::class, "subirFicha"])
    ->middleware(["auth", "active"])
    ->name("subir.ficha");
Route::post("/subir/record", [ArchivoController::class, "subirRecord"])
    ->middleware(["auth", "active"])
    ->name("subir.record");
Route::post("/subir/clectiva", [ArchivoController::class, "subirCLectiva"])
    ->middleware(["auth", "active"])
    ->name("subir.clectiva");
Route::post("/subir/horario", [ArchivoController::class, "subirHorario"])
    ->middleware(["auth", "active"])
    ->name("subir.horario");
Route::post("/subir/resolucion", [ArchivoController::class, "subirResolucion"])
    ->middleware(["auth", "active"])
    ->name("subir.resolucion");
Route::post("/subir/anexo", [ArchivoController::class, "subirAnexo"])
    ->middleware(["auth", "active"])
    ->name("subir.anexo");
Route::post("/subir/documento", [
    ArchivoController::class,
    "subirDocumentoPractica",
])
    ->middleware(["auth", "active"])
    ->name("subir.documento");
Route::post("/actualizar/archivo", [
    ArchivoController::class,
    "actualizarEstadoArchivo",
])
    ->middleware(["auth", "active"])
    ->name("actualizar.archivo");
Route::post("/actualizar/anexo", [
    ArchivoController::class,
    "actualizarEstadoAnexo",
])
    ->middleware(["auth", "active"])
    ->name("actualizar.anexo");

Route::get("/documento/{path}", [ArchivoController::class, "showPDF"])
    ->where("path", ".*")
    ->middleware("auth")
    ->name("documentos.show");

Route::get("/recursos", [ArchivoController::class, "indexRecursos"])
    ->middleware(["auth", "active"])
    ->name("recursos");
Route::post("/recursos", [ArchivoController::class, "storeRecurso"])
    ->middleware(["auth", "active"])
    ->name("recursos.store");
Route::delete("/recursos/{id}", [ArchivoController::class, "destroyRecurso"])
    ->middleware(["auth", "active"])
    ->name("recursos.destroy");

Route::get("/practicas/desarrollo", [PracticaController::class, "desarrollo"])
    ->middleware("auth")
    ->name("desarrollo");

Route::post("/empresa/actualizar-estado", [
    EmpresaController::class,
    "actualizarEstadoEmpresa",
])->name("empresa.actualizar.estado");
Route::post("/jefe_inmediato/actualizar-estado", [
    JefeInmediatoController::class,
    "actualizarEstadoJefeInmediato",
])->name("jefe_inmediato.actualizar.estado");
Route::post("/acreditar/actualizar-archivo", [
    AcreditarController::class,
    "actualizarEstadoArchivo",
])->name("actualizar.estado.archivo");
Route::post("/matricula/actualizar-archivo-mat", [
    matriculaController::class,
    "actualizarEstadoArchivo",
])->name("actualizar.estado.archivo.mat");
Route::post("/acreditar/actualizar-cl/{id}", [
    AcreditarController::class,
    "actualizarEstadoCL",
])->name("actualizar.estado.cl");
Route::post("/acreditar/actualizar-horario/{id}", [
    AcreditarController::class,
    "actualizarEstadoHorario",
])->name("actualizar.estado.horario");
Route::post("/acreditar/actualizar-resolucion/{id}", [
    AcreditarController::class,
    "actualizarEstadoResolucion",
])->name("actualizar.estado.resolucion");
Route::post("/practicas", [PracticaController::class, "storeDesarrollo"])
    ->middleware("auth")
    ->name("desarrollo.store");

Route::get("/practicas/convalidacion", [
    PracticaController::class,
    "convalidacion",
])
    ->middleware("auth")
    ->name("convalidacion");

Route::post("/empresas/{practicas_id}", [
    EmpresaController::class,
    "store",
])->name("empresas.store");

Route::post("/jefe_inmediato/{practicas_id}", [
    JefeInmediatoController::class,
    "store",
])->name("jefe_inmediato.store");

Route::post("/practicas/fut", [PracticaController::class, "storeFut"])
    ->middleware("auth")
    ->name("store.fut");

Route::post("/practicas/cartapresentacion", [
    PracticaController::class,
    "storeCartaPresentacion",
])
    ->middleware("auth")
    ->name("store.cartapresentacion");

Route::post("/practicas/cartaaceptacion", [
    PracticaController::class,
    "storeCartaAceptacion",
])
    ->middleware("auth")
    ->name("store.cartaaceptacion");

Route::post("/practicas/planactividades", [
    PracticaController::class,
    "storePlanActividadesPPP",
])
    ->middleware("auth")
    ->name("store.planactividadesppp");

Route::post("/practicas/constanciacumplimiento", [
    PracticaController::class,
    "storeConstanciaCumplimiento",
])
    ->middleware("auth")
    ->name("store.constanciacumplimiento");

Route::post("/practicas/informefinalppp", [
    PracticaController::class,
    "storeInformeFinalPPP",
])
    ->middleware("auth")
    ->name("store.informefinalppp");

Route::get("/practica", function () {
    $persona = auth()->user()->persona;
    $matriculas = $persona ? $persona->matriculas : collect();

    return view("practicas.practica", compact("persona", "matriculas"));
})
    ->middleware("auth")
    ->name("practica");

Route::get("/practicas/estudiante", [
    homeController::class,
    "practicasEstudiante",
])
    ->middleware("auth")
    ->middleware("active")
    ->name("practicas.estudiante");
Route::get("/mi-practica", [homeController::class, "miPracticaIndex"])
    ->middleware("auth")
    ->middleware("active")
    ->name("practicas.mi-practica");

Route::get("/matricula/estudiante", [
    homeController::class,
    "matriculaEstudiante",
])
    ->middleware("auth")
    ->name("matricula.estudiante");
Route::get("/matricula/index", [homeController::class, "matriculaIndex"])
    ->middleware("auth")
    ->name("matricula.index");
Route::get("/s-practicas", [PracticaController::class, "index"])
    ->middleware("auth")
    ->name("seguimiento.practicas");
Route::get("/supervision/seguimiento/{id}", [
    PracticaController::class,
    "detalle_supervision",
])
    ->middleware("auth")
    ->name("supervision.detalle");

Route::get("/empresa", [EmpresaController::class, "index"])
    ->middleware("auth")
    ->name("empresa");
Route::get("/jefe_inmediato", [JefeInmediatoController::class, "index"])
    ->middleware("auth")
    ->name("jefes");

Route::post("/practicas/registroactividades", [
    PracticaController::class,
    "storeRegistroActividades",
])
    ->middleware("auth")
    ->name("store.registroactividades");
Route::post("/practicas/controlmensualactividades", [
    PracticaController::class,
    "storeControlMensualActividades",
])
    ->middleware("auth")
    ->name("store.controlmensualactividades");

Route::post("/grupos-practica", [AsignacionController::class, "store"])->name(
    "grupos.store",
);

Route::POST("/grupos/{id}", [AsignacionController::class, "update"])->name(
    "grupos.update",
);
Route::POST("/grupos_delete/{id}", [
    AsignacionController::class,
    "eliminar",
])->name("grupos.destroy");

Route::post("/asignarAlumnos", [
    grupoEstudianteController::class,
    "asignarAlumnos",
])
    ->middleware("auth")
    ->middleware("active")
    ->name("grupos.asignarAlumnos");

Route::DELETE("/grupos/eliminar-asignado/{id}", [
    GrupoEstudianteController::class,
    "destroy",
])
    ->middleware("auth")
    ->middleware("active")
    ->name("grupos.eliminarAsignado");

// public function acreditarDocente()
Route::get("/acreditar", [AcreditarController::class, "acreditar"])
    ->middleware("auth")
    ->name("acreditar");
Route::GET("/acreditarDTitular", [
    AcreditarController::class,
    "acreditarDTitular",
])
    ->middleware("auth")
    ->name("acreditar.dtitular");
Route::GET("/acreditarDSupervisor", [
    AcreditarController::class,
    "acreditarDSupervisor",
])
    ->middleware("auth")
    ->name("acreditar.dsupervisor");

// Validar docente titular

Route::get("/api/acreditacion/archivos/{id}/{tipo}", [
    AcreditarController::class,
    "getArchivosPorTipo",
]);
Route::get("/api/evaluacion/archivos/{id}/{tipo}", [
    EvaluacionController::class,
    "getArchivos",
]);
Route::post("/matricula/actualizar-ficha/{id}", [
    ValidacionMatriculaController::class,
    "actualizarEstadoFicha",
])->name("actualizar.estado.ficha");
Route::post("/matricula/actualizar-record/{id}", [
    ValidacionMatriculaController::class,
    "actualizarEstadoRecord",
])->name("actualizar.estado.record");

Route::post("/practicas/proceso", [PracticaController::class, "proceso"])
    ->middleware("auth")
    ->name("proceso");

Route::post("/store.foto", [PersonaController::class, "storeFoto"])
    ->middleware(["auth", "active"])
    ->name("store.foto");

Route::get("/practica/{id}", [PracticaController::class, "show"])->name(
    "practica.show",
);
Route::get("/practica/status/{id}", [
    PracticaController::class,
    "status",
])->name("practica.status");
Route::post("/practica/calificar", [PracticaController::class, "calificar"])
    ->middleware("auth")
    ->name("practica.calificar");
Route::get("/practica/{type}/{id}", [
    PracticaController::class,
    "showTypeFile",
])->name("practica.typefile");

Route::get("/dashboard-docente", [DashboardDocenteController::class, "index"])
    ->middleware("active")
    ->name("dashboard.docente");
Route::get("/dashboardSupervisor", [
    supervisorDashboardController::class,
    "indexsupervisor",
])
    ->middleware("active")
    ->name("supervisor.Dashboard");

Route::get("/dashboardAdmin", [
    AdminDashboardController::class,
    "indexAdmin",
])->name("admin.Dashboard");

Route::post("/solicitud/ap", [
    SolicitudController::class,
    "setSolicitudAp",
])->name("solicitud.ap");
Route::post("/solicitud/nota", [
    SolicitudController::class,
    "setSolicitudNota",
])->name("solicitud.nota");

Route::get("/api/escuelas/{facultadId}", function ($facultadId) {
    return DB::table("escuelas")->where("facultad_id", $facultadId)->get();
});

Route::get("/api/docentes/{escuelaId}", function ($escuelaId) {
    return DB::table("personas")
        ->join(
            "grupos_practicas",
            "personas.id",
            "=",
            "grupos_practicas.id_docente",
        )
        ->where("grupos_practicas.id_escuela", $escuelaId)
        ->select(
            "personas.id",
            DB::raw(
                "CONCAT(personas.nombres, ' ', personas.apellidos) as nombre",
            ),
        )
        ->distinct()
        ->get();
});

// filtra seccion por id_escuela y id_semestre
Route::get("/api/secciones/{id_escuela}/{id_semestre}", function (
    $id_escuela,
    $id_semestre,
) {
    return DB::table("seccion_academica")
        ->where("id_escuela", $id_escuela)
        ->where("id_semestre", $id_semestre)
        ->select("seccion_academica.id", "seccion_academica.seccion as name")
        ->distinct()
        ->get();
});

Route::get("api/docentes-titulares/{saId}", [
    PersonaController::class,
    "getDocentesTitulares",
])->middleware("auth");
Route::get("api/docentes-supervisores/{saId}", [
    PersonaController::class,
    "getDocentesSupervisores",
])->middleware("auth");

Route::get("/api/semestres/{docenteId}", function ($docenteId) {
    return DB::table("grupos_practicas")
        ->join("semestres", "grupos_practicas.id_semestre", "=", "semestres.id")
        ->where("grupos_practicas.id_docente", $docenteId)
        ->select("semestres.id", "semestres.codigo")
        ->distinct()
        ->get();
});

// obtener a los estudiantes
Route::get("/api/asignar_estudiantes/{saId}", function ($saId) {
    return DB::table("asignacion_persona as ap")
        ->join("personas as p", "ap.id_persona", "=", "p.id")
        ->leftjoin("grupo_estudiante as ge", "ap.id", "=", "ge.id_estudiante")
        ->whereNull("ge.id") // Solo estudiantes no asignados
        ->where("ap.id_sa", $saId)
        ->where("ap.id_rol", 5)
        ->select("ap.id", "p.codigo", "p.nombres", "p.apellidos")
        ->get();
});

Route::get("/api/grupo/{id}", [AsignacionController::class, "getGrupo"]);

// obtener a los estudiante de grupo_estudiante
Route::get("/api/grupo_estudiantes/{grupoId}", function ($grupoId) {
    return DB::table("grupo_estudiante as ge")
        ->join("asignacion_persona as ap", "ge.id_estudiante", "=", "ap.id")
        ->join("personas as p", "ap.id_persona", "=", "p.id")
        ->join("grupo_practica as gp", "ge.id_gp", "=", "gp.id")
        ->where("ge.id_gp", $grupoId)
        ->select(
            "ge.id",
            "p.codigo",
            "p.nombres",
            "p.apellidos",
            "gp.name as grupo_name",
        )
        ->get();
});

Route::get("/api/practica/getCalificacion/{id}", [
    PracticaController::class,
    "getCalificacion",
]);

Route::get("/api/matricula/{id}/{tipo}", [
    validacionMatriculaController::class,
    "getArchivosPorTipo",
])->middleware("auth");

Route::get("/api/evaluacion_practica/{id_ap}/{id_modulo}/{anexo}", [
    EvaluacionPracticaController::class,
    "getEvaluacionPractica",
]);

Route::get("/api/empresa/{practica}", [
    EmpresaController::class,
    "getEmpresa",
])->middleware("auth");
Route::get("/api/jefeinmediato/{practica}", [
    JefeInmediatoController::class,
    "getJefeInmediato",
])->middleware("auth");
Route::get("/api/documento/{id}/{type}", [
    ArchivoController::class,
    "getArchivosPorTipo",
])->middleware("auth");
Route::get("/api/documento/ppp/{practica}/{type}", [
    ArchivoController::class,
    "getDocumentoPractica",
])->middleware("auth");
Route::get("/api/verificar/{email}", [PersonaController::class, "verificar"]);

Route::get("/api/solicitud_baja/{id_ap}/{id_sa}", [
    PersonaController::class,
    "getSolicitudBaja",
]);
Route::post("/api/solicitud_nota", [
    PracticaController::class,
    "solicitud_nota",
])->name("solicitud_nota");
Route::post("/api/solicitud_ap", [
    SolicitudController::class,
    "solicitud_ap",
])->name("solicitud_ap");
Route::get("/api/solicitud/getSolicitudNota/{id_practica}", [
    SolicitudController::class,
    "getSolicitudNota",
]);
Route::get("/api/solicitud/getSolicitudAp/{id_ap}", [
    SolicitudController::class,
    "getSolicitudAp",
]);
Route::get("/api/persona/{id}", [
    PersonaController::class,
    "getPersonaForEdit",
])->middleware("auth");

Route::get("/docente/semestres/{escuela}", [
    DashboardDocenteController::class,
    "getSemestres",
]);
Route::get("/docente/supervisores/{escuela}", [
    DashboardDocenteController::class,
    "getSupervisores",
]);

Route::get("/supervisor/semestres/{escuela}", [
    supervisorDashboardController::class,
    "obtenerSemestresPorEscuela",
]);

Route::get("/EstudianteDashborad", [
    estudianteDashboardController::class,
    "index",
])->name("dashboard.estudiante");

Route::post("/practica/{id}/edit", [EmpresaController::class, "update"])->name(
    "empresa.edit",
);
Route::post("/jefe_inmediato/{id}/edit", [
    JefeInmediatoController::class,
    "update",
])->name("jefe_inmediato.edit");

// Rutas para el dashboard de administrador (filtros dinámicos)
Route::get("/api/semestres/escuela/{escuelaId}", function ($escuelaId) {
    /*return DB::table('grupos_practicas')
        ->join('semestres', 'grupos_practicas.id_semestre', '=', 'semestres.id')
        ->where('grupos_practicas.id_escuela', $escuelaId)
        ->select('semestres.id', 'semestres.codigo')
        ->distinct()
        ->get();*/
    return DB::table("semestres")->get();
});

Route::get("/api/docentes/{escuelaId}/{semestreId}", function (
    $escuelaId,
    $semestreId,
) {
    return DB::table("personas")
        ->join(
            "grupos_practicas",
            "personas.id",
            "=",
            "grupos_practicas.id_docente",
        )
        ->where("grupos_practicas.id_escuela", $escuelaId)
        ->where("grupos_practicas.id_semestre", $semestreId)
        ->select(
            "personas.id",
            DB::raw(
                "CONCAT(personas.nombres, ' ', personas.apellidos) as nombre",
            ),
        )
        ->distinct()
        ->get();
});

// Listar los semestres de la tabla semestres
Route::get("/api/semestres", function () {
    return DB::table("semestres")->get();
});

// Actualizar semestre actual en la sesión /set-active/{id}
Route::get("/semestre/set-active/{id}", [
    semestreController::class,
    "setActive",
])->name("semestre.setActive");

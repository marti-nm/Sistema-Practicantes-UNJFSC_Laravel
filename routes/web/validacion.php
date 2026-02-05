<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcreditarController;
use App\Http\Controllers\ValidacionMatriculaController;

// Ruta de sidebar group Supervisión - Validación
Route::prefix("validacion")
    ->middleware("auth")
    ->name("validacion.")
    ->group(function () {
        Route::get("/dtitular", [
            AcreditarController::class,
            "ADTitular",
        ])->name("dtitular");
        Route::get("/dsupervisor", [
            AcreditarController::class,
            "ADSupervisor",
        ])->name("dsupervisor");
        Route::get("/estudiante", [
            ValidacionMatriculaController::class,
            "Vmatricula",
        ])->name("estudiante");
    });

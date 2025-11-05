<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\asignacion_persona;

class panelPrincipal extends Controller
{
    public function dashboard()
{
    
    $rol = auth()->user()->getRolId();
    $persona = auth()->user()->persona;
    $asignacion = asignacion_persona::where('id_persona', $persona->id)->first();
    $estado_ap = $asignacion->estado;


    switch ($rol) {
        case 1:
            return redirect()->action([adminDashboardController::class, 'indexAdmin']);
        case 2:
            return redirect()->action([adminDashboardController::class, 'indexAdmin']);
        case 3:
            return redirect()->action([DashboardDocenteController::class, 'index']);
        case 4:
            if ($estado_ap == 2) {
                return redirect()->action([AcreditarController::class, 'acreditarDSupervisor']);
            }
            return redirect()->action([supervisorDashboardController::class, 'indexsupervisor']);
        case 5:
            return redirect()->action([homeController::class, 'index_estudiante']);
        default:
            abort(403, 'Acceso no autorizado');
    }
}

}

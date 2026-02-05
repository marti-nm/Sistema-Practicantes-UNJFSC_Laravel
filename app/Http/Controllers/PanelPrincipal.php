<?php

namespace App\Http\Controllers;

class PanelPrincipal extends Controller
{
    public function dashboard()
    {
        $rol = auth()->user()->getRolId();

        switch ($rol) {
            case 1:
                return redirect()->action([
                    AdminDashboardController::class,
                    "indexAdmin",
                ]);
            case 2:
                return redirect()->action([
                    AdminDashboardController::class,
                    "indexAdmin",
                ]);
            case 3:
                return redirect()->action([
                    DashboardDocenteController::class,
                    "index",
                ]);
            case 4:
                return redirect()->action([
                    supervisorDashboardController::class,
                    "indexsupervisor",
                ]);
            case 5:
                return redirect()->action([
                    homeController::class,
                    "index_estudiante",
                ]);
            default:
                abort(403, "Acceso no autorizado");
        }
    }
}

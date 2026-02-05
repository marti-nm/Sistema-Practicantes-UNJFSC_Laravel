<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\asignacion_persona;
use App\Models\Semestre;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function landing()
    {
        if (Auth::check()) {
            return redirect()->route("panel");
        }
        return view("landing");
    }

    public function index()
    {
        if (Auth::check()) {
            return redirect()->route("panel");
        }
        return view("auth.login");
    }

    public function login(loginRequest $request)
    {
        if (!Auth::validate($request->only("email", "password"))) {
            return redirect()
                ->to("login")
                ->withErrors("Credenciales incorectas");
        }

        $user = Auth::getProvider()->retrieveByCredentials(
            $request->only("email", "password"),
        );
        Auth::login($user);
        $asignacion = $user->getAP();

        if (!$asignacion) {
            Auth::logout();
            return redirect()
                ->to("login")
                ->withErrors("No se encontró asignación para este usuario");
        }

        $tipoUsuario = $asignacion->id_rol;

        // en la session guardar el id del semestre de SU asignación (sea actual o pasado)
        session(["semestre_actual_id" => $asignacion->id_semestre]);

        switch ($tipoUsuario) {
            case 1: // Admin
                Log::info("Admin");
                return redirect()->route("admin.Dashboard");
            case 2: // Sub Admin
                Log::info("Sub Admin");
                return redirect()->route("panel");
            case 3:
                Log::info("Docente Titular");
                return redirect()->route("dashboard.docente");
            case 4: // Docente Supervisor
                Log::info("Docente Supervisor");
                return redirect()->route("supervisor.Dashboard");
            case 5: // Estudiante
                Log::info("Estudiante");
                return redirect()->route("dashboard.dashboardEstudiante");
            default:
                return redirect()->route("panel");
        }
    }
}

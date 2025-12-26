<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\asignacion_persona;
use App\Models\Semestre;

class loginController extends Controller
{
    public function landing(){
        if(Auth::check()){
            return redirect()->route('panel');
        }
        return view('landing');
    }

    public function index(){
        if(Auth::check()){
            return redirect()->route('panel');
        }
        return view('auth.login');
    } 

    public function login( loginRequest $request){
        
        if(!Auth::validate($request->only('email', 'password'))){
            return redirect()->to('login')->withErrors('Credenciales incorectas');
        }

        $user = Auth::getProvider()->retrieveByCredentials($request->only('email','password'));
        Auth::login($user);
        $persona = $user->persona;
        // Obtener la asignación más reciente del usuario
        $asignacion = asignacion_persona::where('id_persona', $persona->id)
                        ->orderBy('id', 'desc')
                        ->first();
        
        if (!$asignacion) {
            Auth::logout();
            return redirect()->to('login')->withErrors('No se encontró asignación para este usuario');
        }
        
        $tipoUsuario = $asignacion->id_rol;
        $estado = $asignacion->state;
        
        // en la session guardar el id del semestre de SU asignación (sea actual o pasado)
        session(['semestre_actual_id' => $asignacion->id_semestre]);
        
        // Redirección según el nuevo sistema de 5 roles:
        // 1: Admin, 2: Sub Admin, 3: Docente Titular, 4: Docente Supervisor, 5: Estudiante
        // 0: Inactivo, 1: Activo, 2: En espera de aprobación
        if ($estado == 0) {
            Auth::logout();
            return redirect()->to('login')->withErrors('Su cuenta está inactiva. Por favor, contacte al administrador.');
        }
        switch($tipoUsuario) {
            case 1: // Admin
                return redirect()->route('admin.Dashboard');
            case 2: // Sub Admin
                return redirect()->route('panel');
            case 3:
                return redirect()->route('dashboard.docente');
            case 4: // Docente Supervisor
                return redirect()->route('supervisor.Dashboard');
            case 5: // Estudiante
                return redirect()->route('dashboard.dashboardEstudiante');
            default:
                return redirect()->route('panel');
        }
    }
}

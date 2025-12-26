<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;
use App\Models\Semestre;
use App\Models\asignacion_persona;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ActiveUserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if (is_null($user->password_changed_at) && !$request->routeIs('persona.change.password.view')) {
            session()->flash('info', 'Por su seguridad, es necesario que actualice su contraseña antes de continuar.');
            return redirect()->route('persona.change.password.view');
        }

        $id_persona = Persona::where(['usuario_id' => $user->id])->first();

        $semestre = session('semestre_actual_id');

        Log::info('Semestre actual: ' . $semestre);
        $ap_user = asignacion_persona::where(['id_persona' => $id_persona->id])
            ->where(['id_semestre' => $semestre])
            ->first();
        
        Log::info('AP User encontrado: ' . ($ap_user ? 'SI (ID: '.$ap_user->id.', Rol: '.$ap_user->id_rol.', State: '.$ap_user->state.')' : 'NO'));

        // ========== BLOQUEO POR SEMESTRE FINALIZADO ==========
        if ($semestre) {
            $semestreObj = Semestre::find($semestre);
            Log::info('Semestre objeto state: ' . ($semestreObj ? $semestreObj->state : 'NULL'));
            
            if ($semestreObj && $semestreObj->state == 0) {
                // Verificar si el usuario NO es admin (1) ni subadmin (2)
                if ($ap_user && !in_array($ap_user->id_rol, [1, 2])) {
                    
                    // Mostrar modal de aviso solo una vez por semestre finalizado
                    $modalShownForSemester = session('semestre_finalizado_modal_id');
                    if ($modalShownForSemester != $semestre) {
                        session()->flash('show_semestre_finalizado_modal', true);
                        session(['semestre_finalizado_modal_id' => $semestre]);
                        Log::info('Modal de semestre finalizado activado para semestre: ' . $semestre);
                    }
                    
                    if ($request->isMethod('get')) {
                         // Solo mostrar advertencia visual, permitir acceso de lectura
                         if (!session()->has('warning_shown')) {
                            session()->flash('warning_semestre', 'El semestre ha sido finalizado por el administrador. El sistema está en modo solo lectura.');
                            session(['warning_shown' => true]);
                         }
                    } else {
                        // Bloquear cualquier intento de escritura (POST, PUT, DELETE)
                        Log::info('BLOQUEANDO petición POST/PUT/DELETE - Semestre finalizado');
                        if ($request->ajax()) {
                             return response()->json(['error' => 'No puede realizar acciones. El semestre ha finalizado.'], 403);
                        }
                        return back()->with('error', 'No puede realizar acciones. El semestre ha finalizado.');
                    }
                }
            }
        }

        // ========== MANEJO DE ESTADOS DE USUARIO ==========
        if (!$ap_user) {
            return $next($request); // Sin asignación, continuar
        }

        // State 0: Eliminado - No debería poder acceder
        if ($ap_user->state == 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Su cuenta ha sido eliminada.');
        }

        // State 4: Deshabilitado - Solo lectura (bloquear POST/PUT/DELETE)
        if ($ap_user->state == 4 && !$request->isMethod('get')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Su cuenta está deshabilitada. Solo tiene acceso de lectura.'], 403);
            }
            return back()->with('error', 'Su cuenta está deshabilitada. Solo tiene acceso de lectura.');
        }

        // State 2: Pendiente de aprobación - Redirigir a completar proceso
        // Solo redirigir en GET y si no está ya en la ruta correcta
        if ($ap_user->state == 2 && $request->isMethod('get')) {
            $targetRoute = null;
            $currentPath = $request->path();
            
            switch ($ap_user->id_rol) {
                case 3: // Docente
                    if (!str_starts_with($currentPath, 'acreditar')) {
                        $targetRoute = '/acreditarDTitular';
                    }
                    break;
                case 4: // Supervisor
                    if (!str_starts_with($currentPath, 'acreditar')) {
                        $targetRoute = '/acreditarDSupervisor';
                    }
                    break;
                case 5: // Estudiante
                    if (!str_starts_with($currentPath, 'matricula')) {
                        $targetRoute = '/matricula/estudiante';
                    }
                    break;
            }
            
            if ($targetRoute) {
                return redirect($targetRoute);
            }
        }

        // State 3: En revisión - Verificar si tiene matrícula/acreditación completa
        if ($ap_user->state == 3 && $request->isMethod('get')) {
            $currentPath = $request->path();
            
            switch ($ap_user->id_rol) {
                case 5: // Estudiante
                    $matricula = \App\Models\Matricula::where('id_ap', $ap_user->id)->first();
                    
                    if (!$matricula || $matricula->estado_matricula !== 'Completo') {
                        // No tiene matrícula completa, redirigir
                        if (!str_starts_with($currentPath, 'matricula')) {
                            return redirect('/matricula/estudiante')
                                ->with('warning', 'Debe completar su matrícula para acceder al sistema.');
                        }
                    }
                    break;
                    
                case 3: // Docente Titular
                    $acreditacion = \App\Models\Acreditar::where('id_ap', $ap_user->id)->first();
                    
                    if (!$acreditacion || $acreditacion->estado_acreditacion !== 'Aprobado') {
                        // No tiene acreditación aprobada, redirigir
                        if (!str_starts_with($currentPath, 'acreditar')) {
                            return redirect('/acreditarDTitular')
                                ->with('warning', 'Debe completar su acreditación para acceder al sistema.');
                        }
                    }
                    break;
                    
                case 4: // Supervisor
                    $acreditacion = \App\Models\Acreditar::where('id_ap', $ap_user->id)->first();
                    
                    if (!$acreditacion || $acreditacion->estado_acreditacion !== 'Aprobado') {
                        // No tiene acreditación aprobada, redirigir
                        if (!str_starts_with($currentPath, 'acreditar')) {
                            return redirect('/acreditarDSupervisor')
                                ->with('warning', 'Debe completar su acreditación para acceder al sistema.');
                        }
                    }
                    break;
            }
        }

        // State 1: Activo - Acceso completo (continuar)

        return $next($request);
    }
}

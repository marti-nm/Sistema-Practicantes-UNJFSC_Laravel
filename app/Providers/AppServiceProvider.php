<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Practica;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $persona = $user->persona;
                
                if ($persona) {
                    // Lógica de semestre bloqueado (Global para Navbar)
                    $semestre_bloqueado = false;
                    $semestreId = session('semestre_actual_id');
                    if ($semestreId) {
                        $semestreObj = \App\Models\Semestre::find($semestreId);
                        if ($semestreObj && $semestreObj->state == 0) {
                            $ap_user = \App\Models\asignacion_persona::where('id_persona', $persona->id)
                                        ->where('id_semestre', $semestreId)
                                        ->first();
                            if ($ap_user && !in_array($ap_user->id_rol, [1, 2])) {
                                $semestre_bloqueado = true;
                            }
                        }
                    }
                    $view->with('semestre_bloqueado', $semestre_bloqueado);

                    // Lógica existente (con validaciones básicas para evitar errores null)
                    $ap = $persona->asignacion_persona;
                    $tipo = null;
                    if($ap) {
                        $tipo = Practica::where('id_ap', $ap->id)->value('tipo_practica');
                    }
                    
                    $view->with([
                        'practica' => $tipo,
                        'nombre' => $persona->nombres,
                        'apellido' => $persona->apellidos,
                        'codigo' => $persona->codigo,
                    ]);
                }
            }
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class cerrarSesionController extends Controller
{
    public function cerrarSecion(){
        session()->forget(['semestre_finalizado_modal_id', 'warning_shown']);
        session()->flush();
        Auth::logout();
        session()->regenerate();
        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(): View
    {
        return view("docs.index");
    }
}

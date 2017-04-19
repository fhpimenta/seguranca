<?php

namespace Modulos\Seguranca\Http\Controllers;

use App\Http\Controllers\Controller;

class ModulosController extends Controller
{
    public function index()
    {
        return view('Seguranca::modulos.index');
    }
}

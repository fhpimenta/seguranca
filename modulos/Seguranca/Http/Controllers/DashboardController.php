<?php

namespace Modulos\Seguranca\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('Seguranca::dashboard.index');
    }
}

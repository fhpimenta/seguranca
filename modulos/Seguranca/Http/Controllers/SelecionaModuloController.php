<?php

namespace Modulos\Seguranca\Http\Controllers;

use App\Http\Controllers\Controller;
use Modulos\Seguranca\Repositories\ModulosRepository;
use Auth;

class SelecionaModuloController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $modulosRepository = new ModulosRepository();

        $modulos = $modulosRepository->getByUser($user->id);

        return view('Seguranca::selecionamodulo.index', compact('modulos'));
    }
}

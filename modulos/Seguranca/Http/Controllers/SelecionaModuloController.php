<?php

namespace Modulos\Seguranca\Http\Controllers;

use App\Http\Controllers\Controller;
use Modulos\Seguranca\Repositories\ModulosRepository;
use Auth;

class SelecionaModuloController extends Controller
{
    private $modulosRepository;

    public function __construct(ModulosRepository $modulosRepository)
    {
        $this->modulosRepository = $modulosRepository;
    }

    public function index()
    {
        $user = Auth::user();

        $modulos = $this->modulosRepository->getByUser($user->id);

        return view('Seguranca::selecionamodulo.index', compact('modulos'));
    }
}
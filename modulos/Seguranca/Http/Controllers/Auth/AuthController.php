<?php

namespace Modulos\Seguranca\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Modulos\Seguranca\Http\Requests\LoginRequest;
use Auth;
use Modulos\Seguranca\Providers\Seguranca\Seguranca;
use Cache;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $auth;
    protected $app;

    public function __construct(Guard $auth, Application $app)
    {
        $this->auth = $auth;
        $this->app = $app;
    }

    public function getLogin()
    {
        return view('Seguranca::auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        if ($this->auth->attempt($request->except('_token'))) {

            //Gera estrutura do menu em cache
            $seguranca = $this->app[Seguranca::class];

            $seguranca->makeCachePermissions();
            $seguranca->makeCacheMenu();

            return redirect()->intended('/');
        }

        return redirect()
            ->route('auth.login')
            ->withInput($request->only('email'))
            ->withErrors([
                'Usuário e/ou senha inválidos.',
            ]);
    }

    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();

            Cache::forget('PERMISSOES_'.$user->id);

            $this->auth->logout();

            return redirect()->route('auth.login');
        }

        return redirect('/');
    }
}

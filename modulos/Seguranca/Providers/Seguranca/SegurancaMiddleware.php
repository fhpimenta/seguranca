<?php

namespace Modulos\Seguranca\Providers\Seguranca;

use Closure;
use Illuminate\Http\Request;
use Flash;

class SegurancaMiddleware extends AbstractSegurancaMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle(Request $request, Closure $next)
    {
        if (!env('IS_SECURITY_ENNABLED')) {
            return $next($request);
        }

        $path = $this->getRouteName($request);

        if ($this->seguranca->haspermission($path)) {
            return $next($request);
        }

        Flash::error('Você não term permissão para acessar esse recurso.');
        return redirect()->back();
    }
}

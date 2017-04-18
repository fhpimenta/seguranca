<?php

namespace Modulos\Seguranca\Providers\Seguranca;

use Illuminate\Contracts\Foundation\Application;
use Modulos\Seguranca\Models\MenuItem;
use Modulos\Seguranca\Providers\Seguranca\Contracts\Seguranca as SegurancaContract;
use Cache;
use DB;
use Modulos\Seguranca\Providers\Seguranca\Exceptions\ForbiddenException;
use Modulos\Seguranca\Repositories\MenuItemRepository;

class Seguranca implements SegurancaContract
{
    /**
     * The Laravel Application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Retorna o usuário logado na aplicação
     */
    public function getUser()
    {
        return $this->app['auth']->user();
    }

    /**
     * Verifica se o usuário tem acesso ao recurso
     *
     * @param string|array $permissao
     * @return bool
     * @throws ForbiddenException
     */
    public function haspermission($permissao)
    {
        // O usuario nao esta logado, porem a rota eh liberada para usuarios guest.
        if (is_null($this->getUser())) {
            if ($this->isPreLoginOpenRoutes($permissao)) {
                return true;
            }

            return false;
        }

        // Verifica se a rota eh liberada pas usuarios logados.
        if ($this->isPostLoginOpenRoutes($permissao)) {
            return true;
        }

        // Verifica na base de dados se o perfil do usuario tem acesso ao recurso
        $hasPermission = $this->verifyPermission($this->getUser()->getAuthIdentifier(), $permissao);

        if ($hasPermission) {
            return true;
        }

        return false;
    }

    private function verifyPermission($userId, $route)
    {
        $permissoes = Cache::get('PERMISSOES_'.$userId);

        return in_array($route, $permissoes);
    }

    /**
     * Verifica se a rota eh liberada para usuarios que nao estao logados no sistema
     *
     * @param $permissao
     * @return bool
     */
    private function isPreLoginOpenRoutes($permissao)
    {
        $openRoutes = $this->app['config']->get('seguranca.prelogin_openroutes', []);

        return in_array($permissao, $openRoutes);
    }

    /**
     * Verifica se a rota eh liberada para usuarios que estao logados no sistema
     *
     * @param $permissao
     * @return bool
     */
    private function isPostLoginOpenRoutes($permissao)
    {
        $openRoutes = $this->app['config']->get('seguranca.postlogin_openroutes', []);

        return in_array($permissao, $openRoutes);
    }

    public function makeCachePermissions()
    {
        $user = $this->getUser();

        $permissions = DB::table('permissoes AS per')
                        ->join('permissoes_has_perfis AS php', 'per.id', '=', 'php.permissoes_id')
                        ->join('perfis AS perf', 'php.perfis_id', '=', 'perf.id')
                        ->join('perfis_has_users AS phu', 'phu.perfis_id', '=', 'perf.id')
                        ->where('phu.users_id', '=', $user->id)
                        ->get();

        $permissions = $permissions->pluck('rota')->toArray();

        Cache::forever('PERMISSOES_'.$user->id, $permissions);
    }

    public function makeCacheMenu()
    {
        $user = $this->getUser();

        // busca as permissoes do usuario no cache
        $permissoes = Cache::get('PERMISSOES_'.$user->id);

        // busca os modulos no qual o usuario tem permissao
        $modulos = DB::table('modulos')
                        ->join('perfis AS perf', 'perf.modulos_id', '=', 'modulos.id')
                        ->join('perfis_has_users AS phu', 'phu.perfis_id', '=', 'perf.id')
                        ->select('modulos.*')
                        ->where('phu.users_id', '=', $user->id)
                        ->get();

        for ($i = 0; $i < $modulos->count(); $i++) {
            if (!in_array($modulos[$i]->slug.'.index', $permissoes)) {
                unset($modulos[$i]);
            }
        }

        $menuItemRepository = new MenuItemRepository();

        $menus = [];

        foreach ($modulos as $modulo) {
            $tree = new \stdClass();
            $tree->categorias = [];

            $categorias = $menuItemRepository->getCategorias($modulo->id);

            foreach ($categorias as $categoria) {
                // busca as subcategorias
                $subcategorias = $menuItemRepository->getItensFilhos($modulo->id, $categoria->id);

                for ($i = 0; $i < $subcategorias->count(); $i++) {

                    if($subcategorias[$i]->rota && !$this->haspermission($subcategorias[$i]->rota)) {
                        unset($subcategorias[$i]);
                        continue;
                    }

                    // caso o item seja uma subcategoria, busca os filhos
                    if (!$subcategorias[$i]->rota) {
                        // busca os items da subcategoria
                        $itens = $menuItemRepository->getItensFilhos($modulo->id, $subcategorias[$i]->id);

                        for ($j = 0; $j < $itens->count(); $j++) {

                            if (!$this->haspermission($itens[$j]->rota)) {
                                unset($itens[$j]);
                            }
                        }

                        if ($itens->count()) {
                            $subcategorias[$i]->itens = $itens;
                            continue;
                        }

                        unset($subcategorias[$i]);
                    }
                }

                if ($subcategorias->count()) {
                    $categoria->subcategorias = $subcategorias;
                    $tree->categorias[] = $categoria;
                }
            }

            $menus[$modulo->slug] = $tree;
        }

        Cache::forever('MENU_'.$user->id, $menus);
    }

}
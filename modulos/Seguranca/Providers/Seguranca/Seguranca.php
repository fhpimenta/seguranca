<?php

namespace Modulos\Seguranca\Providers\Seguranca;

use Harpia\Menu\MenuTree;
use Harpia\Menu\MenuItem as MenuNode;
use Harpia\Tree\Node;
use Illuminate\Contracts\Foundation\Application;
use Modulos\Seguranca\Models\MenuItem;
use Modulos\Seguranca\Providers\Seguranca\Contracts\Seguranca as SegurancaContract;
use Cache;
use DB;
use Modulos\Seguranca\Providers\Seguranca\Exceptions\ForbiddenException;
use Modulos\Seguranca\Repositories\MenuItemRepository;
use Modulos\Seguranca\Repositories\ModulosRepository;

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
        $menuItemRepository = new MenuItemRepository();
        $modulosRepository = new ModulosRepository();

        $user = $this->getUser();

        // busca os modulos no qual o usuario tem permissao
        $modulos = $modulosRepository->getByUser($user->id);

        $menus = [];

        foreach ($modulos as $modulo) {
            $menu = new MenuTree();
            $menu->addValue(new Node($modulo->nome, $modulo, false));

            $categorias = $menuItemRepository->getCategorias($modulo->id);

            foreach ($categorias as $categoria) {
                $menu->addTree($this->makeCategoriaTree($modulo->id, $categoria->id));
            }

            $menus[$modulo->slug] = $menu;
        }

        Cache::forever('MENU_'.$user->id, $menus);
    }

    public function makeCategoriaTree($moduloId, $categoriaId)
    {
        $menuItemRepository = new MenuItemRepository();
        $categoriaTree = new MenuTree();

        // Categoria eh a raiz da subarvore atual
        $categoria = $menuItemRepository->find($categoriaId);
        $categoriaTree->addValue(new MenuNode($categoria->nome, $categoria, false));

        $itensFilhos = $menuItemRepository->getItensFilhos($moduloId, $categoriaId);

        foreach ($itensFilhos as $itensFilho) {

            // Se é uma subcategoria, monta recursivamente a arvore
            if ($menuItemRepository->isSubCategoria($itensFilho->id)) {
                $categoriaTree->addTree($this->makeCategoriaTree($moduloId, $itensFilho->id));
            }

            // Se for um item final, adiciona a arvore
            if ($menuItemRepository->isItem($itensFilho->id) && $this->haspermission($itensFilho->rota)) {
                $categoriaTree->addValue(new MenuNode($itensFilho->nome, $itensFilho));
            }
        }

        return $categoriaTree;
    }
}

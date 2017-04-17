<?php

namespace Modulos\Seguranca\Providers\Seguranca;

use Illuminate\Contracts\Foundation\Application;
use Modulos\Seguranca\Providers\Seguranca\Contracts\Seguranca as SegurancaContract;
use Cache;
use DB;
use Modulos\Seguranca\Providers\Seguranca\Exceptions\ForbiddenException;

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

        $menus = [];

        foreach ($modulos as $modulo) {
            $tree = [];
            $tree['categorias'] = [];

            $categorias = DB::table('menu_itens')
                            ->where('modulos_id', '=', $modulo->id)
                            ->where('visivel', '=', 1)
                            ->whereNull('menu_itens_pai')
                            ->orderBy('ordem', 'asc')
                            ->get();

            if ($categorias->count()) {
                foreach ($categorias as $categoria) {
                   $categoriaArr = [
                       'nome' => $categoria->nome,
                       'icone' => $categoria->icone,
                       'ordem' => $categoria->ordem,
                       'subcategorias' => []
                   ];

                    // busca as subcategorias
                    $subcategorias = DB::table('menu_itens')
                                        ->where('modulos_id', '=', $modulo->id)
                                        ->where('menu_itens_pai', '=', $categoria->id)
                                        ->where('visivel', '=', 1)
                                        ->orderBy('ordem', 'asc')
                                        ->get();

                    foreach ($subcategorias as $subcategoria) {

                        $subcategoriaArr = [
                            'nome' => $subcategoria->nome,
                            'icone' => $subcategoria->icone,
                            'ordem' => $subcategoria->ordem,
                            'rota' => $subcategoria->rota,
                            'items' => []
                        ];

                        if($subcategoria->rota && !$this->haspermission($subcategoria->rota)) {
                            continue;
                        }

                        if (!$subcategoria->rota) {
                            // busca os items da subcategoria
                            $itens = DB::table('menu_itens')
                                ->where('modulos_id', '=', $modulo->id)
                                ->where('menu_itens_pai', '=', $subcategoria->id)
                                ->where('visivel', '=', 1)
                                ->orderBy('ordem', 'asc')
                                ->get();

                            foreach ($itens as $item) {

                                if ($this->haspermission($item->rota)) {
                                    $itemArr = [
                                        'nome' => $item->nome,
                                        'icone' => $item->icone,
                                        'ordem' => $item->ordem,
                                        'rota' => $item->rota
                                    ];

                                    $subcategoriaArr['itens'][] = $itemArr;
                                }
                            }
                        }

                        $categoriaArr['subcategorias'][] = $subcategoriaArr;
                    }

                    $tree['categorias'][] = $categoriaArr;
                }
            }

            $menus[$modulo->slug] = $tree;
        }

        Cache::forever('MENU_'.$user->id, $menus);
    }

}
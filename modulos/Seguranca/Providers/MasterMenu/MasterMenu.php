<?php

namespace Modulos\Seguranca\Providers\MasterMenu;

use Cache;

class MasterMenu
{
    protected $request;
    protected $auth;

    public function __construct($app)
    {
        $this->request = $app['request'];
        $this->auth = $app['auth'];
    }

    /**
     * Renderiza o menu para o usuario
     * @return string
     */
    public function render()
    {
        $userId = $this->auth->user()->id;

        // Obtem o modulo a partir da requisicao
        $routeName = $this->request->route()->getName();
        $moduloSlug = explode('.', $routeName)[0];

        $menu = Cache::get('MENU_' . $userId);
        $menu = $menu[$moduloSlug];

        $render = '<ul class="sidebar-menu">';
        $render .= '<li class="header">MENU</li>';

        foreach ($menu['categorias'] as $key => $categoria) {
            if (!empty($categoria['subcategorias'])) {
                $render .= '<li class="treeview">';
                $render .= '<a href="#">';
                $render .= '<i class="'.$categoria['icone'].'"></i>';
                $render .= '<span>'.$categoria['nome'].'</span>';
                $render .= '<span class="pull-right-container">';
                $render .= '<i class="fa fa-angle-left pull-right"></i>';
                $render .= '</span></a>';
                $render .= '<ul class="treeview-menu">';

                foreach($categoria['subcategorias'] as $subcategoria) {

                    if (!$subcategoria['rota'] && !empty($subcategoria['itens'])) {
                        $render .= '<li><a href="#"><i class="'.$subcategoria['icone'].'"></i> '.$subcategoria['nome'];
                        $render .= '<span class="pull-right-container">';
                        $render .= '<i class="fa fa-angle-left pull-right"></i>';
                        $render .= '</span></a>';

                        $render .= '<ul class="treeview-menu">';
                        foreach($subcategoria['itens'] as $key => $item) {
                            $render .= '<li><a href="'.route($item['rota']).'"><i class="'.$item['icone'].'"></i> '.$item['nome'].'</a></li>';
                        }

                        $render .= "</ul></li>";
                        continue;
                    }

                    $render .= '<li><a href="'.route($subcategoria['rota']).'"><i class="'.$subcategoria['icone'].'"></i> '.$subcategoria['nome'].'</a></li>';
                }

                $render .= "</ul></li>";
            }
        }

        $render .= "</ul>";

        return $render;
    }
}
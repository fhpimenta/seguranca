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
        $menuTree = $menu[$moduloSlug];

        $render = '<ul class="sidebar-menu">';

        $root = $menuTree->getRoot();

        if ($root->hasChildren()) {
            foreach ($root->getChilds() as $child) {

                $render .= $this->buildMenu($child);
            }
        }

        $render .= "</ul>";

        return $render;
    }

    private function isActive($rota, $permissao)
    {
        $rota = explode('.', $rota);
        $rota = array_slice($rota, 0, 2);

        $permissao = explode('.', $permissao);
        $permissao = array_slice($permissao, 0, 2);

        if ($rota == $permissao) {
            return true;
        }

        return false;
    }

    private function checkLeafIsActive($node)
    {
        $result = false;

        if ($node->hasChildren()) {
            foreach ($node->getChilds() as $child) {

                $result = $this->checkLeafIsActive($child);

                if ($result) {
                    return true;
                }
            }
        }

        $obj = $node->getData();
        $routeName = $this->request->route()->getName();

        if ($this->isActive($routeName, $obj->rota)) {
            $result = true;
        }

        return $result;
    }

    private function buildMenu($node, $isActive = false, $html = '') {

        // Verifica se uma categoria
        if (!$node->getFather() && !$node->isLeaf() && $node->getChilds()) {

            $isActive = $this->checkLeafIsActive($node);

            $data = $node->getData();

            $html .= '<li class="treeview';
            if ($isActive) {
                $html .= ' active';
            }
            $html .= '">';
            $html .= '<a href="#">';
            $html .= '<i class="'.$data->icone.'"></i>';
            $html .= '<span>'.$data->nome.'</span>';
            $html .= '<span class="pull-right-container">';
            $html .= '<i class="fa fa-angle-left pull-right"></i>';
            $html .= '</span></a>';
            $html .= '<ul class="treeview-menu">';

            if ($node->hasChildren()) {
                foreach ($node->getChilds() as $child) {
                    $html .= $this->buildMenu($child);
                }
            }

            $html .= "</ul></li>";
        }

        // Verifica se é uma subcategoria
        if ($node->getFather() && !$node->isLeaf() && $node->getChilds()) {

            $data = $node->getData();

            $isActive = $this->checkLeafIsActive($node);

            $html .= '<li';
            if ($isActive) {
                $html .= ' class="active"';
            }
            $html .= '>';
            $html .= '<a href="#"><i class="'.$data->icone.'"></i> '.$data->nome;
            $html .= '<span class="pull-right-container">';
            $html .= '<i class="fa fa-angle-left pull-right"></i>';
            $html .= '</span></a>';

            $html .= '<ul class="treeview-menu">';

            foreach($node->getChilds() as $child) {
                $html .= $this->buildMenu($child);
            }

            $html .= '</ul></li>';
        }

        // Verifica se é uma folha
        if ($node->getFather() && $node->isLeaf()) {

            $data = $node->getData();

            $isActive = $this->checkLeafIsActive($node);

            $html .= '<li';
            if ($isActive) {
                $html .= ' class="active"';
            }
            $html .= '>';
            $html .= '<a href="'.route($data->rota).'"><i class="'.$data->icone.'"></i> '.$data->nome.'</a></li>';
        }

        return $html;
    }
}

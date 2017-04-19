<?php

namespace Modulos\Seguranca\Repositories;

use Harpia\Menu\MenuTree;
use Modulos\Seguranca\Models\MenuItem;

class MenuItemRepository
{
    public function find($id)
    {
        return MenuItem::find($id);
    }

    public function getCategorias($moduloId)
    {
        return MenuItem::where([
                ['modulos_id', '=', $moduloId],
                ['visivel', '=', 1],
                ['menu_itens_pai', '=', null]
            ])
            ->orderBy('ordem', 'asc')
            ->get();
    }

    public function getItensFilhos($moduloId, $categoriaId)
    {
        return MenuItem::where([
                ['modulos_id', '=', $moduloId],
                ['menu_itens_pai', '=', $categoriaId],
                ['visivel', '=', 1]
            ])
            ->orderBy('ordem', 'asc')
            ->get();
    }

    public function isCategoria($menuItemId)
    {
        $menuItem = MenuItem::where('id', '=', $menuItemId)->get()->shift();

        if (isset($menuItem->menu_itens_pai)) {
            return false;
        }

        if (isset($menuItem->rota)) {
            return false;
        }

        return true;
    }

    public function isSubCategoria($menuItemId)
    {
        $menuItem = MenuItem::where('id', '=', $menuItemId)->get()->shift();

        if (!isset($menuItem->menu_itens_pai)) {
            return false;
        }

        if (isset($menuItem->rota)) {
            return false;
        }

        return true;
    }

    public function isItem($menuItemId)
    {
        $menuItem = MenuItem::where('id', '=', $menuItemId)->get()->shift();

        if (isset($menuItem->menu_itens_pai) && isset($menuItem->rota)) {
            return true;
        }

        return false;
    }
}

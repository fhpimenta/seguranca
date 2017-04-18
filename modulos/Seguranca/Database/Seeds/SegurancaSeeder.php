<?php

namespace Modulos\Seguranca\Database\Seeds;

use Illuminate\Database\Seeder;
use Modulos\Seguranca\Models\MenuItem;
use Modulos\Seguranca\Models\Modulo;
use Modulos\Seguranca\Models\Perfil;
use Modulos\Seguranca\Models\Permissao;

class SegurancaSeeder extends Seeder
{
    public function run()
    {
        $this->call(UsersSeeder::class);

        // Criar o modulo Seguranca
        $modulo = Modulo::create([
            'nome' => 'Segurança',
            'slug' => 'seguranca',
            'icone' => 'fa fa-lock',
            'classes' => 'bg-green'
        ]);

        // Cria perfil de Administrador
        $perfil = Perfil::create([
            'modulos_id' => $modulo->id,
            'nome' => 'Administrador'
        ]);

        // Criar permissao index do modulo Seguranca
        $permissaoIndex = Permissao::create([
            'nome' => 'index',
            'rota' => 'seguranca.dashboard.index'
        ]);

        $permissaoModulosIndex = Permissao::create([
            'nome' => 'index',
            'rota' => 'seguranca.modulos.index'
        ]);

        // Atirbuir permissao index ao perfil de Administrador
        $perfil->permissoes()->attach([$permissaoIndex->id, $permissaoModulosIndex->id]);

        // Atribuir perfil de Administrador ao usuario criado
        $perfil->users()->attach(1);

        // Criando itens no menu

        // Categoria Dashboard
        $dashboard = MenuItem::create([
            'modulos_id' => $modulo->id,
            'nome' => 'Dashboard',
            'icone' => 'fa fa-dashboard',
            'visivel' => 1,
            'ordem' => 1
        ]);

        $homeItem = MenuItem::create([
            'modulos_id' => $modulo->id,
            'menu_itens_pai' => $dashboard->id,
            'nome' => 'Inicio',
            'icone' => 'fa fa-home',
            'rota' => 'seguranca.dashboard.index',
            'visivel' => 1
        ]);

        // Categoria Cadastros
        $cadastro = MenuItem::create([
            'modulos_id' => $modulo->id,
            'nome' => 'Cadastros',
            'icone' => 'fa fa-plus',
            'visivel' => 1,
            'ordem' => 2
        ]);

        $moduloItem = MenuItem::create([
            'modulos_id' => $modulo->id,
            'menu_itens_pai' => $cadastro->id,
            'nome' => 'Modulos',
            'icone' => 'fa fa-cubes',
            'rota' => 'seguranca.modulos.index',
            'visivel' => 1,
            'ordem' => 1
        ]);

    }
}
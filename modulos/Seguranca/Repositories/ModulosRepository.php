<?php

namespace Modulos\Seguranca\Repositories;

use Modulos\Seguranca\Models\Modulo;
use Cache;

class ModulosRepository
{
    protected $model;

    public function __construct(Modulo $model)
    {
        $this->model = $model;
    }

    public function getByUser($userId)
    {
        $modulos = $this->model
                        ->join('perfis AS perf', 'perf.modulos_id', '=', 'modulos.id')
                        ->join('perfis_has_users AS phu', 'phu.perfis_id', '=', 'perf.id')
                        ->select('modulos.*')
                        ->where('phu.users_id', '=', $userId)
                        ->get();

        $permissoes = Cache::get('PERMISSOES_'.$userId);

        for ($i = 0; $i < $modulos->count(); $i++) {
            if (!in_array($modulos[$i]->slug.'.index', $permissoes)) {
                unset($modulos[$i]);
            }
        }

        return $modulos;
    }
}
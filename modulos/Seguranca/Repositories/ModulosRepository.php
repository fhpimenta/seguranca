<?php

namespace Modulos\Seguranca\Repositories;

use DB;
use Cache;

class ModulosRepository
{
    public function getByUser($userId)
    {
        $modulos = DB::table('modulos')
                        ->join('perfis AS perf', 'perf.modulos_id', '=', 'modulos.id')
                        ->join('perfis_has_users AS phu', 'phu.perfis_id', '=', 'perf.id')
                        ->select('modulos.*')
                        ->where('phu.users_id', '=', $userId)
                        ->get();

        $permissoes = Cache::get('PERMISSOES_'.$userId);

        for ($i = 0; $i < $modulos->count(); $i++) {
            if (!in_array($modulos[$i]->slug.'.dashboard.index', $permissoes)) {
                unset($modulos[$i]);
            }
        }

        return $modulos;
    }
}
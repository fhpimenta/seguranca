<?php

namespace Modulos\Seguranca\Repositories;

use Modulos\Seguranca\Models\Permissao;

class PermissoesRepository
{
    protected $model;

    public function __construct(Permissao $model)
    {
        $this->model = $model;
    }
}

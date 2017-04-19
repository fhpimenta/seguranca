<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = ['nome', 'rota'];

    public function perfis()
    {
        return $this->belongsToMany('Modulo\Seguranca\Models\Perfil', 'permissoes_has_perfis', 'permissoes_id', 'perfis_id');
    }
}

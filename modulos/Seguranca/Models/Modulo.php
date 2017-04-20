<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = ['nome', 'slug', 'icone'];

    public function perfis()
    {
        return $this->hasMany('Modulos\Seguranca\Models\Perfil', 'modulos_id');
    }

    public function menu_itens()
    {
        return $this->hasMany('Modulos\Seguranca\Models\MenuItem', 'modulos_id');
    }
}

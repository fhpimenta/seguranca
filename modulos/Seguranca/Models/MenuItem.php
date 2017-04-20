<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $table = 'menu_itens';

    protected $fillable = ['modulos_id', 'nome', 'icone', 'visivel'];

    public function modulo()
    {
        return $this->belongsTo('Modulos\Seguranca\Models\Modulo', 'modulos_id');
    }

    public function item_pai()
    {
        return $this->belongsTo('Modulos\Seguranca\Models\MenuItem', 'menu_itens_pai');
    }

    public function itens_filhos()
    {
        return $this->hasMany('Modulos\Seguranca\Models\MenuItem', 'menu_itens_pai');
    }
}

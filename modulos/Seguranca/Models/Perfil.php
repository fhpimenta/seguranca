<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';

    protected $fillable = ['modulos_id', 'nome'];

    public function modulo()
    {
        return $this->belongsTo('Modulos\Seguranca\Models\Modulo', 'modulos_id');
    }

    public function users()
    {
        return $this->belongsToMany('Modulos\Seguranca\Models\User', 'perfis_has_users', 'perfis_id', 'users_id');
    }

    public function permissoes()
    {
        return $this->belongsToMany('Modulos\Seguranca\Models\Permissao', 'permissoes_has_perfis', 'perfis_id', 'permissoes_id');
    }
}
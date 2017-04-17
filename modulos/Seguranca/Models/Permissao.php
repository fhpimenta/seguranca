<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = ['nome', 'rota'];
}
<?php

namespace Modulos\Seguranca\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = ['nome', 'icone'];
}
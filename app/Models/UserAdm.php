<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAdm extends Model
{
    use HasFactory;
    protected $table = 'useres_adm';
    protected $timestamp = true;
    protected $fillable = [
        'cpf',
        'nome',
        'mail',
        'escola_id',
        'papel_id'
    ];
}

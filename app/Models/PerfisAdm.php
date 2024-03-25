<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfisAdm extends Model
{
    use HasFactory;
    protected $table = 'perfis_adm';
    protected $timestamp = true;
    protected $fillable = [
        'sigla',
        'descricao',
    ];
}

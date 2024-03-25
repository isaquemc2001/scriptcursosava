<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class DiretorioRegional extends Model
{
    use HasFactory, Uuid;
    protected $table = 'diretorias_regionais';
    protected $timestamp = true;
    protected $fillable = [
        'nome',
        'codigo',
    ];

}

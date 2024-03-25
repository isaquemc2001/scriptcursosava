<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory, Uuid;
    protected $table = 'alunos';
    protected $timestamp = true;
    protected $fillable = [
        'codigo_aluno',
        'nome',
        'email'
    ];

}

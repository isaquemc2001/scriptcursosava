<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory; 
    protected $table = 'turmas';
    protected $timestamp = true;
    protected $fillable = [
        'letra',
        'codigo',
        'id_serie'
    ];


    public function disciplinas(){
        return $this->hasMany(Disciplina::class, "id_turma");
    }
}

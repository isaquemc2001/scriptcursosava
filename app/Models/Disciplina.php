<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;
    protected $table = 'disciplinas';
    protected $timestamp = true;
    protected $fillable = [
        'codigo',
        'titulo',
        'id_turma'
    ];


    public static function getDisciplinasPorEscola($escola_id)
    {
        return Disciplina::join('turmas','turmas.id','disciplinas.id_turma')
                            ->join('series','series.id','turmas.id_serie')
                            ->join('escola_turno', 'escola_turno.id','series.id_escola_turno')
                            ->join('escolas','escolas.id','escola_turno.cod_escola')
                            ->where('escolas.id',$escola_id)
                            ->select('disciplinas.*')
                           ->get();
    }
}

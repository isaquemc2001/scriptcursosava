<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;
    protected $table = 'escolas';
    protected $timestamp = true;
    protected $fillable = [
        'nome',
        'codigo',
        'diretoria_id'
    ];


    public function diretoria()
    {
        return $this->belongsTo(DiretorioRegional::class, 'diretoria_id', 'id');
    }

    public static function getIbByCourseId($codigo)
    {
        return self::join('escola_turno as et', 'et.cod_escola', 'escolas.id')
                    ->join('series as s', 's.id_escola_turno', 'et.id')
                    ->join('turmas as t', 't.id_serie', 's.id')
                    ->join('disciplinas as d', 'd.id_turma', 't.id')
                    ->where('d.codigo',$codigo)
                    ->select('escolas.id')
                    ->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola_turno extends Model
{
    use HasFactory;
    protected $table = 'escola_turno';
    protected $timestamp = true;
    protected $fillable = [
        'codigo',
        'cod_escola',
        'cod_turno'
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'cod_turno', 'id');
    }
}

<?php

namespace Database\Seeders;

use App\Models\DiretorioRegional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiretoriasRegionaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $diretorias = [
            [
                'codigo' => "DRE01",
                'nome'   => "Diretoria Geral 1",
                'sigla'  => "DRE01"
            ],
            [
                'codigo' => "DRE02",
                'nome'   => "Diretoria Geral 2",
                'sigla'  => "DRE02"
            ],
            [
                'codigo' => "DRE03",
                'nome'   => "Diretoria Geral 3",
                'sigla'  => "DRE03"
            ],
            [
                'codigo' => "DRE04",
                'nome'   => "Diretoria Geral 4",
                'sigla'  => "DRE04"
            ],
            [
                'codigo' => "DRE05",
                'nome'   => "Diretoria Geral 5",
                'sigla'  => "DRE05"
            ],
        ];

        /*$index = 1;
        foreach ($diretorias as $diretoria) {
            $diretoria['id'] = $index++;
            if (!DiretorioRegional::find($diretoria['id'])) {
                DiretorioRegional::create($diretoria);
            }
        }*/
    }
}

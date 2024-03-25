<?php

namespace Database\Seeders;

use App\Models\Escola;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EscolasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $escolas = [
            [
                'codigo' => 'DRE01005874',
                'nome'   => 'CENTRO DE EXCELÊNCIA MANUEL BOMFIM',
                'diretoria_id' => 1
            ],
            [
                'codigo' => 'DRE01008414',
                'nome'   => 'COLEGIO ESTADUAL SEVERIANO CARDOSO',
                'diretoria_id' => 1
            ],
            [
                'codigo' => 'DRE01007123',
                'nome'   => 'CENTRO DE EXCELÊNCIA LEONARDO GOMES DE CARVALHO LEITE',
                'diretoria_id' => 1
            ],
            [
                'codigo' => 'DRE01003096',
                'nome'   => 'COLÉGIO ESTADUAL OTÁVIO DE SOUZA LEITE',
                'diretoria_id' => 1
            ],
            [
                'codigo' => 'DRE03001234',
                'nome'   => 'COLEGIO ESTADUAL MURILO BRAGA',
                'diretoria_id' => 3
            ],
            [
                'codigo' => 'DRE03004937',
                'nome'   => 'COLEGIO ESTADUAL EDUARDO SILVEIRA',
                'diretoria_id' => 3
            ],
            [
                'codigo' => 'DRE05004037',
                'nome'   => 'ESCOLA DE 1° GRAU NAÇÕES UNIDAS',
                'diretoria_id' => 5
            ],
        ];

        $index = 1;
        foreach ($escolas as $escolas) {
            $escolas['id'] = $index++;
            if (!Escola::find($escolas['id'])) {
                Escola::create($escolas);
            }
        }
    }
}

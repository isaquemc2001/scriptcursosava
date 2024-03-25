<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurnosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $turnos = [

            [
                'id'   => 1,
                'nome' => 'Manhã',
                'codigo' => 1
            ],

            [
                'id'   => 2,
                'nome'  => 'Tarde',
                'codigo' => 2
            ],

            [
                'id'   => 3,
                'nome' => 'Noite',
                'codigo' => 3
            ],

            [
                'id'   => 4,
                'nome' => 'Integral',
                'codigo' => 4
            ],

            [
                'id'   => 6,
                'nome' => 'Integral',
                'codigo' => 6
            ],
        ];


        $index = 1;
        foreach ($turnos as $turno) {
            $turno['id'] = $index++;
            if (!Turno::find($turno['id'])) {
                Turno::create($turno);
            }
        }
    }
}

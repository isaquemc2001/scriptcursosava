<?php

namespace Database\Seeders;

use App\Models\Disciplina;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisciplinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $disciplinas = [
            // letras ingles
            // [
            //     'codigo' => 'LETR0810',
            //     'titulo' => 'LINGUÍSTICA APLICADA: LINGUAGEM E FUNDAMENTOS TEÓRICOS E METODOLÓGICOS I',
            //     'id_turma' => 2
            // ],
            // [
            //     'codigo' => 'LETR0811',
            //     'titulo' => 'POLÍTICAS LINGUÍSTICAS E INTERNACIONALIZAÇÃO',
            //     'id_turma' => 2
            // ],
            // [
            //     'codigo' => 'LETR0809',
            //     'titulo' => 'SEMINÁRIOS I - TEMAS ATUAIS EM EDUCAÇÃO',
            //     'id_turma' => 2
            // ],
            // [
            //     'codigo' => 'LETR0808',
            //     'titulo' => 'LÍNGUA INGLESA: LINGUAGENS E IDENTIDADES',
            //     'id_turma' => 2
            // ],

            // // ciencias sociais
            // [
            //     'codigo' => 'SOCIA0099',
            //     'titulo' => 'SOCIOLOGIA I',
            //     'id_turma' => 3
            // ],
            // [
            //     'codigo' => 'SOCIA0098',
            //     'titulo' => 'POLÍTICA I',
            //     'id_turma' => 3
            // ],
            // [
            //     'codigo' => 'SOCIA0100',
            //     'titulo' => 'FILOSOFIA E CIÊNCIAS. SOCIAIS',
            //     'id_turma' => 3
            // ],
            // [
            //     'codigo' => 'SOCIA0101',
            //     'titulo' => 'SEMINÁRIOS I - TEMAS ATUAIS EM EDUCAÇÃO',
            //     'id_turma' => 3
            // ],
            // [
            //     'codigo' => 'SOCIA0097',
            //     'titulo' => 'ANTROPOLOGIA I',
            //     'id_turma' => 3
            // ],

            // // dança
            // [
            //     'codigo' => 'DANCA0205',
            //     'titulo' => 'IMPROVISAÇÃO EM DANÇA',
            //     'id_turma' => 4
            // ],
            // [
            //     'codigo' => 'DANCA0204',
            //     'titulo' => 'MÚSICA E MOVIMENTO',
            //     'id_turma' => 4
            // ],
            // [
            //     'codigo' => 'DANCA0201',
            //     'titulo' => 'ESTUDOS CINESIOLÓGICOS EM DANÇA',
            //     'id_turma' => 4
            // ],
            // [
            //     'codigo' => 'DANCA0202',
            //     'titulo' => 'HISTÓRIA DA DANÇA',
            //     'id_turma' => 4
            // ],
            // [
            //     'codigo' => 'DANCA0203',
            //     'titulo' => 'DANÇAS BRASILEIRAS',
            //     'id_turma' => 4
            // ],

            // // letras portugues
            // [
            //     'codigo' => 'LETRV0119',
            //     'titulo' => 'TEORIAS DE LINGUAGEM',
            //     'id_turma' => 5
            // ],
            // [
            //     'codigo' => 'LETRV0120',
            //     'titulo' => 'LINGUAGENS E TECNOLOGIA',
            //     'id_turma' => 5
            // ],
            // [
            //     'codigo' => 'LETRV0118',
            //     'titulo' => 'LETRAMENTO ACADÊMICO',
            //     'id_turma' => 5
            // ],
            // [
            //     'codigo' => 'LETRV0117',
            //     'titulo' => 'FUNDAMENTOS GRAMATICAIS',
            //     'id_turma' => 5
            // ],
            // [
            //     'codigo' => 'LETRV0116',
            //     'titulo' => 'ESTUDOS LITERÁRIOS',
            //     'id_turma' => 5
            // ],

            // fisica
            // [
            //     'codigo' => 'FISI0358',
            //     'titulo' => 'QUÍMICA',
            //     'id_turma' => 6
            // ],
            // [
            //     'codigo' => 'FISI0356',
            //     'titulo' => 'ELEMENTOS DE MATEMÁTICA I',
            //     'id_turma' => 6
            // ],
            // [
            //     'codigo' => 'FISI0359',
            //     'titulo' => 'EVOLUÇÃO DAS IDEIAS DA FÍSICA',
            //     'id_turma' => 6
            // ],
            // [
            //     'codigo' => 'FISI0357',
            //     'titulo' => 'ELEMENTOS DE FÍSICA',
            //     'id_turma' => 6
            // ],

            // // ciencias biologicas
            // [
            //     'codigo' => 'BIOL0313',
            //     'titulo' => 'BIOLOGIA CELULAR APLICADA AO ENSINO',
            //     'id_turma' => 7
            // ],
            // [
            //     'codigo' => 'BIOL0314',
            //     'titulo' => 'DIVERSIDADE BOTÂNICA I',
            //     'id_turma' => 7
            // ],
            // [
            //     'codigo' => 'BIOL0310',
            //     'titulo' => 'QUÍMICA DA VIDA',
            //     'id_turma' => 7
            // ],
            // [
            //     'codigo' => 'BIOL0312',
            //     'titulo' => 'BIOQUÍMICA APLICADA AO ENSINO',
            //     'id_turma' => 7
            // ],
            // [
            //     'codigo' => 'BIOL0309',
            //     'titulo' => 'FÍSICA DA VIDA',
            //     'id_turma' => 7
            // ],

            // // quimica
            // [
            //     'codigo' => 'QUI0310',
            //     'titulo' => 'MATEMÁTICA BÁSICA',
            //     'id_turma' => 8
            // ],
            // [
            //     'codigo' => 'QUI0311',
            //     'titulo' => 'QUÍMICA E ENSINO',
            //     'id_turma' => 8
            // ],
            // [
            //     'codigo' => 'QUI0309',
            //     'titulo' => 'LABORATÓRIO DE QUÍMICA',
            //     'id_turma' => 8
            // ],
            // [
            //     'codigo' => 'QUI0312',
            //     'titulo' => 'SEMINÁRIOS I - TEMAS ATUAIS EM EDUCAÇÃO',
            //     'id_turma' => 8
            // ],
            // [
            //     'codigo' => 'QUI0308',
            //     'titulo' => 'QUÍMICA GERAL',
            //     'id_turma' => 8
            // ],

            // // matematica
            // [
            //     'codigo' => 'MAT0164',
            //     'titulo' => 'PRÉ-CÁLCULO',
            //     'id_turma' => 9
            // ],
            // [
            //     'codigo' => 'MAT0166',
            //     'titulo' => 'METODOLOGIA DO ENSINO DE MATEMÁTICA',
            //     'id_turma' => 9
            // ],
            // [
            //     'codigo' => 'MAT0165',
            //     'titulo' => 'VETORES E GEOMETRIA ANALÍTICA',
            //     'id_turma' => 9
            // ],
            // [
            //     'codigo' => 'MAT0167',
            //     'titulo' => 'SEMINÁRIO I – TEMAS ATUAIS EM EDUCAÇÃO',
            //     'id_turma' => 9
            // ],
            // [
            //     'codigo' => 'MAT0163',
            //     'titulo' => 'FUNDAMENTOS DE MATEMÁTICA',
            //     'id_turma' => 9
            // ],
        ];

        foreach($disciplinas as $disciplina){
            Disciplina::create($disciplina);
        }
    }
}

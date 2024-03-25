<?php

namespace App\Http\Controllers;

use App\Helper\Mask;
use App\Models\Aluno;
use App\Models\DiretorioRegional;
use App\Models\Disciplina;
use App\Models\Escola;
use App\Models\Escola_turno;
use App\Models\PerfisAdm;
use App\Models\Professor;
use App\Models\Serie;
use App\Models\Turma;
use App\Models\Turno;
use App\Models\UserAdm;
use App\Services\MoodleHomologService;
use App\Services\MoodleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Mockery\Expectation;

class DiretoriasController extends Controller
{
    public function import() {
        set_time_limit(0);
        if(($handle = fopen("C:\Users\jvdso\Downloads\/professores-30-10.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            while (($data = fgetcsv($handle,1000,";")) !== false){

                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }

                try{
                    $data = [
                        'diretoria_codigo'  => $data[0],
                        'diretoria_nome'    => $data[1],
                        'escola_codigo'     => $data[4],
                        'escola_nome'       => $data[5],
                        'turno_codigo'      => $data[12],
                        'turno_nome'        => $data[13],
                        'serie_codigo'      => $data[10],
                        'serie_nome'        => $data[9] . "/" . $data[11],
                        'turma_codigo'      => $data[14],
                        'turma_nome'        => $data[15],
                        'disciplina_codigo' => $data[18],
                        'discplina_nome'    => $data[19]
                    ];

                    $diretoria = DiretorioRegional::where('codigo', $data['diretoria_codigo'])->first();

                    if(!$diretoria){
                        $diretoria = [
                            'codigo' => $data['diretoria_codigo'],
                            'nome'   => $data['diretoria_nome']
                        ];
                        $diretoria =   DiretorioRegional::create($diretoria);
                    }

                    $escola = Escola::where('codigo',$data['escola_codigo'])->first();

                    if(!$escola){
                        $escola = [
                            'nome'          => $data['escola_nome'],
                            'codigo'        => $data['escola_codigo'],
                            'diretoria_id'  => $diretoria->id
                        ];
                         $escola = Escola::create($escola);
                    }

                    $escola_turno = Escola_turno::where('codigo', $escola->codigo . "-" . $data['turno_codigo'])->first();

                    if(!$escola_turno){
                        $escola_turno = [
                            'codigo' => $escola->codigo . "-" . $data['turno_codigo'],
                            'cod_escola' => $escola->id,
                            'cod_turno' => $data['turno_codigo']
                        ];
                        $escola_turno = Escola_turno::create($escola_turno);
                    }

                    $serie = Serie::where('codigo', $escola_turno->codigo . "-" . $data['serie_codigo'])->first();

                    if(!$serie){
                        $serie = [
                            'codigo' => $escola_turno->codigo . "-" . $data['serie_codigo'],
                            'descricao' => $data['serie_nome'],
                            'id_escola_turno' =>  $escola_turno->id
                        ];
                        $serie = Serie::create($serie);
                    }

                    $turma = Turma::where('codigo', $data['turma_codigo'])->first();

                    if(!$turma){
                        $turma = [
                            'codigo'    => $data['turma_codigo'],
                            'letra'     => $data['turma_nome'],
                            'id_serie'  => $serie->id
                        ];
                        $turma = Turma::create($turma);
                    }

                    $disciplina = Disciplina::where('codigo',$turma->codigo.$data['disciplina_codigo'])->first();
                    if(!$disciplina){
                            $disciplina = [
                                'codigo' => $turma->codigo.$data['disciplina_codigo'],
                                'titulo' => $data['discplina_nome'],
                                'id_turma' => $turma->id
                            ];
                            $disciplina = Disciplina::create($disciplina);

                    }


                }catch(\Exception $e){
                   dd($data,$e);
                }

            }
        }

    }

    public function criarEstrutura(){
        $diretorias = DiretorioRegional::all();
        $moodleService = new MoodleService();
        $ae = 0;
        foreach ($diretorias as $diretoria) {
            $ae++;
            $diretoria_categoria = $moodleService->getCategory($diretoria->codigo);

            if(!$diretoria_categoria){ //caso a diretoria não exista faz o cadastro
                $diretoria_categoria = $moodleService->createCategory(['name' => $diretoria->nome, 'cod' => $diretoria->codigo]);
            }

            $escolas = Escola::Join('escola_turno as et','et.cod_escola','escolas.id')
                            ->join('series as s','s.id_escola_turno','et.id')
                            ->join('turmas as t','t.id_serie','s.id')
                            ->join('disciplinas as d','d.id_turma','t.id')
                            ->where('d.copied',false)
                            ->where('diretoria_id',$diretoria->id)
                            ->groupBy('escolas.codigo','escolas.id','escolas.nome')
                            ->select('escolas.codigo','escolas.id','escolas.nome')
                            ->get();



            foreach ($escolas as $escola) {

                $escola_categoria = $moodleService->getCategory($escola->codigo);

                if(!$escola_categoria){ //caso a escola não exista faz o cadastro
                    $escola_categoria = $moodleService->createCategory(['name' => $escola->nome, 'cod' => $escola->codigo],$diretoria_categoria[0]['id'] );
                }

                $turnos = Escola_turno::where('cod_escola',$escola->id)->get();

                foreach ($turnos as $turno) {
                    $turno_categoria = $moodleService->getCategory($turno->codigo);

                    if(!$turno_categoria){
                        $turno_categoria = $moodleService->createCategory(['name' => $turno->turno->nome, 'cod' => $turno->codigo],$escola_categoria[0]['id'] );
                    }

                    $series = Serie::where('id_escola_turno',$turno->id)->get();

                    foreach ($series as $serie) {
                        $serie_categoria = $moodleService->getCategory($serie->codigo);

                        if(!$serie_categoria){
                            $serie_categoria = $moodleService->createCategory(['name' => $serie->descricao, 'cod' => $serie->codigo,],$turno_categoria[0]['id']);
                        }

                        $turmas = Turma::where('id_serie',$serie->id)->get();
                        foreach ($turmas as $turma) {
                            $turma_categoria = $moodleService->getCategory($turma->codigo);
                            if(!$turma_categoria){
                                $turma_categoria = $moodleService->createCategory(['name' => $turma->letra, 'cod' => $turma->codigo],$serie_categoria[0]['id']);

                            }
                        }

                    }

                }
            }
        }

    }

    public function cadastrarDisciplinas() {
        $moodleService = new MoodleService();
        $turmas = Turma::join('disciplinas','disciplinas.id_turma','turmas.id')
        ->where('disciplinas.copied', false)
        ->groupBy('turmas.codigo','turmas.id')
        ->orderBy('turmas.codigo')
        ->select("turmas.codigo", 'turmas.id')
        ->get();
        foreach($turmas as $turma){
            $disciplinas = Disciplina::where('id_turma',$turma->id)->where('copied',false)->get();
            $turma_categoria = $moodleService->getCategory($turma->codigo);
            $moodleService->createCourses($disciplinas, $turma_categoria[0]['id']);
        }

    }

    public function copiarCurso(){
        $moodleService = new MoodleService();
        $courseToBeCopied =  $moodleService->getCourses('idnumber','curso_base');
        // dd($courseToBeCopied );
        // print_r($courseToBeCopied);
        // exit;
        if($courseToBeCopied[0]){
            $courseToBeCopied = $courseToBeCopied[0];
            $classes = Disciplina::get();
            foreach($classes as $class){
                $category = $moodleService->getCategory($class->id_turma);
                if($category && $category[0]){
                    $category = $category[0];
                    $classSubjects = $moodleService->getCourses('category',$category['id']);
                    // print_r($classSubjects);
                    // exit;
                    try{
                        foreach($classSubjects as $classSubject){
                            $disciplina = Disciplina::where('codigo', $classSubject['shortname'])->first();
                            if($disciplina && !$disciplina->copied){
                                $moodleService->copyCourse($courseToBeCopied['id'], $classSubject['id']);
                                $disciplina->copied = True;
                                $disciplina->save();

                            }
                        }
                    }catch(Exception $e){

                    }
                }
            }
        }
    }

    public function gerarCSVAlunos(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        if(($handle = fopen("C:\Users\jvdso\Downloads\/professores-23-11.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            $users = [];
            $cont = 0;
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }
                    try{
                        $user_name = explode(" ", $data[1],2);


                        $user = [
                            'username'  => $data[2],
                            'email'     => $data[3],
                            'firstname' => $user_name[0],
                            'lastname'  => isset($user_name[1]) ? $user_name[1] : '' ,
                            'password'  => $data[2],
                            'city'      => '',
                            'country'   => 'BR',
                            'lang'      => 'pt_br',
                            'course1'   =>  "1231231231",
                            'type1'     => 1,
                            'group1'    => ''
                        ];

                        $users[$index-1] = $user;
                        $index++;
                    }catch(Exception $e){
                        $cont++;
                    }


            }
            $index2 = 0;
            $index_titulo = 0;
            $cabecalho = [
                'username',
                'email',
                'firstname',
                'lastname',
                'password',
                'city',
                'country',
                'lang',
                'course1',
                'type1',
                'group1'
            ];
            $count = 1;
            foreach( $users as $user){
                if($index2==0){
                    $out = fopen( "C:\Users\jvdso\Downloads\professores_disciplina".$count.".csv", 'w' );
                    fputcsv( $out, $cabecalho, ";");
                }
                    fputcsv( $out, $user, ";");
                    $index2++;
                if($index2 == 10000){
                    fclose( $out );
                    $index2 = 0;
                    $count++;
                }
            }

        }
    }

    public function gerarCSVProfessores(){
        if(($handle = fopen("C:\Users\jvdso\Downloads\professores-31-10.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            $users = [];
            $naoCadastrados = [];
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }
                $data[16] = str_replace(".","",$data[16]);
                $data[16] = str_replace("-","",$data[16]);
                if(strlen( $data[16]) < 11){
                    $data[16] = str_pad($data[16], 11, "0", STR_PAD_LEFT);
                }

                $user_name = explode(" ", $data['15'],2);

                $user = [
                    'username'  => "$data[16]",
                    'email'     => $data['17'],
                    'firstname' => $user_name[0],
                    'lastname'  => isset($user_name[1]) ? $user_name[1] : '' ,
                    'password'  => "$data[16]",
                    'city'      => '',
                    'country'   => 'BR',
                    'lang'      => 'pt_br',
                    'course1'   =>  $data[12].$data[18],
                    'type1'     => 1,
                    'group1'    => ''
                ];
                    $users[$index-1] = $user;
                    $index++;
            }



            $cabecalho = [
                'username',
                'email',
                'firstname',
                'lastname',
                'password',
                'city',
                'country',
                'lang',
                'course1',
                'type1',
                'group1'
            ];
            $out = fopen( 'C:\Users\jvdso\Downloads\professores-disciplina.csv', 'w' );
            fputcsv( $out, $cabecalho, ";");
            foreach( $users as $user){
                if(strlen($user['username']) < 11){
                    $user['username'] =  str_pad($user['username'] , 11 , '0' , STR_PAD_LEFT);

                }
                $user['username'] =  "*-" . $user['username'];
                $user['password'] = $user['username'];
                fputcsv( $out, $user, ";");

            }
            fclose( $out );

            $out = fopen( 'C:\Users\jvdso\Downloads\naoCadastrados.csv', 'w' );
            fputcsv( $out, $cabecalho, ";");
            foreach( $naoCadastrados as $naoCadastrado){
                if(strlen($naoCadastrado['CPF']) < 11){
                    $naoCadastrado['CPF'] =  str_pad($naoCadastrado['CPF'] , 11 , '0' , STR_PAD_LEFT);

                }
                $naoCadastrado['CPF'] =  "*-" . $naoCadastrado['CPF'];

                fputcsv( $out, $naoCadastrado, ";");

            }
            fclose( $out );

           // dd($naoCadastrados);

        }
    }











    public function cadastrarProfessores() {
        set_time_limit(0);
        $moodleService = new MoodleService();

        if(($handle = fopen("C:\Users\jvdso\Downloads\disciplina-professor.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;

            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }
                if(strlen( $data[12]) < 11){
                    $data[12] = str_pad($data[12], 11, "0", STR_PAD_LEFT);
                }

                $professor = Professor::where('cpf', $data[12] )->first();

                $user_name = explode(" ", $professor->nome,2);
                $user = [
                    'username' => $data[12],
                    'password' => $data[12],
                    'firstname' => $user_name[0],
                    'lastname'  => $user_name[1],
                    'email'     => $data[14]
                ];

                $moodleService->createUser($user);

                $index++;

            }


        }


        //dd($parans);

    }

    public function AssociarProfessorDisciplina() {
        set_time_limit(0);
        $response = Http::get('https://ava.seduc.se.gov.br/ava-escolar/login/token.php?username=01326881582&password=Jv84567%40&service=criar_curso'); //busca o token de acesso a partir das credenciais
        $token = $response->json()['token'];

        if(($handle = fopen("C:\Users\jvdso\Downloads\professores.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;

            while (($data = fgetcsv($handle,1000,";")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }
                $disciplina_explode = explode(" ", $data[7]);
                if($disciplina_explode[count($disciplina_explode)-1] == "I"){
                    $parans = [
                        'wstoken'                   => $token,
                        'wsfunction'                => 'core_user_get_users',
                        'moodlewsrestformat'        => 'json',
                        'criteria[0][key]'          => 'username',
                        'criteria[0][value]'        => $data[13]
                    ];

                    $response = Http::asForm()->post('https://ava.seduc.se.gov.br/ava-escolar/webservice/rest/server.php',$parans);

                    $usuario = $response->json();

                    //dd($usuario['users'][0]);

                    $disciplina_explode = explode(" ", $data[7]);
                    if($disciplina_explode[count($disciplina_explode)-1] == "I"){}

                    $parans = [
                        'wstoken'            => $token,
                        'wsfunction'         => 'core_course_get_courses_by_field',
                        'moodlewsrestformat' => 'json',
                        'field'                => 'shortname',
                        'value'              => "851101294"//($data[8] . $data[6])
                    ];

                    $response = Http::asForm()->post('https://ava.seduc.se.gov.br/ava-escolar/webservice/rest/server.php',$parans);

                    $discplina = $response->json();

                    //dd($discplina['courses'][0]['id']);

                    $parans = [
                        'wstoken'            => $token,
                        'wsfunction'         => 'core_course_get_courses_by_field',
                        'moodlewsrestformat' => 'json',
                        'enrolments[0][roleid]' => 1,
                        'enrolments[0][userid]' => $usuario['users'][0]['id'],
                        'enrolments[0][courseid]' => $discplina['courses'][0]['id'],
                        'enrolments[0][timestart]' => 0,
                        'enrolments[0][timeend]' => 0,
                        'enrolments[0][suspend]' => 0,
                    ];
                    //dd($parans);
                    $response = Http::asForm()->post('https://ava.seduc.se.gov.br/ava-escolar/webservice/rest/server.php',$parans);

                    dd($response->json());
                   //enrol_manual_enrol_users

                }

            }


        }


        //dd($parans);

    }

    public function pegarNotas()
    {
        set_time_limit(0);
        $response = Http::get('http://localhost/login/token.php?username=01326881582&password=Jv84567%40&service=criar_curso'); //busca o token de acesso a partir das credenciais


        $token = $response->json()['token'];
        //$token = '78c4388b4e7192df788005cb6977d215';*/

        $parans = [
            'wstoken'            => $token,
            'wsfunction'         => 'core_course_get_courses_by_field',
            'moodlewsrestformat' => 'json',
            'field'              => 'idnumber',
            'value'              => '8456'
        ];

        $response = Http::asForm()->post('http://localhost/webservice/rest/server.php',$parans);
        $curso = $response->json()['courses'][0];

        $parans = [
            'wstoken'            => $token,
            'wsfunction'         => 'gradereport_user_get_grade_items',
            'moodlewsrestformat' => 'json',
            'courseid'          => $curso['id']
        ];

        $response = Http::asForm()->post('http://localhost/webservice/rest/server.php',$parans);

        dd($response->json());


    }


    public function cadastrarProfessores2(){
        set_time_limit(0);

        if(($handle = fopen("C:\Users\jvdso\Downloads\professores.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }
                if(strlen( $data[2]) < 11){
                    $data[2] = str_pad($data[2], 11, "0", STR_PAD_LEFT);
                }


                $professor = [
                    'cpf'  => $data[2],
                    'nome' => $data[1],
                    'email'=> $data[5]
                ];
                Professor::create($professor);
            }


        }
    }

    public function cadastrarAlunos(){
        set_time_limit(0);

        if(($handle = fopen("C:\Users\jvdso\Downloads\alunos03-10.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }

                $emailFake = str_replace(' ','',$data[1]) . "@mail.com";
                $aluno = [
                    'codigo_aluno'  => str_replace('.','',$data[0]),
                    'nome'          => $data[1],
                    'email'         => $data[5] != null ? $data[5] : $data[6]
                ];
                $aluno['email'] =  ($aluno['email'] == null || $aluno['email'] == 'null') ? $emailFake : $aluno['email'];

                Aluno::create($aluno);
            }


        }
    }

    public function gerarCSVProfessores2(){
        if(($handle = fopen("C:\Users\jvdso\Downloads\/novo_import.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            $users = [];
            $naoCadastrados = [];
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }

                if(strlen( $data[12]) < 11){
                    $data[12] = str_pad($data[12], 11, "0", STR_PAD_LEFT);
                }

                $professor = Professor::where('cpf', $data[12] )->first();
                if($professor){
                    $user = [
                        'dea'       => $data[1],
                        'escola'    => $data[3],
                        'cpf'       => $data[12],
                        'nome'      => $professor->nome,
                        'email'     => $data[14],
                        'turno'     => $data[5],
                        'serie'     => $data[7],
                        'turma'     => $data[9],
                        'unidade'   => $data[11],
                    ];
                        $users[$index-1] = $user;
                        $index++;
                }
            }

            $cabecalho = [
                'DEA',
                'Escola',
                'CPF',
                'Nome',
                'Email',
                'Turno',
                'Serie',
                'Turma',
                'Unidade Curricular',
            ];
            $out = fopen( 'C:\Users\jvdso\Downloads\thirza.csv', 'w' );
            fputcsv( $out, $cabecalho, ";");
            foreach( $users as $user){
                fputcsv( $out, $user, ";");
            }
            fclose( $out );

        }
    }


    public function apagarCategoria(){
        set_time_limit(0);
        $moodleService = new MoodleService();
        $moodleService->deleteCategory();
    }


    public function importarAdministrativo()
    {
        set_time_limit(0);
        if(($handle = fopen("C:\Users\jvdso\Downloads\Diretores_Coordenadores.csv","r") ) !== false ){  //lê arquivo csv
            $index = 0;
            while (($data = fgetcsv($handle,1000,",")) !== false){
                if($index == 0){ //pula a linha do cabeçalho
                    $index++;
                    continue;
                }

                $data = [
                    'escola_id'     => $data[0],
                    'escola_nome'   => $data[1],
                    'user_papel'    => $data[2],
                    'user_nome'     => $data[3] . " " . $data[4],
                    'cpf'           => Mask::remove($data[5],'document'),
                    'email'         => $data[6]
                ];

                if(strlen( $data['cpf']) < 11){
                    $data['cpf'] = str_pad($data['cpf'], 11, "0", STR_PAD_LEFT);
                }

                if($data['escola_id'] == 0){
                    $escola = true;
                }else{
                    $escola = Escola::where('codigo', $data['escola_id'])->first();
                }

                $papel  = PerfisAdm::where('sigla',$data['user_papel'])->first();
                if($escola &&  $papel){

                    try {
                        $adm = [
                            'cpf'       => $data['cpf'],
                            'nome'      => $data['user_nome'],
                            'mail'     => $data['email'],
                            'papel_id'  => $papel['id']

                        ];

                        if($data['escola_id'] != 0){
                            $adm['escola_id'] = $escola['id'];
                        }

                        if(!$data['email'] || !$data['cpf'] || !$data['user_nome'] ){
                            continue;
                        }

                        UserAdm::create($adm);
                    }catch(Exception $e){

                    }

                }


            }


        }
    }

    public function exportarAdministrativo()
    {
        $data = [];
        $users = UserAdm::whereIn('papel_id', [1,2])->get();
        foreach($users as $user){
            $user_nome = explode(" ",$user->nome,2);
            $userData = [
                'username'  => $user->cpf,
                'email'     => $user->mail,
                'firstname' => $user_nome[0],
                'lastname'  => $user_nome[1],
                'password'  => $user->cpf,
                'city'      => '',
                'country'   => 'BR',
                'lang'      => 'pt_br',
                'course1'   =>  '',
                'type1'     => $user->papel_id,
                'group1'    => ''

            ];

            $disciplinas = Disciplina::getDisciplinasPorEscola($user->escola_id);
            foreach($disciplinas as $disciplina){
                $userData['course1'] = $disciplina->codigo;
                $data[] = $userData;
            }
        }

            $cabecalho = [
                'username',
                'email',
                'firstname',
                'lastname',
                'password',
                'city',
                'country',
                'lang',
                'course1',
                'type1',
                'group1'
            ];
            $out = fopen( 'C:\Users\jvdso\Downloads\adminstrativo.csv', 'w' );
            fputcsv( $out, $cabecalho, ";");
            foreach( $data as $d){
                fputcsv( $out, $d, ";");
            }
            fclose( $out );
    }


    public function relatorioAnaCarla(){

        $turmas = [];


        $t = Turma::all();

        foreach($t as $turma){
            $disciplinas = [];
            foreach($turma->disciplinas as $disciplina){
                $codigo_disciplina = str_replace("$turma->codigo", "", $disciplina->codigo);
                if(strlen($codigo_disciplina) == 3){
                    $codigo_disciplina .= "0";
                }
                $disciplinas[] = $codigo_disciplina;
            }
            $turmas[$turma->codigo]= $disciplinas;
        }

        $cabecalho = [
            'cod_turma',
            'cod_disciplina'
        ];
        $out = fopen( 'C:\Users\jvdso\Downloads\codigos_disciplinas.csv', 'w' );
        fputcsv( $out, $cabecalho, ";");
        foreach( $turmas as $cod_turma => $disciplinas){
            foreach($disciplinas as $disciplina){
                $linha = [$cod_turma, $disciplina];
                fputcsv( $out, $linha, ";");
            }
        }
        fclose( $out );




    }

    public function cadastrarUsuario(){
      set_time_limit(0);
      $moodleService = new MoodleService();
      $user = [
        [
          'username' => 'usuariocriadopelaapi2',
          'password' => 'usuariocriadopelaapi2',
          'email'    => 'usuariocriadopelaapi2@teste.com',
          'firstname'=> 'usuario2 criado',
          'lastname' => 'pela api'
        ],
        [
          'username' => 'usuariocriadopelaapi3',
          'password' => 'usuariocriadopelaapi3',
          'email'    => 'usuariocriadopelaapi3@teste.com',
          'firstname'=> 'usuario3 criado',
          'lastname' => 'pela api'
        ]

      ];
      dd($moodleService->createUser($user));

    }

    public function copiarCursoHomolog(){
        $moodleService = new MoodleService();
        $moodleHomologService = new MoodleHomologService();
        $disciplinas = Turma::join('disciplinas','disciplinas.id_turma','turmas.id')
                ->where('disciplinas.copied', false)
                ->select("disciplinas.*")
                ->get();

        foreach($disciplinas as $disciplina){
            $courseDestination =  $moodleService->getCourses('idnumber',"$disciplina->codigo");
            $courseToBeCopied =  $moodleHomologService->getCourses('idnumber',"$disciplina->codigo");
            if($courseDestination && $courseToBeCopied){
                $moodleService->copyCourse($courseToBeCopied[0]['id'], $courseDestination[0]['id']);
                $disciplina->copied = True;
                $disciplina->save();
            }
        }
        /*
        if($courseToBeCopied[0]){
            $courseToBeCopied = $courseToBeCopied[0];

            foreach($classes as $class){
                $category = $moodleService->getCategory($class->codigo);
                if($category && $category[0]){
                    $category = $category[0];
                    $classSubjects = $moodleService->getCourses('category',$category['id']);
                    foreach($classSubjects as $classSubject){
                        $disciplina = Disciplina::where('codigo', $classSubject['shortname'])->first();
                        if($disciplina && !$disciplina->copied){
                            $moodleService->copyCourse($courseToBeCopied['id'], $classSubject['id']);


                        }
                    }
                }
            }
        }*/
    }


    public function gerarRelatorioPaulo(){
        set_time_limit(0);

        $select = [
            "diretorias_regionais.codigo as dr_id",
            "diretorias_regionais.nome as dr_nome",
            "e.codigo as escola_id",
            "e.nome as escola_nome",
            "t.codigo as turno.id",
            "t.nome as turno.nome",
            "s.codigo as serie.codigo",
            "s.descricao as serie.nome",
            "tr.codigo as turma_codigo",
            "tr.letra as turma_nome",
            "d.codigo as disciplina_codigo",
            "d.titulo as disciplina_nome"
        ];

        $dados = DiretorioRegional::select($select)
        ->join('escolas as e', 'e.diretoria_id', 'diretorias_regionais.id')
        ->join('escola_turno as et', 'et.cod_escola', 'e.id')
        ->join('turnos as t', 'et.cod_turno', 't.id')
        ->join('series as s', 's.id_escola_turno', 'et.id')
        ->join('turmas as tr', 'tr.id_serie', 's.id')
        ->join('disciplinas as d', 'd.id_turma', 'tr.id')
        ->get();

        $dadosRelatorio = [];

        foreach($dados as $dado){

            $codSerie = str_replace($dado["escola_id"] . "-" . $dado["turno.id"] . "-","", $dado['serie.codigo'] );
            $codDisciplina = str_replace($dado["turma_codigo"],"", $dado['disciplina_codigo'] );

            $dadosRelatorio[] = [
                $dado["dr_id"],
                $dado["dr_nome"],
                $dado["escola_id"],
                $dado["escola_nome"],
                $dado["turno.id"],
                $dado["turno.nome"],
                $codSerie,
                $dado["serie.nome"],
                $dado["turma_codigo"],
                $dado["turma_nome"],
                $codDisciplina,
                $dado["disciplina_nome"]
            ];
        }

        $cabecalho = [
            "dr_id",
            "dr_nome",
            "escola_id",
            "escola_nome",
            "turno.id",
            "turno.nome",
            "serie.codigo",
            "serie.nome",
            "turma_codigo",
            "turma_nome",
            "disciplina_codigo",
            "disciplina_nome"
        ];
        $out = fopen( 'C:\Users\jvdso\Downloads\relatorio_paulo.csv', 'w' );
        fputcsv( $out, $cabecalho, ";");
        foreach( $dadosRelatorio as $codigos){
            fputcsv( $out, $codigos, ";");
        }
        fclose( $out );
    }

    public function gerarRelatorioAlteracoes(){
        $moodleService = new MoodleService();
        $courseToBeCopied =  $moodleService->getCoursesEss('lastmodified');
        $cursos = [];
        foreach($courseToBeCopied as $curso){
            if($curso['timecreated'] != $curso['timemodified']){
                $escola = Escola::getIbByCourseId($curso['idnumber']);
                $cursos[] = [
                    'shortname' => $curso['shortname'],
                    'fullname'  => $curso['fullname'],
                    'timecreated' => date('n', $curso['timemodified']),
                    'idnumber'    => $curso['idnumber'],
                    'escola'      => $escola != null ? $escola->id : 0
                ];
            }
        }

                // Convertendo o array para uma coleção do Laravel
        $colecao = collect($cursos);

        // Agrupando primeiro pelo atributo 'timecreated' e depois pelo atributo 'escola' e ordenando
        $resultado = $colecao->sortBy('timecreated')->groupBy('timecreated')->map(function ($item) {
            return $item->groupBy('escola');
        });

        $relatorio = [];

        foreach($resultado as $mes => $escolas){
            foreach($escolas as $id => $escola){
                if($id == 0){
                    continue;
                }
                $disciplinas = [];
                $total = count($escola);
                foreach($escola as $disciplina){
                    $disciplinas[] = $disciplina['fullname'];
                }
                $escolaNome = Escola::find($id);
                $escolaNome = $escolaNome->nome;
                $relatorio[$mes][$escolaNome] = [
                    'total' => $total,
                    'disciplinas' => $disciplinas
                ];

            }
        }

        $cabecalho = [
            'mes',
            'escola',
            'disciplina'
        ];
        $out = fopen( 'C:\Users\jvdso\Downloads\relatorioAuteracao.csv', 'w' );
        fputcsv( $out, $cabecalho, ";");
        foreach( $relatorio as $mes => $escola){
            foreach($escola as $id => $dados){
                foreach($dados['disciplinas'] as $disciplina){
                    $linha = [$mes, $id, $disciplina];
                    fputcsv( $out, $linha, ";");
                }
            }

        }
        fclose( $out );

    }

    public function consultarForuns(){
        set_time_limit(0);
        $moodleService = new MoodleService();
        $userIds = [];
        $courseToBeCopied =  $moodleService->getCoursesEss('lastmodified');
        foreach($courseToBeCopied as $curso){
            if($curso['timecreated'] != $curso['timemodified']){
                $foruns = $moodleService->getForunsByCorse($curso['id']);
                foreach($foruns as $forum){
                    $discussions = $moodleService->getDiscussionsByForum($curso['id'], $forum['id']);
                    foreach ($discussions as $topics) {
                        foreach($topics as $discussion){
                            $discussionPosts = $moodleService->getPostsByDiscussion($discussion['id']);
                            dd($discussionPosts);
                            foreach ($discussionPosts as $post) {
                                if(is_string($post)){
                                    print_r($post);
                                    continue;
                                }else{
                                    dd($post);
                                    $userIds[] = $post['userid'];
                                }


                            }
                        }
                    }
                }
            }
        }

        dd($userIds);

    }

}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoodleHomologService
{

    protected $URL;
    protected $token;

    public function __construct()
    {
        set_time_limit(0);
        $url_token = env('MOODLE_HOMOLOG_URL') . "login/token.php?username=" . env('MOODLE_HOMOLOG_USER') . "&password=" . env('MOODLE_HOMOLOG_PASSWORD') . "&service=" . env('MOODLE_HOMOLOG_SERVICE');
        $response = Http::withoutVerifying()->get($url_token);
        $this->token =  $response->json()['token'];
        $this->URL = env('MOODLE_HOMOLOG_URL') . "webservice/rest/server.php";
    }


    public function getCategory($cod)
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_categories',
            'moodlewsrestformat' => 'json',
            'criteria[0][key]'   => 'idnumber',
            'criteria[0][value]' => $cod
        ];

        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
    
        return $response->json();
    }

    public function createCategory(array $category, $parent = null) 
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'          => 'core_course_create_categories',
            'moodlewsrestformat'  => 'json',
            'categories[0][name]' => $category['name'],
            'categories[0][idnumber]' => $category['cod']
        ];

        if($parent){
            $parans['categories[0][parent]'] = $parent;
        }

        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
        return $response->json();
    }


    public function createCourses($disciplinas, $categoryid)
    {
        $parans = [
            'wstoken'             => $this->token,
            'wsfunction'          => 'core_course_create_courses',
            'moodlewsrestformat'  => 'json',
        ];

        $disciplinas_turmas = [];
        $index = 0;
        foreach ($disciplinas as $disciplina) {
            $disciplinas_turmas[$index] = [
                'fullname'      => $disciplina->titulo,
                'shortname'     => $disciplina->codigo,
                'categoryid'    => $categoryid,
                'idnumber'      => $disciplina->codigo,
                'numsections'   => 4,
                'summary'       => '',
                'startdate'     => strtotime('now'),
                'enddate'       => strtotime('now + 1 year')
            ];
            $index++;
        }
        for($x = 0; $x < count($disciplinas_turmas); $x++){
            $parans['courses'][$x] = $disciplinas_turmas[$x];
        }
       
        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
        return $response;
    }


    public function getCourses($field = null, $code = null)
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_courses_by_field',
            'moodlewsrestformat' => 'json'
        ];

        if($field &&  $code){
            $parans['field'] = $field;
        
            $parans['value'] = $code;
        }
        $response = Http::withoutVerifying()->get('http://app-homo-moodle.seduc.se.gov.br/ava-escolar/webservice/rest/server.php', [
            'wstoken' => 'a72788eecd261e8afb1673ee7d298dc2',
            'wsfunction' => 'core_course_get_courses_by_field',
            'field'  => 'idnumber',
            'value'  => "$code",
            'moodlewsrestformat' => 'json'
        ]);

        $course = $response->json();
        if($course && $course['courses']){
            $course = $course['courses'];
        }else{
            $course = null;
        }

        return $course ;
    }

    public function getAllCourses()
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_courses',
            'moodlewsrestformat' => 'json'
        ];

        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);

        $course = $response->json();
       
        return $course ;
    }

    public function copyCourse($importFromId, $importToId)
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_import_course',
            'moodlewsrestformat' => 'json',
            'deletecontent'      => '0',
            'importfrom'        =>  $importFromId,
            'importto'          =>  $importToId
        ];
        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
    }


    public function createUser($user)
    {
        $parans = [
            'wstoken'                   => $this->token,
            'wsfunction'                => 'core_user_create_users',
            'users'                     => $user,
        ];
       
        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
        return  $response->json();
    }

    public function deleteCategory()
    {
        $parans = [
            'wstoken'                   => $this->token,
            'wsfunction'                => 'core_course_delete_categories',
            'categories[0][id]'         => 567,
            'categories[0][recursive]'  => 1
        ];
       
        $response = Http::withoutVerifying()->asForm()->post($this->URL,$parans);
        return  $response->json();
    }
}
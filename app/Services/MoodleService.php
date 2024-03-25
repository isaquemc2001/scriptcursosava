<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoodleService
{

    protected $URL;
    protected $token;

    public function __construct()
    {

        // $url_token = env('MOODLE_URL') . "login/token.php?username=" . env('MOODLE_USER') . "&password=" . env('MOODLE_PASSWORD') . "&service=" . env('MOODLE_SERVICE');
        // $response = Http::get($url_token);
        $this->token =  'f82c14692653f613aa6dc16199374df3';
        // print_r($this->token);
        // exit;
        $this->URL = env('MOODLE_URL') . "webservice/rest/server.php";
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

        $response = Http::timeout(10000)->asForm()->withOptions(['verify' => false])->post($this->URL,$parans);

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

        $response = Http::asForm()->post($this->URL,$parans);
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

        $response = Http::asForm()->post($this->URL,$parans);
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
        $response = Http::timeout(10000)->asForm()->withOptions(['verify' => false])->post($this->URL,$parans);

        $course = $response->json();

        // print_r($course);
        // exit;

        if($course['courses']){
            $course = $course['courses'];
        }else{
            $course = null;
        }

        return $course ;
    }

    public function getCoursesESS($field = null, $code = null)
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_courses',
            'moodlewsrestformat' => 'json'
        ];
        $response = Http::timeout(10000)->asForm()->post($this->URL,$parans);

        $course = $response->json();
        return $course ;
    }

    public function getAllCourses()
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_courses',
            'moodlewsrestformat' => 'json'
        ];

        $response = Http::asForm()->post($this->URL,$parans);

        $course = $response->json();

        return $course ;
    }

    public function copyCourse($importFromId, $importToId)
    {
        $parans = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_import_course',
            'moodlewsrestformat' => 'json',
            'deletecontent'      => '1',
            'importfrom'        =>  $importFromId,
            'importto'          =>  $importToId
        ];
        $response = Http::timeout(10000)->asForm()->withOptions(['verify' => false])->post($this->URL,$parans);

        // print_r($response);
        // exit;
    }


    public function createUser($user)
    {
        $parans = [
            'wstoken'                   => $this->token,
            'wsfunction'                => 'core_user_create_users',
            'users'                     => $user,
        ];

        $response = Http::asForm()->post($this->URL,$parans);
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

        $response = Http::asForm()->post($this->URL,$parans);
        return  $response->json();
    }

    public function getForunsByCorse($corseId)
    {
        $parans = [
            'wstoken'   => $this->token,
            'wsfunction' => 'mod_forum_get_forums_by_courses',
            'courseids[0]' => $corseId,
            'moodlewsrestformat' => 'json',
        ];

        $response = Http::asForm()->post($this->URL,$parans);
        return  $response->json();
    }

    public function getDiscussionsByForum($corseId, $forumId)
    {
        $parans = [
            'wstoken'   => $this->token,
            'wsfunction' => 'mod_forum_get_forum_discussions',
            'forumid' => $forumId,
            'moodlewsrestformat' => 'json',
        ];

        $response = Http::asForm()->post($this->URL,$parans);
        return  $response->json();
    }

    public function getPostsByDiscussion($discussionid)
    {
        $parans = [
            'wstoken'   => $this->token,
            'wsfunction' => 'mod_forum_get_forum_discussion_posts',
            'discussionid' => $discussionid,
            'moodlewsrestformat' => 'json',
        ];

        $response = Http::asForm()->post($this->URL,$parans);
        return json_decode($response->getBody(), true);
    }
}

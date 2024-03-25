<?php

use App\Http\Controllers\DiretoriasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('import',[DiretoriasController::class, 'import']);
Route::get('criarEstrutura',[DiretoriasController::class, 'criarEstrutura']);
Route::get('cadastrarDisciplinas', [DiretoriasController::class, 'cadastrarDisciplinas']);
Route::get('cadastrarProfessores', [DiretoriasController::class, 'cadastrarProfessores']);
Route::get('associarProfessorDisciplina', [DiretoriasController::class, 'AssociarProfessorDisciplina']);
Route::get('gerarCSVProfessores', [DiretoriasController::class, 'gerarCSVProfessores']);
Route::get('gerarCSVAlunos', [DiretoriasController::class, 'gerarCSVAlunos']);
Route::get('pegarNotas', [DiretoriasController::class, 'pegarNotas']);
Route::get('copiarCurso', [DiretoriasController::class, 'copiarCurso']);
Route::get('importarProfessores', [DiretoriasController::class, 'cadastrarProfessores2']);
Route::get('importarAlunos', [DiretoriasController::class, 'cadastrarAlunos']);
Route::get('gerarCSVProfessores2', [DiretoriasController::class, 'gerarCSVProfessores2']);
Route::get('apagarcursos', [DiretoriasController::class, 'apagarCategoria']);
Route::get('importarAdministrativo', [DiretoriasController::class, 'importarAdministrativo']);
Route::get('exportarAdministrativo', [DiretoriasController::class, 'exportarAdministrativo']);
Route::get('relatorioAnaCarla', [DiretoriasController::class, 'relatorioAnaCarla']);
Route::get('juntarCidades', [DiretoriasController::class, 'excluirCidades']);
Route::get('cadastrarUsuario', [DiretoriasController::class, 'cadastrarUsuario']);

Route::get('relatorioPaulo', [DiretoriasController::class, 'gerarRelatorioPaulo']);
Route::get('relatorioAlteracao', [DiretoriasController::class, 'gerarRelatorioAlteracoes']);
Route::get('relatorioForum', [DiretoriasController::class, 'consultarForuns']);
Route::get('importarDaHomologacao', [DiretoriasController::class, 'copiarCursoHomolog']);

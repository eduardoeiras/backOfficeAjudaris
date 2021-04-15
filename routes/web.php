<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ROUTES DE LOGIN
Route::post('login', 'UtilizadorController@realizarLogin')->name('login');
Route::get('admin/terminarSessao', 'UtilizadorController@realizarLogout')->middleware(['checkLogInAdmin']);
Route::get('colaborador/terminarSessao', 'UtilizadorController@realizarLogout')->middleware(['CheckLogInColaborador']);

Route::get('/', function () {
    return view("login");
})->name("paginaLogin");

Route::get('/{msg}', function ($msg) {
    return view("login")->with("msg", $msg);
})->name("paginaLoginErro");

/*  _______________________________________________________________________________________________________________________________________
   |                                                                                                                                       |
   |-------------------------------------------------- ROUTES PARA O USER ADMIN -----------------------------------------------------|
   |_______________________________________________________________________________________________________________________________________|
*/

/*Cada route tem de ter um middleware que verifica se o utilizador fez login antes de concretizar o pedido*/
Route::get('admin/dashboardAdmin','ProjetoController@index')->name("dashboardAdmin")->middleware(['checkLogInAdmin']);
Route::get('admin/projetos/getPorId/{id}', 'ProjetoController@getProjetoPorId')->middleware(['checkLogInAdmin']);
Route::post('admin/projetos/delete/{id}', 'ProjetoController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetos/edit/{id}', 'ProjetoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/projetos/add', 'ProjetoController@store')->middleware(['checkLogInAdmin']);
Route::get('admin/projetos/getPdf/{id}', 'FicheiroController@getPdf')->middleware(['checkLogInAdmin']);
Route::post('admin/projetos/submeterFicheiro', 'FicheiroController@receberFicheiro')->middleware(['checkLogInAdmin']);

Route::get('admin/gerirProjeto{id}', 'ProjetoController@gerirParticipantes')->name("gerirProjeto")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirProjeto/getParticipantes', 'ProjetoController@getParticipantes')->middleware(['checkLogInAdmin']);
Route::get('admin/gerirProjeto/pesquisaParticipantes/{tipo}-{ano}-{pesq}', 'ProjetoController@participantesPesq')->middleware(['checkLogInAdmin']);

Route::get('admin/utilizadores', 'UtilizadorController@index')->name("utilizadores")->middleware(['checkLogInAdmin']);
Route::get('admin/utilizadores/getPorId/{id}', 'UtilizadorController@getUserPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/utilizadores/getAll', 'UtilizadorController@getAll')->middleware(['checkLogInAdmin']);
Route::get('admin/utilizadores/existeUserNome/{nome}', 'UtilizadorController@existeUser')->middleware(['checkLogInAdmin']);
Route::post('admin/utilizadores/deleteUtilizador/{id}', 'UtilizadorController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/utilizadores/editUtilizador/{id}', 'UtilizadorController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/utilizadores/addUtilizador', 'UtilizadorController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/professores','ProfessorController@index')->name("professores")->middleware(['checkLogInAdmin']);
Route::get('admin/professores/getPorId/{id}', 'ProfessorController@getProfPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/professores/getDisponiveis', 'ProfessorController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/professores/delete/{id}', 'ProfessorController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/professores/edit/{id}', 'ProfessorController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/professores/add', 'ProfessorController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/escolas','EscolaSolidariaController@index')->name("escolas")->middleware(['checkLogInAdmin']);
Route::get('admin/escolas/getPorId/{id}', 'EscolaSolidariaController@getEscolaPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/escolas/getDisponiveis', 'EscolaSolidariaController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/escolas/delete/{id}', 'EscolaSolidariaController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/escolas/edit/{id}', 'EscolaSolidariaController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/escolas/add', 'EscolaSolidariaController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/agrupamentos','AgrupamentoController@index')->name("agrupamentos")->middleware(['checkLogInAdmin']);
Route::get('admin/agrupamentos/getAllComLocalidade', 'AgrupamentoController@getAllComLocalidade')->middleware(['checkLogInAdmin']);
Route::get('admin/agrupamentos/getPorId/{id}', 'AgrupamentoController@getAgrupamentoPorId')->middleware(['checkLogInAdmin']);
Route::post('admin/agrupamentos/delete/{id}', 'AgrupamentoController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/agrupamentos/edit/{id}', 'AgrupamentoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/agrupamentos/add', 'AgrupamentoController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/ilustradores','IlustradorSolidarioController@index')->name("ilustradores")->middleware(['checkLogInAdmin']);
Route::get('admin/ilustradores/getPorId/{id}', 'IlustradorSolidarioController@getIlustradorPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/ilustradores/getDisponiveis', 'IlustradorSolidarioController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/ilustradores/delete/{id}', 'IlustradorSolidarioController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/ilustradores/edit/{id}', 'IlustradorSolidarioController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/ilustradores/add', 'IlustradorSolidarioController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/contadores','ContadorHistoriaController@index')->name("contadores")->middleware(['checkLogInAdmin']);
Route::get('admin/contadores/getPorId/{id}', 'ContadorHistoriaController@getContadorPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/contadores/getDisponiveis', 'ContadorHistoriaController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/contadores/delete/{id}', 'ContadorHistoriaController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/contadores/edit/{id}', 'ContadorHistoriaController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/contadores/add', 'ContadorHistoriaController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/entidades','EntidadeOficialController@index')->name("entidades")->middleware(['checkLogInAdmin']);
Route::get('admin/entidades/getPorId/{id}', 'EntidadeOficialController@getEntidadePorId')->middleware(['checkLogInAdmin']);
Route::get('admin/entidades/getDisponiveis', 'EntidadeOficialController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/entidades/delete/{id}', 'EntidadeOficialController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/entidades/edit/{id}', 'EntidadeOficialController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/entidades/add', 'EntidadeOficialController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/juris','JuriController@index')->name("juris")->middleware(['checkLogInAdmin']);
Route::get('admin/juris/getPorId/{id}', 'JuriController@getJuriPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/juris/getDisponiveis', 'JuriController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/juris/delete/{id}', 'JuriController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/juris/edit/{id}', 'JuriController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/juris/add', 'JuriController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/rbes','RBEController@index')->name("rbes")->middleware(['checkLogInAdmin']);
Route::get('admin/rbes/getPorId/{id}', 'RBEController@getRbePorId')->middleware(['checkLogInAdmin']);
Route::get('admin/rbes/getDisponiveis', 'RBEController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/rbes/delete/{id}', 'RBEController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/rbes/edit/{id}', 'RBEController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/rbes/add', 'RBEController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/universidades','UniversidadeController@index')->name("universidades")->middleware(['checkLogInAdmin']);
Route::get('admin/universidades/getPorId/{id}', 'UniversidadeController@getUniversidadePorId')->middleware(['checkLogInAdmin']);
Route::get('admin/universidades/getDisponiveis', 'UniversidadeController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/universidades/delete/{id}', 'UniversidadeController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/universidades/edit/{id}', 'UniversidadeController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/universidades/add', 'UniversidadeController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/profsFaculdade','ProfessorFaculdadeController@index')->name("profsFaculdade")->middleware(['checkLogInAdmin']);
Route::get('admin/profsFaculdade/getPorId/{id}', 'ProfessorFaculdadeController@getProfPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/profsFaculdade/getDisponiveis', 'ProfessorFaculdadeController@getDisponiveis')->middleware(['checkLogInAdmin']);
Route::post('admin/profsFaculdade/delete/{id}', 'ProfessorFaculdadeController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/profsFaculdade/edit/{id}', 'ProfessorFaculdadeController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/profsFaculdade/add', 'ProfessorFaculdadeController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/concelhos','ConcelhoController@index')->name("concelhos")->middleware(['checkLogInAdmin']);
Route::get('admin/concelhos/verificaRbe/{id}','ConcelhoController@verificaRbes')->middleware(['checkLogInAdmin']);
Route::get('admin/concelhos/getAll','ConcelhoController@getAll')->middleware(['checkLogInAdmin']);
Route::get('admin/concelhos/getPorId/{id}', 'ConcelhoController@getConcelhoPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/concelhos/existeConcelho/{concelho}', 'ConcelhoController@existeConcelho')->middleware(['checkLogInAdmin']);
Route::post('admin/concelhos/delete/{id}', 'ConcelhoController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/concelhos/edit/{id}', 'ConcelhoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/concelhos/add', 'ConcelhoController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/trocasAgrupamento', 'TrocaAgrupamentoController@index')->name("trocasAgrupamento")->middleware(['checkLogInAdmin']);
Route::get('admin/trocasAgrupamento/getPorId/{id}', 'TrocaAgrupamentoController@getTrocaPorId')->middleware(['checkLogInAdmin']);
Route::post('admin/trocasAgrupamento/delete/{id}', 'TrocaAgrupamentoController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/trocasAgrupamento/edit/{id}', 'TrocaAgrupamentoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/trocasAgrupamento/add', 'TrocaAgrupamentoController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/codPostal/getAll', 'CodPostalController@getAll')->middleware(['checkLogInAdmin']);
Route::get('admin/codPostal/add', 'CodPostalController@store')->middleware(['checkLogInAdmin']);
Route::get('admin/codPostal/getLocalidade/{codPostal}', 'CodPostalController@getLocalidade')->middleware(['checkLogInAdmin']);

Route::get('admin/formacoes', 'FormacaoController@index')->name("formacoes")->middleware(['checkLogInAdmin']);
Route::get('admin/formacoes/getPorId/{id}', 'FormacaoController@getFormacaoPorId')->middleware(['checkLogInAdmin']);
Route::post('admin/formacoes/add', 'FormacaoController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/formacoes/edit/{id}', 'FormacaoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/formacoes/delete/{id}', 'FormacaoController@destroy')->middleware(['checkLogInAdmin']);

//ROUTES DE VERIFICAÇÃO DA EXISTÊNCIA DE ASSOCIAÇÕES AOS PROJETOS

Route::get('admin/projetoEscola/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoEscolaController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoIlustrador/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoIlustradorController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoContador/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoContadorController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoEntidade/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoEntidadeController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoJuri/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoJuriController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoRbe/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoRBEController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoUniversidade/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoUniversidadeController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoProfFac/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoProfessorFaculController@verificaAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/projetoProfessor/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoProfessorController@verificaAssociacao')->middleware(['checkLogInAdmin']);

//ROUTES PARA A ADIÇÃO DE ASSOCIAÇÕES AOS PROJETOS

Route::post('admin/projetoEscola/add', 'ProjetoEscolaController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoIlustrador/add', 'ProjetoIlustradorController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoContador/add', 'ProjetoContadorController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoEntidade/add', 'ProjetoEntidadeController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoJuri/add', 'ProjetoJuriController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoRbe/add', 'ProjetoRBEController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoUniversidade/add', 'ProjetoUniversidadeController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoProfFac/add', 'ProjetoProfessorFaculController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoProfessor/add', 'ProjetoProfessorController@store')->middleware(['checkLogInAdmin']);

Route::get('admin/cargosProfessor/getAll', 'CargoProfController@getAll')->middleware(['checkLogInAdmin']);
Route::get('admin/cargosProfessor/getPorIdProfessor/{id}-{id_projeto}-{ano}', 'CargoProfController@getPorIdProf')->middleware(['checkLogInAdmin']);

//ROUTES PARA A REMOÇÃO DE ASSOCIAÇÕES AOS PROJETOS

Route::post('admin/projetoEscola/delete/{id}-{id_projeto}-{ano}', 'ProjetoEscolaController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoIlustrador/delete/{id}-{id_projeto}-{ano}', 'ProjetoIlustradorController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoContador/delete/{id}-{id_projeto}-{ano}', 'ProjetoContadorController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoEntidade/delete/{id}-{id_projeto}-{ano}', 'ProjetoEntidadeController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoJuri/delete/{id}-{id_projeto}-{ano}', 'ProjetoJuriController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoRbe/delete/{id}-{id_projeto}-{ano}', 'ProjetoRBEController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoUniversidade/delete/{id}-{id_projeto}-{ano}', 'ProjetoUniversidadeController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoProfFac/delete/{id}-{id_projeto}-{ano}', 'ProjetoProfessorFaculController@destroy')->middleware(['checkLogInAdmin']);
Route::post('admin/projetoProfessor/delete/{id}-{id_projeto}-{ano}', 'ProjetoProfessorController@destroy')->middleware(['checkLogInAdmin']);

//ROUTES PARA A GESTÃO DOS PROFESSORES DAS ESCOLAS

Route::get('admin/gerirEscola{id}', 'EscolaSolidariaController@gerirEscola')->name("gerirEscola")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirEscola/getProfessores', 'EscolaSolidariaController@getProfessores')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirEscola/delete/{id}-{id_escola}', 'EscolaSolidariaController@deleteAssociacao')->middleware(['checkLogInAdmin']);
Route::get('admin/professores/getDisponiveisSemEscola/{id}','ProfessorController@getDisponiveisSemEscola')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirEscola/add','EscolaSolidariaController@associarProfessor')->middleware(['checkLogInAdmin']);

//ROUTES PARA A GESTÃO DOS LIVROS POR ANO DAS ESCOLAS
Route::get('admin/gerirLivrosAno-{id}-{nome}', 'LivrosAnoController@index')->name("gerirLivrosAno")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirLivrosAno/getPorId/{ano}-{id}', 'LivrosAnoController@getPorId')->middleware(['checkLogInAdmin']);
Route::get('admin/gerirLivrosAno/existeAssociacao/{ano}-{id}', 'LivrosAnoController@existeAssociacao')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirLivrosAno/add','LivrosAnoController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirLivrosAno/edit/{ano}-{id}','LivrosAnoController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirLivrosAno/delete/{ano}-{id}','LivrosAnoController@destroy')->middleware(['checkLogInAdmin']);

//ROUTES PARA A GESTÃO DOS PROFESSORES DAS UNIVERSIDADES

Route::get('admin/gerirUniversidade{id}', 'UniversidadeController@gerirProfessoresUniversidade')->name("gerirUniversidade")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirUniversidade/getProfessores', 'UniversidadeProfFaculdadeController@getProfessores')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirUniversidade/delete/{id}-{id_universidade}', 'UniversidadeProfFaculdadeController@destroy')->middleware(['checkLogInAdmin']);
Route::get('admin/profsFaculdade/getDisponiveisSemEscola/{id}','ProfessorFaculdadeController@getDisponiveisSemEscola')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirUniversidade/add','UniversidadeProfFaculdadeController@store')->middleware(['checkLogInAdmin']);

//ROUTES PARA A GESTÃO DOS PROJETOS ASSOCIADOS AOS UTILIZADORES DO TIPO: COLABORADOR

Route::get('admin/gerirProjetosUser/{id}', 'UtilizadorController@gerirProjetosUser')->name("projetosUtilizador")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirProjetosUser/getProjetosAssociados/{id}', 'ProjetoUtilizadorController@getProjetosAssociados')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirProjetosUser/projetosAssociados/destroy/{id}-{id_projeto}', 'ProjetoUtilizadorController@destroy')->middleware(['checkLogInAdmin']);
Route::get('admin/gerirProjetosUser/projetos/getSemAssociacao/{id}','ProjetoController@getSemAssociacao')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirProjetosUser/gerirProjetosUtilizador/add','ProjetoUtilizadorController@store')->middleware(['checkLogInAdmin']);

//ROUTES PARA A GESTÃO DAS COMUNICAÇÕES
Route::get('admin/gerirComunicacoes-{id}-{nome}', 'ComunicacoesController@index')->name("gerirComunicacoes")->middleware(['checkLogInAdmin']);
Route::get('admin/gerirComunicacoes/getPorId/{id}', 'ComunicacoesController@getPorId')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirComunicacoes/add','ComunicacoesController@store')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirComunicacoes/edit/{id}','ComunicacoesController@update')->middleware(['checkLogInAdmin']);
Route::post('admin/gerirComunicacoes/delete/{id}','ComunicacoesController@destroy')->middleware(['checkLogInAdmin']);

//ROUTES PARA A PESQUISA GERAL
//Route::get('admin/pesqGlobal/{nome}', 'ColaboradorController@pesqGeralNome')->middleware(['checkLogInAdmin']);
Route::get('admin/pesqGlobal/getColaboradores', 'ColaboradorController@getColaboradores')->middleware(['checkLogInAdmin']);
Route::get('admin/pesquisaGeral', function () {
    return view("admin/pesquisaGeral");
})->middleware(['checkLogInAdmin']);

/*  _______________________________________________________________________________________________________________________________________
   |                                                                                                                                       |
   |-------------------------------------------------- ROUTES PARA O USER COLABORADOR -----------------------------------------------------|
   |_______________________________________________________________________________________________________________________________________|
*/

Route::get('colaborador/dashboardColaborador','ProjetoController@index')->name("dashboardColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetos/getPorId/{id}', 'ProjetoController@getProjetoPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetos/edit/{id}', 'ProjetoController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetos/add', 'ProjetoController@store')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetos/getPdf/{id}', 'FicheiroController@getPdf')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/gerirProjeto{id}', 'ProjetoController@gerirParticipantes')->name("gerirProjetoColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirProjeto/getParticipantes', 'ProjetoController@getParticipantes')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirProjeto/pesquisaParticipantes/{tipo}-{ano}-{pesq}', 'ProjetoController@participantesPesq')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/professores','ProfessorController@index')->name("professoresColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/professores/getPorId/{id}', 'ProfessorController@getProfPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/professores/getDisponiveis', 'ProfessorController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/professores/edit/{id}', 'ProfessorController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/professores/add', 'ProfessorController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/escolas','EscolaSolidariaController@index')->name("escolasColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/escolas/getPorId/{id}', 'EscolaSolidariaController@getEscolaPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/escolas/getDisponiveis', 'EscolaSolidariaController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/escolas/edit/{id}', 'EscolaSolidariaController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/escolas/add', 'EscolaSolidariaController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/agrupamentos','AgrupamentoController@index')->name("agrupamentosColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/agrupamentos/getAllComLocalidade', 'AgrupamentoController@getAllComLocalidade')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/agrupamentos/getPorId/{id}', 'AgrupamentoController@getAgrupamentoPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/agrupamentos/edit/{id}', 'AgrupamentoController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/agrupamentos/add', 'AgrupamentoController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/ilustradores','IlustradorSolidarioController@index')->name("ilustradoresColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/ilustradores/getPorId/{id}', 'IlustradorSolidarioController@getIlustradorPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/ilustradores/getDisponiveis', 'IlustradorSolidarioController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/ilustradores/edit/{id}', 'IlustradorSolidarioController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/ilustradores/add', 'IlustradorSolidarioController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/contadores','ContadorHistoriaController@index')->name("contadoresColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/contadores/getPorId/{id}', 'ContadorHistoriaController@getContadorPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/contadores/getDisponiveis', 'ContadorHistoriaController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/contadores/edit/{id}', 'ContadorHistoriaController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/contadores/add', 'ContadorHistoriaController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/entidades','EntidadeOficialController@index')->name("entidadesColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/entidades/getPorId/{id}', 'EntidadeOficialController@getEntidadePorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/entidades/getDisponiveis', 'EntidadeOficialController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/entidades/edit/{id}', 'EntidadeOficialController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/entidades/add', 'EntidadeOficialController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/juris','JuriController@index')->name("jurisColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/juris/getPorId/{id}', 'JuriController@getJuriPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/juris/getDisponiveis', 'JuriController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/juris/edit/{id}', 'JuriController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/juris/add', 'JuriController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/rbes','RBEController@index')->name("rbesColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/rbes/getPorId/{id}', 'RBEController@getRbePorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/rbes/getDisponiveis', 'RBEController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/rbes/edit/{id}', 'RBEController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/rbes/add', 'RBEController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/universidades','UniversidadeController@index')->name("universidadesColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/universidades/getPorId/{id}', 'UniversidadeController@getUniversidadePorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/universidades/getDisponiveis', 'UniversidadeController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/universidades/edit/{id}', 'UniversidadeController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/universidades/add', 'UniversidadeController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/profsFaculdade','ProfessorFaculdadeController@index')->name("profsFaculdadeColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/profsFaculdade/getPorId/{id}', 'ProfessorFaculdadeController@getProfPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/profsFaculdade/getDisponiveis', 'ProfessorFaculdadeController@getDisponiveis')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/profsFaculdade/edit/{id}', 'ProfessorFaculdadeController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/profsFaculdade/add', 'ProfessorFaculdadeController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/concelhos','ConcelhoController@index')->name("concelhosColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/concelhos/verificaRbe/{id}','ConcelhoController@verificaRbes')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/concelhos/getAll','ConcelhoController@getAll')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/concelhos/getPorId/{id}', 'ConcelhoController@getConcelhoPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/concelhos/edit/{id}', 'ConcelhoController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/concelhos/add', 'ConcelhoController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/trocasAgrupamento', 'TrocaAgrupamentoController@index')->name("trocasAgrupamentoColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/trocasAgrupamento/getPorId/{id}', 'TrocaAgrupamentoController@getTrocaPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/trocasAgrupamento/edit/{id}', 'TrocaAgrupamentoController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/trocasAgrupamento/add', 'TrocaAgrupamentoController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/codPostal/getAll', 'CodPostalController@getAll')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/codPostal/add', 'CodPostalController@store')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/codPostal/getLocalidade/{codPostal}', 'CodPostalController@getLocalidade')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/formacoes', 'FormacaoController@index')->name("formacoesColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/formacoes/getPorId/{id}', 'FormacaoController@getFormacaoPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/formacoes/add', 'FormacaoController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/formacoes/edit/{id}', 'FormacaoController@update')->middleware(['CheckLogInColaborador']);

//ROUTES DE VERIFICAÇÃO DA EXISTÊNCIA DE ASSOCIAÇÕES AOS PROJETOS

Route::get('colaborador/projetoEscola/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoEscolaController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoIlustrador/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoIlustradorController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoContador/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoContadorController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoEntidade/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoEntidadeController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoJuri/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoJuriController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoRbe/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoRBEController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoUniversidade/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoUniversidadeController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoProfFac/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoProfessorFaculController@verificaAssociacao')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/projetoProfessor/jaAssociado/{id}-{id_projeto}-{ano}', 'ProjetoProfessorController@verificaAssociacao')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A ADIÇÃO DE ASSOCIAÇÕES AOS PROJETOS

Route::post('colaborador/projetoEscola/add', 'ProjetoEscolaController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoIlustrador/add', 'ProjetoIlustradorController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoContador/add', 'ProjetoContadorController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoEntidade/add', 'ProjetoEntidadeController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoJuri/add', 'ProjetoJuriController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoRbe/add', 'ProjetoRBEController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoUniversidade/add', 'ProjetoUniversidadeController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoProfFac/add', 'ProjetoProfessorFaculController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoProfessor/add', 'ProjetoProfessorController@store')->middleware(['CheckLogInColaborador']);

Route::get('colaborador/cargosProfessor/getAll', 'CargoProfController@getAll')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/cargosProfessor/getPorIdProfessor/{id}-{id_projeto}-{ano}', 'CargoProfController@getPorIdProf')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A REMOÇÃO DE ASSOCIAÇÕES AOS PROJETOS

Route::post('colaborador/projetoEscola/delete/{id}-{id_projeto}-{ano}', 'ProjetoEscolaController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoIlustrador/delete/{id}-{id_projeto}-{ano}', 'ProjetoIlustradorController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoContador/delete/{id}-{id_projeto}-{ano}', 'ProjetoContadorController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoEntidade/delete/{id}-{id_projeto}-{ano}', 'ProjetoEntidadeController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoJuri/delete/{id}-{id_projeto}-{ano}', 'ProjetoJuriController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoRbe/delete/{id}-{id_projeto}-{ano}', 'ProjetoRBEController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoUniversidade/delete/{id}-{id_projeto}-{ano}', 'ProjetoUniversidadeController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoProfFac/delete/{id}-{id_projeto}-{ano}', 'ProjetoProfessorFaculController@destroy')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/projetoProfessor/delete/{id}-{id_projeto}-{ano}', 'ProjetoProfessorController@destroy')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A GESTÃO DOS PROFESSORES DAS ESCOLAS

Route::get('colaborador/gerirEscola{id}', 'EscolaSolidariaController@gerirEscola')->name("gerirEscolaColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirEscola/getProfessores', 'EscolaSolidariaController@getProfessores')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/professores/getDisponiveisSemEscola/{id}','ProfessorController@getDisponiveisSemEscola')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirEscola/add','EscolaSolidariaController@associarProfessor')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirEscola/delete/{id}-{id_escola}', 'EscolaSolidariaController@deleteAssociacao')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A GESTÃO DOS LIVROS POR ANO DAS ESCOLAS
Route::get('colaborador/gerirLivrosAno-{id}-{nome}', 'LivrosAnoController@index')->name("gerirLivrosAnoColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirLivrosAno/getPorId/{ano}-{id}', 'LivrosAnoController@getPorId')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirLivrosAno/existeAssociacao/{ano}-{id}', 'LivrosAnoController@existeAssociacao')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirLivrosAno/add','LivrosAnoController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirLivrosAno/edit/{ano}-{id}','LivrosAnoController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirLivrosAno/delete/{ano}-{id}','LivrosAnoController@destroy')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A GESTÃO DOS PROFESSORES DAS UNIVERSIDADES

Route::get('colaborador/gerirUniversidade{id}', 'UniversidadeController@gerirProfessoresUniversidade')->name("gerirUniversidadeColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirUniversidade/getProfessores', 'UniversidadeProfFaculdadeController@getProfessores')->middleware(['CheckLogInColaborador']);
Route::get('colaborador/profsFaculdade/getDisponiveisSemEscola/{id}','ProfessorFaculdadeController@getDisponiveisSemEscola')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirUniversidade/add','UniversidadeProfFaculdadeController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirUniversidade/delete/{id}-{id_universidade}', 'UniversidadeProfFaculdadeController@destroy')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A GESTÃO DAS COMUNICAÇÕES
Route::get('colaborador/gerirComunicacoes-{id}-{nome}', 'ComunicacoesController@index')->name("gerirComunicacoesColaborador")->middleware(['CheckLogInColaborador']);
Route::get('colaborador/gerirComunicacoes/getPorId/{id}', 'ComunicacoesController@getPorId')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirComunicacoes/add','ComunicacoesController@store')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirComunicacoes/edit/{id}','ComunicacoesController@update')->middleware(['CheckLogInColaborador']);
Route::post('colaborador/gerirComunicacoes/delete/{id}','ComunicacoesController@destroy')->middleware(['CheckLogInColaborador']);

//ROUTES PARA A GESTÃO DOS EMAILS PARA O COLABORADOR E ADMINISTRADOR

Route::get('admin/getEmails/{id}', 'ColaboradorController@getEmails')->middleware(['checkLogInAdmin']);
Route::get('colaborador/getEmails/{id}', 'ColaboradorController@getEmails')->middleware(['CheckLogInColaborador']);
Route::get('admin/existeEmail/{email}', 'ColaboradorController@existeEmailSemColaborador')->middleware(['checkLogInAdmin']);
Route::get('colaborador/existeEmail/{email}', 'ColaboradorController@existeEmailSemColaborador')->middleware(['CheckLogInColaborador']);



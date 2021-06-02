<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Colaborador;
use App\Models\Universidade;
use App\Models\ProfessorFaculdade;
use App\Models\UniversidadeProfFaculdade;
use App\Http\Controllers\ColaboradorController;
use DB;

class UniversidadesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);
        
        //CRIAÇÃO DOS ARRAYS PARA O JURI
        $universidadesInseridas = array();
        $professoresFaculInseridos = array();

        //CRIAÇÃO DO PROJETO AO QUAL OS PARTICIPANTES SERÃO ASSOCIADOS
        $idProjeto = -1;
        $projeto = DB::table('projeto')
                    ->where('projeto.nome', '=', "Histórias da Ajudaris")
                    ->orderBy('projeto.id_projeto')->first();

        if($projeto != null) {
            $idProjeto = $projeto->id_projeto;
        }

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UMA UNIVERSIDADE*/
            $nome = $row[1];
            if($nome != null){
                $curso = $row[2];
                $observacoes = $row[11];
                $distrito = $row[0];
                $telefone = $row[7];
                $emails = array();
                $emailObs = null;
                if($row[6] != null) {
                    if(sizeof(explode(";", $row[6])) > 1) {
                        $emailArray = explode(";", $row[6]);
                        foreach($emailArray as $emailStr) {
                            $email = DB::table('email')
                            ->where('email.email', '=', "$emailStr")
                            ->first();
                            if($email != null) {
                                $emailObs = $emailObs.$email->email."; ";
                            }
                            else {
                                if(strlen($emailStr) < 70) {
                                    array_push($emails, $emailStr);
                                }
                            }
                        }
                    }
                    else {
                        if(sizeof(explode(" ", $row[6])) <= 1) {
                            if(strlen($row[6]) <= 70) {
                                $emailStr = $row[6];
                                $email = DB::table('email')
                                    ->where('email.email', '=', "$emailStr")
                                    ->first();
                                if($email != null) {
                                    $emailObs = "Email: ".$email->email;
                                }
                                else {
                                    if(strlen($emailStr) < 70) {
                                        array_push($emails, $emailStr);
                                    }
                                }  
                            } 
                        }       
                    }
                }
                if($row[4] != null) {
                    if(sizeof(explode(";", $row[4])) > 1) {
                        $emailArray = explode(";", $row[4]);
                        foreach($emailArray as $emailStr) {
                            $email = DB::table('email')
                            ->where('email.email', '=', "$emailStr")
                            ->first();
                            if($email != null) {
                                $emailObs = $emailObs.$email->email."; ";
                            }
                            else {
                                if(strlen($emailStr) < 70) {
                                    array_push($emails, $emailStr);
                                }
                            }
                        }
                    }
                    else {
                        if(sizeof(explode(" ", $row[4])) <= 1) {
                            if(strlen($row[4]) <= 70) {
                                $emailStr = $row[4];
                                $email = DB::table('email')
                                    ->where('email.email', '=', "$emailStr")
                                    ->first();
                                if($email != null) {
                                    $emailObs = "Email: ".$email->email;
                                }
                                else {
                                    if(strlen($emailStr) < 70) {
                                        array_push($emails, $emailStr);
                                    }
                                }  
                            } 
                        }       
                    }
                }
                $tipo = null;
                if($row[8] != null){
                    if(strtolower($row[8]) == "sim"){
                        $tipo = "Universidade de Artes";
                    }
                }
                if($row[9] != null){
                    if(strtolower($row[9]) == "sim"){
                        $tipo = "Universidade de Educação";
                    }
                }
                if($row[10] != null){
                    if(strtolower($row[10]) == "sim"){
                        $tipo = "Universidade Ciencias da Educação";
                    }
                }
                if($row[11] != null){
                    if($row[11] == "sim"){
                        $tipo = "Universidade Fotografia/Filmagem";
                    }
                } 
    
                $disponibilidade = 1;
                
    
                //VERIFICAÇÃO SE A UNIVERSIDADE JÁ FOI INSERIDO
                $idUniversidades = -1;
                $existe = false;
                foreach($universidadesInseridas as $universidades) {
                    if($universidades["nome"] == $nome) {
                        $existe = true;
                        $idUniversidades = $universidades["id"];
                        break;
                    }
                }
    
                /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO JURI COLOCANDO-O NO ARRAY DE
                  DE JURIS JÁ INSERIDOS  */
                $idColabUniversidade = -1;
                if(!$existe) {
                    $idColabUniversidade = ColaboradorController::create($nome, $observacoes, null, $telefone, null, $disponibilidade, 
                    null, null, null, null, $distrito, $emails);
    
                    $universidade = new Universidade();
                    $universidade->id_colaborador = $idColabUniversidade;
                    $universidade->curso = $curso;
                    $universidade->tipo = $tipo;
                    $universidade->save();
    
                    $idUniversidades = $universidade->getKey();
                    $universidadeInserida = array("id" => $idUniversidades,"nome" => $nome);
                    array_push($universidadesInseridas, $universidadeInserida);
                }
                else {
                    $universidade = Universidade::find($idUniversidades);
                    $idColabUniversidade = $universidade->id_colaborador;
                }
    
                /*OBTER INFORMAÇOES PROFESSOR*/
                $nomeProfFacul = $row[5];
                
                if($nomeProfFacul != null){
                    $idProfessorFacul = 0;
                    $existeProfessor = false;
                    foreach($professoresFaculInseridos as $professorFacul) {
                        if($professorFacul["nome"] == $nomeProfFacul) {
                            $existeProfessor = true;
                            $idProfessorFacul = $professorFacul["id"];
                            break;
                        }
                    }
        
                    $idColabProfFacul = -1;
                    if(!$existeProfessor) {
                        $idColabProfFacul = ColaboradorController::create($nomeProfFacul, null, null, null, null, $disponibilidade,
                        null, null, null, null, null, null);
            
                        $professorFacul = new ProfessorFaculdade();
                        $professorFacul->id_colaborador = $idColabProfFacul;
                        $professorFacul->cargo = "Professor Responsável";
                        $professorFacul->save();
            
                        $idProfessorFacul = $professorFacul->getKey();
                        $professorFaculInserido = array("id" => $idProfessorFacul,"nome" => $nomeProfFacul);
                        array_push($professoresFaculInseridos, $professorFaculInserido);
                    }
                    else {
                        $professorFacul = ProfessorFaculdade::find($idProfessorFacul);
                        $idColabProfFacul = $professorFacul->id_colaborador;
                    }
    
                    /*VERIFICA ASSOCIAÇAO PROFESSOR FACULDADE*/
                    $existeAssociacao = DB::table('universidade_prof_faculdade')
                    ->where([
                        ['universidade_prof_faculdade.id_professorFaculdade', '=', $idProfessorFacul],
                        ['universidade_prof_faculdade.id_universidade', '=', $idUniversidades]
                        ])
                    ->first();
    
                    if($existeAssociacao == null){
                        $profFac = new UniversidadeProfFaculdade();
    
                        $profFac->id_universidade = intval($idUniversidades);
                        $profFac->id_professorFaculdade = intval($idProfessorFacul);
                        $profFac->save();
                    }
                }
    
                /*DIRETOR*/
                $nomeDiretor = $row[3]; 
                if($nomeDiretor != null){
                    $idProfessorFacul = 0;
                    $existeProfessor = false;
                    foreach($professoresFaculInseridos as $professorFacul) {
                        if($professorFacul["nome"] == $nomeDiretor) {
                            $existeProfessor = true;
                            $idProfessorFacul = $professorFacul["id"];
                            break;
                        }
                    }
        
                    $idColabProfFacul = -1;
                    if(!$existeProfessor) {
                        $idColabProfFacul = ColaboradorController::create($nomeDiretor, null, null, null, null, $disponibilidade,
                        null, null, null, null, null, null);
            
                        $professorFacul = new ProfessorFaculdade();
                        $professorFacul->id_colaborador = $idColabProfFacul;
                        $professorFacul->cargo = "Diretor";
                        $professorFacul->save();
            
                        $idProfessorFacul = $professorFacul->getKey();
                        $professorFaculInserido = array("id" => $idProfessorFacul,"nome" => $nomeDiretor);
                        array_push($professoresFaculInseridos, $professorFaculInserido);
                    }
                    else {
                        $professorFacul = ProfessorFaculdade::find($idProfessorFacul);
                        $idColabProfFacul = $professorFacul->id_colaborador;
                    }
    
                    /*VERIFICA ASSOCIAÇAO PROFESSOR FACULDADE*/
                    $existeAssociacao = DB::table('universidade_prof_faculdade')
                    ->where([
                        ['universidade_prof_faculdade.id_professorFaculdade', '=', $idProfessorFacul],
                        ['universidade_prof_faculdade.id_universidade', '=', $idUniversidades]
                        ])
                    ->first();
    
                    if($existeAssociacao == null){
                        $profFac = new UniversidadeProfFaculdade();
                        $profFac->id_universidade = intval($idUniversidades);
                        $profFac->id_professorFaculdade = intval($idProfessorFacul);
                        $profFac->save();
                    }
                }
            } 
        }
    }
}
<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Agrupamento;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use App\Models\Professor;
use App\Models\EscolaSolidariaProf;
use App\Models\Comunicacao;
use App\Models\CargoProf;
use App\Models\ProjetoProfessor;
use App\Models\ProjetoEscola;
use App\Models\Projeto;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ProjetoProfessorController;
use App\Http\Controllers\ProjetoEscolaController;
use DateTime;
use DB;

class EstabelecimentosEnsinoSolidarioImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //UNSET DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA OS AGRUPAMENTOS, ESCOLAS, PROFESSORES E CARGOS INSERIDOS
        $agrupInseridos = array();
        $escolasInseridas = array();
        $professoresInseridos = array();
        $cargosInseridos = array();

        //CRIAÇÃO DO PROJETO AO QUAL OS PARTICIPANTES SERÃO ASSOCIADOS
        $projeto = new Projeto();
        $projeto->nome = "Histórias da Ajudaris";
        $projeto->objetivos = null;
        $projeto->regulamento = null;    
        $projeto->publicoAlvo = null;
        $projeto->observacoes = null;
        $projeto->save();
        $idProjeto = $projeto->getKey();

        foreach($rows as $row) {
            $idAgrupamento = -1;
            $idColabAgrupamento = -1;
            $idEscola = -1;
            $idColabEscola = -1;
            $idCargo = -1;
            $idProfessor = -1;

            $disponibilidade = 0;

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM AGRUPAMENTO */
            $nomeAgrup = $row[0];
            if($nomeAgrup != null) {
                if(sizeof(explode(",", $row[0])) > 1) {
                    $nomeArray = explode(",", $row[0]);
                    $nomeAgrup = $nomeArray[0];
                }
                if(strlen($nomeAgrup) > 150) {
                    $nomeAgrup = substr($nomeAgrup,0,150);
                }
                $rua = $row[1];
                if($rua != null) {
                    if(sizeof(explode(",", $rua)) > 1) {
                        $ruaArray = explode(",", $rua);
                        $rua = $ruaArray[0];
                    }    
                }
                $codPostal = null;
                $codPostalRua = null;
                if($row[2] != null) {
                    $codArray = explode("-", $row[2], 2);
                    if(count($codArray) == 2) {
                        if(is_numeric($codArray[0]) && is_numeric($codArray[1])) {
                        $codPostal = $codArray[0]; 
                        $codPostalRua = $codArray[1];  
                        }
                    }   
                }
                $localidade = $row[3];
                $distrito = $row[4];
                $telefone = $row[5];
                if($telefone != null) {
                    $telefone = trim($telefone);
                    if(!is_numeric($telefone)) {
                        $telefone = null;
                    }
                }
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
                $diretor = null;
                if(strlen($row[7]) <= 70) {
                    $diretor = $row[7];
                }
                if(strtolower($row[21]) == "sim") {
                    $disponibilidade = 0;
                }
                else {
                    $disponibilidade = 1;
                }

                //VERIFICAÇÃO SE O AGRUPAMENTO JÁ FOI INSERIDO
                $existe = false;
                foreach($agrupInseridos as $agrup) {
                    if($agrup["nome"] == $nomeAgrup) {
                        $existe = true;
                        $idAgrupamento = $agrup["id"];
                        break;
                    }
                }

                /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO AGRUPAMENTO COLOCANDO-O NO ARRAY DE
                DE AGRUPAMENTOS JÁ INSERIDOS  */
                if(!$existe) {
                    if($nomeAgrup != null && $nomeAgrup != "") {
                        // VERIFICAR SE O EMAIL JÁ EXISTE, VISTO EXISTIREM EMAILS REPETIDOS PARA DIFERENTES COLABORADORES
                        $idColabAgrupamento = ColaboradorController::create($nomeAgrup, $emailObs, null, $telefone, null, $disponibilidade, 
                        $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);

                        $agrupamento = new Agrupamento();
                        $agrupamento->nomeDiretor = $diretor;
                        $agrupamento->id_colaborador = $idColabAgrupamento;
                        $agrupamento->save();

                        $idAgrupamento = $agrupamento->getKey();
                        $agrupInserido = array("id" => $idAgrupamento,"nome" => $nomeAgrup);
                        array_push($agrupInseridos, $agrupInserido);    
                    }
                }
                else {
                    $agrupamento = Agrupamento::find($idAgrupamento);
                    $idColabAgrupamento = $agrupamento->id_colaborador;
                }    
            }
            

            /* OBTENÇÃO DAS INFORMAÇÕES DAS ESCOLA */
            $nomeEscola = $row[8];
            if($nomeEscola != null) {
                if(strlen($nomeEscola) > 150) {
                    $nomeEscola = substr($nomeEscola,0,150);
                }
                $telefone = $row[9];
                if($telefone != null) {
                    $telefone = trim($telefone);
                    if(!is_numeric($telefone)) {
                        $telefone = null;
                    }
                }
                $contAssPaisArray = str_split($row[19]);
                $contAssPais = "";
                for ($i = 0; $i < count($contAssPaisArray); ++$i) {
                    if(is_numeric($contAssPaisArray[$i])) {
                        if(is_numeric(substr($row[19], $i, 9))) {
                            $contAssPais = substr($row[19], $i, 9);
                            break;
                        }
                    }
                }
                $rua = null;
                $numeroPorta = null;
                $codPostal = null;
                $codPostalRua = null;
                $localidade = null;
                $distrito = null; 
                if($row[10] != null) {
                    $moradaEscola = explode(",", $row[10]);
                    if($moradaEscola != null && count($moradaEscola) == 5) {
                        if($moradaEscola[0] != null) {
                        $rua = $moradaEscola[0]; 
                        }
                        if($moradaEscola[1] != null) {
                            $numeroPorta = $moradaEscola[1];
                        }
                        if($moradaEscola[2] != null) {
                            if($moradaEscola[2] != null) {
                                $codArray = explode("-", $moradaEscola[2], 2);
                                if(count($codArray) == 2) {
                                    if(is_numeric($codArray[0]) && is_numeric($codArray[1])) {
                                    $codPostal = $codArray[0]; 
                                    $codPostalRua = $codArray[1];  
                                    }
                                }   
                            }  
                        }
                        if($moradaEscola[3] != null) {
                            $localidade = $moradaEscola[3];    
                        }
                        if($moradaEscola[4] != null) {
                            $distrito = $moradaEscola[4];
                        }
                    }
                }
                
                
                /* VERIFICAÇÃO SE A ESCOLA JÁ FOI INSERIDA */
                $existeEscola = false;
                foreach($escolasInseridas as $escola) {
                    if($escola["nome"] == $nomeEscola) {
                        $existeEscola = true;
                        $idEscola = $escola["id"];
                        break;
                    }
                }
                    
                /* SE A ESCOLA NÃO FOI INSERIDA, CRIA-SE O OBJETO COLABORADOR E A RESPETIVA ESCOLA
                INSERINDO-A NO ARRAY DE ESCOLAS INSERIDAS */
                if(!$existeEscola) {
                    if($nomeEscola != null && $nomeEscola != "") {
                        $idColabEscola = ColaboradorController::create($nomeEscola, null, null, $telefone, $numeroPorta, $disponibilidade,
                        $codPostal, $codPostalRua, $rua, $localidade, $distrito, null);

                        $escsolidarias = new EscolaSolidaria();
                        $escsolidarias->contactoAssPais = $contAssPais;
                        if($idAgrupamento != -1) {
                            $escsolidarias->id_agrupamento = $idAgrupamento;    
                        }
                        $escsolidarias->id_colaborador = $idColabEscola;
                        $escsolidarias->save();

                        $idEscola = $escsolidarias->getKey();

                        $escolaInserida = array("id" => $idEscola,"nome" => $nomeEscola);
                        array_push($escolasInseridas, $escolaInserida);    
                    }
                }
                else {
                    $escola = EscolaSolidaria::find($idEscola);
                    $idColabEscola = $escola->id_colaborador;
                }

                /* OBTER AS INFORMAÇÕES DA COMUNICAÇÃO, REALIZANDO A SUA CRIAÇÃO
                SE ESTA EXISTIR E SE POSSÍVEL */
                $observacoes = str_split($row[18]);
                if($observacoes != null) {
                    $data = self::obterData($observacoes);
                    $obs = $row[18];
                    
                    $comunicacao = new Comunicacao();
                    if($data != null) {
                        $comunicacao->data = $data;    
                    }
                    else {
                        $comunicacao->data = null;
                    }
                    $comunicacao->observacoes = $obs;
                    $comunicacao->id_colaborador = $idColabEscola;
                    $comunicacao->save();        
                }    
            }
            

            /* OBTENÇÃO DAS INFORMAÇÕES DO PROFESSOR */
            $nomeProf = $row[11];
            if($nomeProf != null) {
                $funcaoProjeto = $row[12];
                $emails = array();
                $emailObs = null;
                if($row[13] != null) {
                    if(sizeof(explode(";", $row[13])) > 1) {
                        $emailArray = explode(";", $row[13]);
                        foreach($emailArray as $emailStr) {
                            $email = DB::table('email')
                            ->where('email.email', '=', "$emailStr")
                            ->first();
                            if($email != null) {
                                $emailObs = $emailObs.$email->email."; ";
                            }
                            else {
                                array_push($emails, $emailStr); 
                            }
                        }
                    }
                    else {
                        $emailStr = $row[13];
                        $email = DB::table('email')
                            ->where('email.email', '=', "$emailStr")
                            ->first();
                        if($email != null) {
                            $emailObs = "Email: ".$email->email;
                        }
                        else {
                            array_push($emails, $emailStr); 
                        }       
                    }  
                }
                $telemovel = $row[14];
                if($telemovel != null) {
                    $telemovel = trim($telemovel);
                    if(!is_numeric($telefone)) {
                        $telefone = null;
                    }
                }

                //CRIAÇÃO DO CARGO ASSOCIADO AO PROJETO, CASO NÃO EXISTA
                if($funcaoProjeto != null) {
                    $funcaoProjeto = ucfirst($funcaoProjeto);
                    $existeCargo = false;
                    foreach($cargosInseridos as $cargo) {
                        if($cargo["nome"] == $funcaoProjeto) {
                            $existeCargo = true;
                            $idCargo = $cargo["id"];
                            break;
                        }
                    }
                    if(!$existeCargo) {
                        $cargoProf = new CargoProf();
                        $cargoProf->nomeCargo = $funcaoProjeto;
                        $cargoProf->save();
                        $idCargo = $cargoProf->getKey();
                        $cargoInserido = array("id" => $idCargo,"nome" => $funcaoProjeto);
                        array_push($cargosInseridos, $cargoInserido);
                    }
                }
                

                /* VERIFICAÇÃO SE O PROFESSOR JÁ FOI INSERIDO */
                $existeProfessor = false;
                foreach($professoresInseridos as $professor) {
                    if($professor["nome"] == $nomeProf) {
                        $existeProfessor = true;
                        $idProfessor = $professor["id"];
                        break;
                    }
                }

                /* INSERIR O PROFESSOR SE ELE NÃO EXISTIR NA BASE DE DADOS */
                $idColabProf = -1;
                if(!$existeProfessor) {
                    if($nomeProf != null && $nomeProf != "") {
                        $idColabProf = ColaboradorController::create($nomeProf, $emailObs, $telemovel, null, null, $disponibilidade,
                        null, null, null, null, null, $emails);
            
                        $professor = new Professor();
                        $professor->id_colaborador = $idColabProf;
                        $professor->save();
            
                        $idProfessor = $professor->getKey();
                        $professorInserido = array("id" => $idProfessor,"nome" => $nomeProf);
                        array_push($professoresInseridos, $professorInserido);    
                    }
                }
                else {
                    $professor = Professor::find($idProfessor);
                    $idColabProf = $professor->id_colaborador;
                }

                /* VERIFICAÇÃO SE EXISTE A ASSOCIACAO DO PROFESSORA À ESCOLA, REALIZANDO A SUA CRIAÇÃO
                CASO NÃO SE VERIFIQUE */
                if($idEscola != -1) {
                    $existeAssociacao = ProfessorController::existeAssociacao($idProfessor, $idEscola);
                    if(!$existeAssociacao) {
                        $novaAssoc = new EscolaSolidariaProf();
                        $novaAssoc->id_escola = $idEscola;
                        $novaAssoc->id_professor = $idProfessor;
                        $novaAssoc->interlocutor = 1;
                        $novaAssoc->save();

                        $professor = Professor::find($idProfessor);
                        $escola = EscolaSolidaria::find($idEscola);
                        $professor->id_agrupamento = $escola->id_agrupamento;
                        $professor->save();
                    }    
                }
                
                //CRIAÇÃO DAS ASSOCIAÇÕES DO PROFESSOR E ESCOLA ASSOCIADA AO PROJETO
                $ano = 2021;
                for($i = 21; $i < 33; ++$i) {
                    if($row[$i] != null) {
                        if(strtolower($row[$i]) == "sim") {
                            $existeAssociacaoProj = ProjetoProfessorController::verificaAssociacao($idProfessor, $idProjeto, $ano);
                            $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                            if(!$existeAssociacaoProj) {
                                $projcontador = new ProjetoProfessor();
                                $projcontador->id_projeto = $idProjeto;
                                $projcontador->id_professor = $idProfessor;
                                $projcontador->anoParticipacao = $ano;
                                if($idCargo != -1) {
                                    $projcontador->id_cargo = $idCargo;    
                                }
                                else {
                                    $projcontador->id_cargo = null;
                                }
                                $projcontador->save();
                            }
                            if($idEscola != -1) {
                                $existeAssociacaoProj = ProjetoEscolaController::verificaAssociacao($idEscola, $idProjeto, $ano);
                                $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                                if(!$existeAssociacaoProj) {
                                    $projescola = new ProjetoEscola();
                                    $projescola->id_projeto = $idProjeto;
                                    $projescola->id_escolaSolidaria = $idEscola;
                                    $projescola->anoParticipacao = $ano;
                                    $projescola->save();
                                }
                            }
                            $ano = $ano - 1;
                        }
                    }
                }    
            }
            
        }
    }

    public function obterData($observacoes) {
        $data = "";
        $metodo1 = "";
        $metodo2 = "";
        $metodo3 = "";
        $metodo4 = "";
        $metodo5 = "";
        $metodos = array();
        for ($i = 0; $i < 10; ++$i) {
            if(isset($observacoes[$i])) {
                $metodo1 = $metodo1.$observacoes[$i];
            }
        }
        for ($i = 0; $i < 8; ++$i) {
            if(isset($observacoes[$i])) {
                $metodo2 = $metodo2.$observacoes[$i]; 
            }
        }
        for ($i = 0; $i < 7; ++$i) {
            if(isset($observacoes[$i])) {
                $metodo3 = $metodo3.$observacoes[$i];    
            }
        }
        for ($i = 0; $i < 6; ++$i) {
            if(isset($observacoes[$i])) {
                $metodo4 = $metodo4.$observacoes[$i];    
            } 
        }
        for ($i = 0; $i < 4; ++$i) {
            if(isset($observacoes[$i])) {
                $metodo5 = $metodo5.$observacoes[$i];    
            }
        }
        array_push($metodos, $metodo1, $metodo2, $metodo3, $metodo4, $metodo5);
        for ($i = 0; $i < count($metodos); ++$i) {
            $metodo = $metodos[$i];
            if(!empty($metodo)) {
                $metodo = trim($metodo, " ");
                if(is_numeric($metodo[0]) && is_numeric(substr($metodo, -1))) {
                    $metodo = str_replace("/", "-", $metodo);
                    if($i == 0 && count(explode("-", $metodo, 3)) == 3) {
                        $data = DateTime::createFromFormat('d-m-Y', $metodo);
                        break;
                    }
                    elseif($i == 1 && count(explode("-", $metodo, 3)) == 3) {
                        $data = DateTime::createFromFormat('d-m-Y', $metodo);
                        break;
                    }
                    elseif($i == 2 && count(explode("-", $metodo, 3)) == 3) {
                        $data = DateTime::createFromFormat('d-m-Y', $metodo);
                        break;
                    }
                    elseif($i == 3 && count(explode("-", $metodo, 3)) == 3) {
                        $data = DateTime::createFromFormat('d-m-Y', $metodo);
                        break;
                    }
                    elseif($i == 4 && count(explode("-", $metodo, 2)) == 2) {
                        $metodo = $metodo."-2021";
                        $data = DateTime::createFromFormat('d-m-Y', $metodo);
                        break;
                    }
                }
                else {
                    $data = null;
                }   
            }
            else {
                $data = null;
            }
                
        }
        return $data;
    }
}
<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Agrupamento;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProfessorController;

class EstabelecimentosEnsinoSolidarioImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA OS AGRUPAMENTOS E ESCOLAS INSERIDOS
        $agrupInseridos = array();
        $escolasInseridas = array();
        $professoresInseridos = array();
        
        //PARA TESTAR SÓ PARA A PRIMEIRA LINHA - REMOVER QUANDO COCLUÍDO E DESCOMENTAR O FOREACH
        $row = $rows[1];
        //var_dump($row);

        //foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM AGRUPAMENTO */
            $nomeAgrup = $row[0];
            $rua = $row[1];
            $codArray = explode("-", $row[2], 2);
            $codPostal = $codArray[0];
            $codPostalRua = $codArray[1];
            $localidade = $row[3];
            $distrito = $row[4];
            $telefone = $row[5];
            $emails = array();
            if($row[6] != null) {
                array_push($emails, $row[6]);    
            }
            $diretor = $row[7];
            $disponibilidade = false;
            if($row[21] == "Sim") {
                $disponibilidade = true;
            }
            else if($row[21] == "Não") {
                $disponibilidade = true;
            }

            //VERIFICAÇÃO SE O AGRUPAMENTO JÁ FOI INSERIDO
            $idAgrupamento = 0;
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
                $idColab = ColaboradorController::create($nomeAgrup, null, null, $telefone, null, $disponibilidade, 
                $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);

                $agrupamento = new Agrupamento();
                $agrupamento->nomeDiretor = $diretor;
                $agrupamento->id_colaborador = $idColab;
                $agrupamento->save();

                $idAgrupamento = $agrupamento->id;
                $agrupInserido = array("id" => $idAgrupamento,"nome" => $nomeAgrup);
                array_push($agrupInseridos, $agrupInserido);
            }
            else {
                $agrupamento = Agrupamento::find($idAgrupamento);
                $idAgrupamento = $agrupamento->id_colaborador;
            }

            /* OBTENÇÃO DAS INFORMAÇÕES DAS ESCOLA */
            $nomeEscola = $row[8];
            $telefone = $row[9];
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
            $moradaEscola = explode(",", $row[10]);
            $rua = null;
            $numeroPorta = null;
            $codPostalTotal = null;
            $codPostal =  null;
            $codPostalRua = null;
            $localidade = null;
            $distrito = null;
            if($moradaEscola != null) {
                $rua = $moradaEscola[0];
                $numeroPorta = $moradaEscola[1];
                $codPostalTotal = explode("-", $moradaEscola[2], 2);
                $codPostal =  $codPostalTotal[0];
                $codPostalRua = $codPostalTotal[1];
                $localidade = $moradaEscola[3];
                $distrito = $moradaEscola[4];
            }
            
            /* VERIFICAÇÃO SE A ESCOLA JÁ FOI INSERIDA */
            $idEscola = 0;
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
                $idColabEscola = ColaboradorController::create($nomeEscola, null, null, $telefone, $numeroPorta, $disponibilidade,
                $codPostal, $codPostalRua, $rua, $localidade, $distrito, null);

                $escsolidarias = new EscolaSolidaria();
                $escsolidarias->contactoAssPais = $contAssPais;
                $escsolidarias->id_agrupamento = $idAgrupamento;
                $escsolidarias->id_colaborador = $idColabEscola;
                $escsolidarias->save();

                $idEscola = $escsolidarias->id;
                $escolaInserida = array("id" => $idEscola,"nome" => $nomeEscola);
                array_push($escolasInseridas, $escolaInserida);
            }
            else {
                $escola = Escola::find($idEscola);
                $idColabEscola = $escola->id_colaborador;
            }

            /* OBTER AS INFORMAÇÕES DA COMUNICAÇÃO, REALIZANDO A SUA CRIAÇÃO
               SE ESTA EXISTIR E SE POSSÍVEL */
            $observacoes = str_split($row[18]);
            if($observacoes != null) {
                $data = self::obterData($observacoes);
                $comunicacao = $row[18];

                $comunicacao = new Comunicacao();
                $comunicacao->data = $data;
                $comunicacao->observacoes = $comunicacao;
                $comunicacao->id_colaborador = $idColabEscola;
                $comunicacao->save();        
            }

            
            
            /* OBTENÇÃO DAS INFORMAÇÕES DO PROFESSOR */
            $nomeProf = $row[11];
            $funcaoProjeto = $row[12];
            $emails = array();
            if($row[14] != null) {
               array_push($emails, $row[13]); 
            }
            $telemovel = $row[14];

            /* VERIFICAÇÃO SE O PROFESSOR JÁ FOI INSERIDO */
            $idProfessor = 0;
            $existeProfessor = false;
            foreach($professoresInseridos as $professor) {
                if($professor["nome"] == $nomeProf) {
                    $existeProfessor = true;
                    $idProfessor = $professor["id"];
                    break;
                }
            }

            /* INSERIR O PROFESSOR SE ELE NÃO EXISTIR NA BASE DE DADOS */
            if(!$existeProfessor) {
                $idColabProf = ColaboradorController::create($nomeProf, null, $telemovel, null, null, $disponibilidade,
                null, null, null, null, null, $emails);
    
                $professor = new Professor();
                $professor->id_colaborador = $idColabProf;
                $professor->save();
    
                $idProfessor = $professor->id;
                $professorInserido = array("id" => $idProfessor,"nome" => $nomeProf);
                array_push($professoresInseridos, $professorInserido);
            }

            /* VERIFICAÇÃO SE EXISTE A ASSOCIACAO DO PROFESSORA À ESCOLA, REALIZANDO A SUA CRIAÇÃO
               CASO NÃO SE VERIFIQUE */
            $existeAssociacao = ProfessorController::existeAssociacao($idProfessor, $idEscola);
            if(!$existeAssociacao) {
                $novaAssoc = new EscolaSolidariaProf();
                $novaAssoc->id_escola = $idEscola;
                $novaAssoc->id_professor = $idProfessor;
                $novaAssoc->save();

                $professor = Professor::find($idProfessor);
                $escola = EscolaSolidaria::find($idEscola);
                $professor->id_agrupamento = $escola->id_agrupamento;
                $professor->save();
            }


            
        //}
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
                $metodo1 = $metodo1.$observacoes[$i];
        }
        for ($i = 0; $i < 8; ++$i) {
                $metodo2 = $metodo2.$observacoes[$i];
        }
        for ($i = 0; $i < 7; ++$i) {
                $metodo3 = $metodo3.$observacoes[$i];
        }
        for ($i = 0; $i < 6; ++$i) {
                $metodo4 = $metodo4.$observacoes[$i];
        }
        for ($i = 0; $i < 4; ++$i) {
                $metodo5 = $metodo5.$observacoes[$i];
        }
        array_push($metodos, $metodo1, $metodo2, $metodo3, $metodo4, $metodo5);
        for ($i = 0; $i < count($metodos); ++$i) {
            $metodo = $metodos[$i];
            $metodo = trim($metodo, " ");
            if(is_numeric($metodo[0]) && is_numeric(substr($metodo, -1))) {
                $metodo = str_replace("/", "-", $metodo);
                if($i == 0 && count(explode("-", $metodo, 3)) == 3) {
                    $data = $metodo;
                    break;
                }
                elseif($i == 1 && count(explode("-", $metodo, 3)) == 3) {
                    $data = $metodo;
                    break;
                }
                elseif($i == 2 && count(explode("-", $metodo, 3)) == 3) {
                    $data = $metodo;
                    break;
                }
                elseif($i == 3 && count(explode("-", $metodo, 3)) == 3) {
                    $data = $metodo;
                    break;
                }
                elseif($i == 4 && count(explode("-", $metodo, 2)) == 2) {
                    $data = $metodo;
                    break;
                }
            }    
        }
        return $data;
    }
}
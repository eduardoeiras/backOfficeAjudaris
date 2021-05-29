<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Colaborador;
use App\Models\Comunicacao;
use App\Models\Juri;
use App\Models\ProjetoJuri;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\ProjetoJuriController;
use DateTime;
use DB;

class Revisao_JuriImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA O JURI
        $juriInseridos = array();
        
        
        //PARA TESTAR SÓ PARA A PRIMEIRA LINHA - REMOVER QUANDO COCLUÍDO E DESCOMENTAR O FOREACH
        $row = $rows[1];
        //var_dump($row);

        //CRIAÇÃO DO PROJETO AO QUAL OS PARTICIPANTES SERÃO ASSOCIADOS
        $idProjeto = -1;
        $projeto = DB::table('projeto')
                    ->where('projeto.nome', '=', "Histórias da Ajudaris")
                    ->orderBy('projeto.id_projeto')->first();

        if($projeto != null) {
            $idProjeto = $projeto->id_projeto;
        }

        //foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM JURI */
            $nome = $row[0];
            $rua = $row[1];
            $codArray = explode("-", $row[3], 2);
            $codPostal = $codArray[0];
            $codPostalRua = $codArray[1];
            $localidade = $row[3];
            $distrito = $row[4];
            $telefone = $row[2];
            $emails = array();
            if($row[6] != null) {
                array_push($emails, $row[1]);    
            }
            $tipoJuri = $row[7];
            $disponibilidade = false;
            if(strtolower($row[21]) == "sim") {
                $disponibilidade = true;
            }
            else {
                $disponibilidade = false;
            }

            //VERIFICAÇÃO SE O AGRUPAMENTO JÁ FOI INSERIDO
            $idJuri = -1;
            $existe = false;
            foreach($juriInseridos as $juri) {
                if($juri["nome"] == $nome) {
                    $existe = true;
                    $idJuri = $juri["id"];
                    break;
                }
            }

            /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO JURI COLOCANDO-O NO ARRAY DE
              DE JURIS JÁ INSERIDOS  */
            $idColabJuri = -1;
            if(!$existe) {
                $idColabJuri = ColaboradorController::create($nome, null, null, $telefone, null, $disponibilidade, 
                $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);

                $juri = new Juri();
                $juri->tipoJuri = $tipoJuri;
                $juri->id_colaborador = $idColabJuri;
                $juri->save();

                $idJuri = $juri->getKey();
                $juriInserido = array("id" => $idJuri,"nome" => $nome);
                array_push($juriInseridos, $juriInserido);
            }
            else {
                $juri = Juri::find($idJuri);
                $idColabJuri = $juri->id_colaborador;
            }

            /* OBTER AS INFORMAÇÕES DA COMUNICAÇÃO, REALIZANDO A SUA CRIAÇÃO
               SE ESTA EXISTIR E SE POSSÍVEL */
            $observacoes = str_split($row[11]);
            if($observacoes != null) {
                $data = self::obterData($observacoes);
                $obs = $row[11];

                $comunicacao = new Comunicacao();
                $comunicacao->data = $data;
                $comunicacao->observacoes = $obs;
                $comunicacao->id_colaborador = $idColabJuri;
                $comunicacao->save();        
            }

            //CRIAÇÃO DAS ASSOCIAÇÕES DO JURI ASSOCIADA AO PROJETO
            $ano = 2021;
            for($i = 21; $i < 33; ++$i) {
                if($row[$i] != null) {
                    if(strtolower($row[$i]) == "sim") {
                        $existeAssociacaoProj = ProjetoJuriController::verificaAssociacao($idJuri, $idProjeto, $ano);
                        $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                        if(!$existeAssociacaoProj) {
                            $projcontador = new ProjetoJuri();
                            $projcontador->id_projeto = $idProjeto;
                            $projcontador->id_juri = $idJuri;
                            $projcontador->anoParticipacao = $ano;
                            $projcontador->save();
                        }
                        $ano = $ano - 1;
                    }
                }
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
        }
        return $data;
    }
}
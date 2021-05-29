<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Colaborador;
use App\Models\Comunicacao;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProjetoEntidadeController;
use App\Models\EntidadeOficial;
use App\Models\ProjetoEntidade;
use DateTime;
use DB;

class EntidadesOficiaisImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);
        unset($rows[1]);
        unset($rows[2]);

        //CRIAÇÃO DOS ARRAYS PARA O JURI
        $entidadeInseridas = array();
        
        
        //PARA TESTAR SÓ PARA A PRIMEIRA LINHA - REMOVER QUANDO COCLUÍDO E DESCOMENTAR O FOREACH
        $row = $rows[3];
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
            $nome = $row[2];
            $nomeEntidade = $row[1];
            $observacoes = $row[5];
            $telefone = $row[4];
            $emails = array();
            if($row[3] != null) {
                array_push($emails, $row[3]);    
            }
            /*$disponibilidade = false;
            if(strtolower($row[21]) == "sim") {
                $disponibilidade = true;
            }
            else {
                $disponibilidade = false;
            }*/

            //VERIFICAÇÃO SE A ENTIDADE JÁ FOI INSERIDO
            $idEntidade = -1;
            $existe = false;
            foreach($entidadeInseridas as $entidade) {
                if($entidade["nome"] == $nome) {
                    $existe = true;
                    $idEntidade = $entidade["id"];
                    break;
                }
            }

            /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO JURI COLOCANDO-O NO ARRAY DE
              DE JURIS JÁ INSERIDOS  */
            $idColabEntidade = -1;
            if(!$existe) {
                $idColabEntidade = ColaboradorController::create($nome, $observacoes, null, $telefone, null, null, 
                null, null, null, null, null, $emails);

                $entidade = new EntidadeOficial();
                $entidade->id_colaborador = $idColabEntidade;
                $entidade->entidade = $nomeEntidade;
                $entidade->save();

                $idEntidade = $entidade->getKey();
                $entidadeInserida = array("id" => $idEntidade,"nome" => $nome);
                array_push($entidadeInseridas, $entidadeInserida);
            }
            else {
                $entidade = EntidadeOficial::find($idEntidade);
                $idColabEntidade = $entidade->id_colaborador;
            }

            /* OBTER AS INFORMAÇÕES DA COMUNICAÇÃO, REALIZANDO A SUA CRIAÇÃO
               SE ESTA EXISTIR E SE POSSÍVEL */
            /*$observacoes = str_split($row[4]);
            if($observacoes != null) {
                $data = self::obterData($observacoes);
                $obs = $row[4];

                $comunicacao = new Comunicacao();
                $comunicacao->data = $data;
                $comunicacao->observacoes = $obs;
                $comunicacao->id_colaborador = $idColabEntidade;
                $comunicacao->save();        
            }*/

            //CRIAÇÃO DAS ASSOCIAÇÕES DO JURI ASSOCIADA AO PROJETO
            /*$ano = 2021;
            for($i = 21; $i < 33; ++$i) {
                if($row[$i] != null) {
                    if(strtolower($row[$i]) == "sim") {
                        $existeAssociacaoProj = ProjetoEntidadeController::verificaAssociacao($idEntidade, $idProjeto, $ano);
                        $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                        if(!$existeAssociacaoProj) {
                            $projcontador = new ProjetoEntidade();
                            $projcontador->id_projeto = $idProjeto;
                            $projcontador->id_entidade = $idEntidade;
                            $projcontador->anoParticipacao = $ano;
                            $projcontador->save();
                        }
                        $ano = $ano - 1;
                    }
                }
            }*/

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
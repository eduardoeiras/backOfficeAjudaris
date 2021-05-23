<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Agrupamento;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use App\Http\Controllers\ColaboradorController;

class EscolasAgrupamentosImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA OS AGRUPAMENTOS E ESCOLAS INSERIDOS
        $agrupInseridos = array();
        $escolasInseridas = array();
        
        //PARA TESTAR SÓ PARA A PRIMEIRA LINHA
        $row = $rows[1];
        var_dump($row);

        //foreach($rows as $row) {

            //OBTENÇÃO DAS INFORMAÇÕES DE UM AGRUPAMENTO
            $nomeAgrup = $row[0];
            $rua = $row[1];
            $codArray = explode("-", $row[2], 2);
            $codPostal = $codArray[0];
            $codPostalRua = $codArray[1];
            $localidade = $row[3];
            $distrito = $row[4];
            $telefone = $row[5];
            $emails = array();
            array_push($emails, $row[6]);
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
                if($agrup["nome"] != $nomeAgrup) {
                    $existe = true;
                    $idAgrupamento = $agrup["id"];
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

            /* VERIFICAÇÃO SE A ESCOLA JÁ FOI INSERIDA */
            $idEscola = 0;
            $existeEscola = false;
            foreach($escolasInseridas as $escola) {
                if($escola["nome"] != $nomeEscola) {
                    $existeEscola = true;
                    $idEscola = $escola["id"];
                }
            }
                
            /* SE A ESCOLA NÃO FOI INSERIDA, CRIA-SE O OBJETO COLABORADOR E A RESPETIVA ESCOLA
               INSERINDO-A NO ARRAY DE ESCOLAS INSERIDAS */
            if(!$existeEscola) {
                $idColabEscola = ColaboradorController::create($nomeEscola, null, null, $telefone, $disponibilidade,
                null, null, null, null, null, null);

                $escsolidarias = new EscolaSolidaria();
                $escsolidarias->contactoAssPais = $contAssPais;
                $escsolidarias->id_agrupamento = $idAgrupamento;
                $escsolidarias->id_colaborador = $idColabEscola;
                $escsolidarias->save();

                $idEscola = $escsolidarias->id;
                $escolaInserida = array("id" => $idEscola,"nome" => $nomeEscola);
                array_push($escolasInseridas, $escolaInserida);
            }
                       
        }
    }
}
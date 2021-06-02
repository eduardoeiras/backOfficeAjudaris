<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\ContadorHistoria;
use App\Models\Colaborador;
use App\Http\Controllers\ColaboradorController;

class ContadoresHistoriasImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //UNSET DA PRIMEIRA E SEGUNDA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA OS CONTADORES INSERIDOS
        $contadoresInseridos = array();

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM CONTADOR DE HISTÓRIAS */
            if($row[1] != null) {
                $nomeContador = $row[1];
                $telefone = $row[4];
                $telemovel = $row[3];
                $emails = array();
                if($row[2] != null) {
                    array_push($emails, $row[2]);
                }
                $rua = null;
                $codPostal = null;
                $codPostalRua = null;
                $localidade = null;
                $distrito = null;
                $numeroPorta = null;
                $morada = $row[5];
                if($morada != null) {
                    $morada = explode(",", $morada);
                    if($morada != null && count($morada) == 5) {
                        if($morada[0] != null) {
                           $rua = $morada[0]; 
                        }
                        if($morada[1] != null) {
                            $numeroPorta = $morada[1];
                        }
                        if($morada[2] != null) {
                            $codPostalTotal = explode("-", $morada[2], 2);
                            $codPostal =  $codPostalTotal[0];
                            $codPostalRua = $codPostalTotal[1];   
                        }
                        if($morada[3] != null) {
                            $localidade = $morada[3];    
                        }
                        if($morada[4] != null) {
                            $distrito = $morada[4];
                        }
                    }
                }
                $observacoes = $row[6];
                $disponibilidade = 1;

                /* VERIFICAÇÃO SE O ILUSTRADOR JÁ FOI INSERIDO */
                $idContador = -1;
                $existe = false;
                foreach($contadoresInseridos as $contador) {
                    if($contador["nome"] == $nomeContador) {
                        $existe = true;
                        $idContador = $contador["id"];
                        break;
                    }
                }

                /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO CONTADOR DE HISTÓRIAS, COLOCANDO-O NO ARRAY DE
                DE CONTADORES JÁ INSERIDOS  */
                $idColabContador = -1;
                if(!$existe) {
                    $idColabContador = ColaboradorController::create($nomeContador, $observacoes, $telemovel, $telefone, 
                    $numeroPorta, $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);

                    $contadorHistoria = new ContadorHistoria();
                    $contadorHistoria->id_colaborador = $idColabContador;
                    $contadorHistoria->save();

                    $idContador = $contadorHistoria->getKey();
                    $contadorInserido = array("id" => $idContador,"nome" => $nomeContador);
                    array_push($contadoresInseridos, $contadorInserido);
                }  
            } 
        }
    }
}
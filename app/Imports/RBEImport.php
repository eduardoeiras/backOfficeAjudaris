<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\RBE;
use App\Models\Colaborador;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ConcelhoController;

class RBEImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA E SEGUNDA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);
        unset($rows[1]);

        //CRIAÇÃO DOS ARRAYS PARA AS RBES INSERIDAS
        $rbesInseridas = array();

        $nomeCoordenador = null;
        $email = null;
        $disponibilidade = 1;

        foreach($rows as $row) {

            $regiao = $row[0];
            $concelho = $row[3];

            /* OBTENÇÃO DAS INFORMAÇÕES DE UMA RBE */
            if($row[1] != null) {
                $nomeCoordenador = $row[1];
                $email = $row[2];
            }

            if(!is_int($regiao)) {
                /* VERIFICAÇÃO SE A RBE COM NOME DO COORDENADOR E REGIÃO JÁ EXISTE, OBTENDO O SEU ID */
                $idRbe = -1;
                $existe = false;
                foreach($rbesInseridas as $rbe) {
                    if($rbe["nome"] == $nomeCoordenador) {
                        if($rbe["regiao"] == $regiao) {
                            $existe = true;
                            $idRbe = $rbe["id"];
                            break;    
                        }
                    }
                } 
                
                /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO CONTADOR DE HISTÓRIAS, COLOCANDO-O NO ARRAY DE
                DE CONTADORES JÁ INSERIDOS  */
                $idColabRbe = -1;
                if(!$existe) {
                    $emailObs = "Email do Coordenador: ".$email;

                    $idColabRbe = ColaboradorController::create($nomeCoordenador, $emailObs, null, null, 
                    null, $disponibilidade, null, null, null, null, null, null);

                    $rbe = new RBE();
                    $rbe->regiao = $regiao;
                    $rbe->id_colaborador = $idColabRbe;
                    $rbe->save();

                    $idRbe = $rbe->getKey();
                    $rbeInserida = array("id" => $idRbe,"nome" => $nomeCoordenador, "regiao" => $regiao);
                    array_push($rbesInseridas, $rbeInserida);
                }
                
                if($concelho != null) {
                    $concelhosArray = array();
                    array_push($concelhosArray, $concelho);
                    ConcelhoController::criaAssociaConcelhos($concelhosArray, $idRbe);  
                }
                
            }
        }
    }
}
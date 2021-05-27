<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IlustradorSolidario;
use App\Models\Colaborador;
use App\Models\ProjetoIlustrador;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProjetoIlustradorController;
use DB;

class IlustradorSolidarioImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA E SEGUNDA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);
        unset($rows[1]);

        //CRIAÇÃO DOS ARRAYS PARA OS ILUSTRADORES INSERIDOS
        $ilustradoresInseridos = array();

        //OBTENÇÃO DO ID DO PROJETO AO QUAL OS ILUSTRADORES SERÃO ASSOCIADOS
        $idProjeto = -1;
        $projeto = DB::table('projeto')
                    ->where('projeto.nome', '=', "Histórias da Ajudaris")
                    ->orderBy('projeto.id_projeto')->first();

        if($projeto != null) {
            $idProjeto = $projeto->id_projeto;
        }

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM ILUSTRADOR SOLIDÁRIO */
            $nomeIlustrador = $row[0];
            $telefone = $row[1];
            $emails = array();
            if($row[2] != null) {
                array_push($emails, $row[2]);
            }
            $rua = $row[3];
            $codPostal = null;
            $codPostalRua = null;
            if($row[4] != null) {
                $codArray = explode("-", $row[4], 2);
                $codPostal = $codArray[0];
                $codPostalRua = $codArray[1];
            }
            $localidade = $row[5];
            $distrito = $row[6];
            $volumeLivro = $row[20];
            $observacoes = $row[23];
            $disponibilidade = false;
            if(strtolower($row[21]) == "sim") {
                $disponibilidade = false;
            }
            else {
                $disponibilidade = true;
            }

            /* VERIFICAÇÃO SE O ILUSTRADOR JÁ FOI INSERIDO */
            $idIlustrador = -1;
            $existe = false;
            foreach($ilustradoresInseridos as $ilustrador) {
                if($ilustrador["nome"] == $nomeIlustrador) {
                    $existe = true;
                    $idIlustrador = $ilustrador["id"];
                    break;
                }
            }

            /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO ILUSTRADOR COLOCANDO-O NO ARRAY DE
              DE ILUSTRADORES JÁ INSERIDOS  */
            $idColabIlustrador = -1;
            if(!$existe) {
                $idColabIlustrador = ColaboradorController::create($nomeIlustrador, $observacoes, null, $telefone, null, $disponibilidade, 
                $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);

                $ilustrador = new IlustradorSolidario();
                $ilustrador->volumeLivro = $volumeLivro;
                $ilustrador->id_colaborador = $idColabIlustrador;
                $ilustrador->save();

                $idIlustrador = $ilustrador->getKey();
                $ilustradorInserido = array("id" => $idIlustrador,"nome" => $nomeIlustrador);
                array_push($ilustradoresInseridos, $ilustradorInserido);
            }
            else {
                $ilustrador = IlustradorSolidario::find($idIlustrador);
                $idColabIlustrador = $ilustrador->id_colaborador;
            }

            //CRIAÇÃO DAS ASSOCIAÇÕES DO ILUSTRADOR AO PROJETO
            $ano = 2009;
            for($i = 7; $i < 19; ++$i) {
                if($row[$i] != null) {
                    if(strtolower($row[$i]) == "sim") {
                        $existeAssociacaoProj = ProjetoIlustradorController::verificaAssociacao($idIlustrador, $idProjeto, $ano);
                        $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                        if(!$existeAssociacaoProj) {
                            $projIlustrador = new ProjetoIlustrador();
                            $projIlustrador->id_projeto = $idProjeto;
                            $projIlustrador->id_ilustradorSolidario = $idIlustrador;
                            $projIlustrador->anoParticipacao = $ano;
                            $projIlustrador->save();
                        }
                        $ano = $ano + 1;
                    }
                }
            }
            $ano = 2021;
            $associacao2021 = $row[21];
            if(strtolower($associacao2021) == "sim") {
                $existeAssociacaoProj = ProjetoIlustradorController::verificaAssociacao($idIlustrador, $idProjeto, $ano);
                $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                if(!$existeAssociacaoProj) {
                    $projIlustrador = new ProjetoIlustrador();
                    $projIlustrador->id_projeto = $idProjeto;
                    $projIlustrador->id_ilustradorSolidario = $idIlustrador;
                    $projIlustrador->anoParticipacao = $ano;
                    $projIlustrador->save();
                }    
            }
        }
    }
}
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
        $headers = $rows[0];
        unset($rows[0]);

        //CRIAÇÃO DOS ARRAYS PARA O JURI
        $juriInseridos = array();
        
        /* CRIAÇÃO DO PROJETO AO QUAL OS PARTICIPANTES SERÃO ASSOCIADOS */
        $idProjeto = -1;
        $projeto = DB::table('projeto')
                    ->where('projeto.nome', '=', "Histórias da Ajudaris")
                    ->orderBy('projeto.id_projeto')->first();

        if($projeto != null) {
            $idProjeto = $projeto->id_projeto;
        }

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM JURI */
            $nome = $row[0];
            if($nome != null) {
                $observacoes = $row[19];
                $telefone = $row[2];
                $emails = array();
                if($row[1] != null) {
                    array_push($emails, $row[1]);    
                }
                $tipoJuri = null; 
                $disponibilidade = 1;
                if($row[17] != null && $row[18] != null) {
                    if(strtolower($row[18]) == "sim") {
                        $tipoJuri = 1;
                    }
                    if(strtolower($row[17]) == "sim") {
                        $tipoJuri = 0;
                    }
                    if(strtolower($row[17]) == "sim" && strtolower($row[18]) == "sim") {
                        $tipoJuri = 2;
                    }

                    if(strtolower($row[18]) == "sim") {
                        $disponibilidade = 0;
                    }
                    if(strtolower($row[17]) == "sim") {
                        $disponibilidade = 0;
                    }
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
                    $idColabJuri = ColaboradorController::create($nome, $observacoes, null, $telefone, null, $disponibilidade, 
                    null, null, null, null, null, $emails);
    
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
                $inseriu = false;
                for($i = 4; $i < 19; ++$i) {
                    $headerColumn = $headers[$i];
                    $anoArray = explode(" ", $headerColumn, 2);
                    if(strtolower($anoArray[0]) == "júri") {
                        $tipoJuri = 0;
                    }
                    if(strtolower($anoArray[0]) == "revisão") {
                        $tipoJuri = 1;
                    }
                    $ano = $anoArray[1];
                    if($row[$i] != null) {
                        if(strtolower($row[$i]) == "sim") {
                            $existeAssociacaoProj = ProjetoJuriController::verificaAssociacao($idJuri, $idProjeto, $ano);
                            $existeAssociacaoProj = ($existeAssociacaoProj === "true");
                            if(!$existeAssociacaoProj) {
                                $inseriu = true;
                                $projuri = new ProjetoJuri();
                                $projuri->id_projeto = $idProjeto;
                                $projuri->id_juri = $idJuri;
                                $projuri->tipoParticipacao = $tipoJuri;
                                $projuri->anoParticipacao = $ano;
                                $projuri->save();
                            }
                            else {
                                if($inseriu) {
                                   $projuri = DB::table('projeto_juri')
                                    ->where([
                                        ['projeto_juri.id_projeto', '=', $idProjeto],
                                        ['projeto_juri.id_juri', '=', $idJuri],
                                        ['projeto_juri.anoParticipacao', '=', $ano]
                                        ]);

                                    if($projuri->first() != null) {
                                        $projuri->update(["tipoParticipacao" => 2]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
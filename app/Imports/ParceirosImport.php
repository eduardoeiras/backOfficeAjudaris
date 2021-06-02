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
use DB;

class ParceirosImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //REMOÇÃO DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);
        
        //CRIAÇÃO DOS ARRAYS PARA O JURI
        $parceirosInseridos = array();
        
        
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

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UM JURI */

            if($row[0] != null) {
                $nomeEntidade = $row[0];
                $nome = $row[1];
                $categoria = "\nCategoria: ".$row[9];
                $observacoes = $categoria.$row[10];
                $rua = $row[5];
                $codPostal = null;
                $codPostalRua = null;
                if($row[6] != null) {
                    $codArray = explode("-", $row[6], 2);
                    if(count($codArray) == 2) {
                        if(is_numeric($codArray[0]) && is_numeric($codArray[1])) {
                        $codPostal = $codArray[0]; 
                        $codPostalRua = $codArray[1];  
                        }
                    }   
                }
                //$codArray = explode("-", $row[6], 2);
                //$codPostal = $codArray[0];
                //$codPostalRua = $codArray[1];
                $localidade = $row[7];
                $distrito = $row[8];
                $telefone = $row[3];
                $emails = array();
                if($row[4] != null) {
                    array_push($emails, $row[4]);    
                }
                $disponibilidade = false;
                if(strtolower($row[11]) == "sim") {
                    $disponibilidade = true;
                }
                else {
                    $disponibilidade = false;
                }
    
                //VERIFICAÇÃO SE O PARCEIRO JÁ FOI INSERIDO
                $idParceiro = -1;
                $existe = false;
                foreach($parceirosInseridos as $parceiro) {
                    if($parceiro["nome"] == $nome) {
                        $existe = true;
                        $idParceiro = $parceiro["id"];
                        break;
                    }
                }
    
                /* SE NÃO EXISTE É CRIADO O OBJETO COLABORADOR E O RESPETIVO JURI COLOCANDO-O NO ARRAY DE
                  DE JURIS JÁ INSERIDOS  */
                $idColabParceiro = -1;
                if(!$existe) {
                    $idColabParceiro = ColaboradorController::create($nome, $observacoes, null, $telefone, null, $disponibilidade, 
                    $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails);
    
                    $parceiro = new EntidadeOficial();
                    $parceiro->id_colaborador = $idColabParceiro;
                    $parceiro->entidade = $nomeEntidade;
                    $parceiro->save();
    
                    $idParceiro = $parceiro->getKey();
                    $parceiroInserido = array("id" => $idParceiro,"nome" => $nome);
                    array_push($parceirosInseridos, $parceiroInserido);
                }
                else {
                    $parceiro = EntidadeOficial::find($idParceiro);
                    $idColabParceiro = $parceiro->id_colaborador;
                }
            }
        }
    }
}
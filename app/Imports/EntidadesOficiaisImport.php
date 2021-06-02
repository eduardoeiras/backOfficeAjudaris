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
            $nome = $row[2];
            $nomeEntidade = $row[1];
            if(strlen($nome) > 150) {
                $nome = null;
            }
            if(strlen($nomeEntidade) > 150) {
                $nomeEntidade = null;
            }
            if($nome != null && $nomeEntidade != null){
                $observacoes = $row[5];
                $telefone = $row[4];
                $emails = array();
                if($row[3] != null) {
                    array_push($emails, $row[3]);    
                }
                $disponibilidade = 1;
                
    
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
                    $idColabEntidade = ColaboradorController::create($nome, $observacoes, null, $telefone, null, $disponibilidade, 
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
            }
        }
    }
}
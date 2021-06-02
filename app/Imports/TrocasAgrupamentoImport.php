<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\TrocaAgrupamento;
use App\Models\Colaborador;
use App\Models\Professor;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\TrocaAgrupamentoController;
use DB;

class TrocasAgrupamentoImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //UNSET DA PRIMEIRA LINHA COM A DESIGNAÇÃO DAS COLUNAS
        unset($rows[0]);

        foreach($rows as $row) {

            /* OBTENÇÃO DAS INFORMAÇÕES DE UMA TROCA DE AGRUPAMENTO */
            $idProfessor = -1;
            if($row[0] != null && $row[1] != null && $row[2] != null) {
                $agrupAnterior = $row[0];
                $novoArup = $row[1];
                $nomeProf = $row[2];
                $emails = array();
                if($row[4] != null) {
                    array_push($emails, $row[4]);
                }
                $telemovel = $row[3];
                $observacoes = $row[5];
                $disponibilidade = 1;

                /* VERIFICAR SE O PROFESSOR EXISTE */
                $prof = DB::table('professor')
                        ->join('colaborador', 'professor.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('colaborador.*', 'professor.*')
                        ->where([
                            ['colaborador.nome', '=', $nomeProf]
                            ])
                        ->first();
                
                /* CASO O PROFESSOR EXISTA, OBTÉM-SE O SEU ID */
                if($prof != null) {
                   $idProfessor = $prof->id_professor;
                }
                /* CASO CONTRÁRIO CRIA-SE O PROFESSOR E OBTÉM-SE O SEU ID */
                else {
                    $idColabProf = ColaboradorController::create($nomeProf, $observacoes, $telemovel, null, null, $disponibilidade,
                    null, null, null, null, null, $emails);
            
                    $professor = new Professor();
                    $professor->id_colaborador = $idColabProf;
                    $professor->save();
            
                    $idProfessor = $professor->getKey();
                }

                /* CRIAÇÃO DA TROCA DE AGRUPAMENTO */
                $trocas = new TrocaAgrupamento();
                $trocas->agrupamentoAntigo = $agrupAnterior;
                $trocas->novoAgrupamento = $novoArup;
                $trocas->data = date("Y-m-d");
                $trocas->id_professor = $idProfessor;
                $trocas->save();
                  
            } 
        }
    }
}
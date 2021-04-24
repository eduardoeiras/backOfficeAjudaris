<?php

namespace App\Http\Controllers;

use App\Models\ProjetoAgrupamento;
use Illuminate\Http\Request;
use DB;

class ProjetoAgrupamentoController extends Controller
{
    public function store(Request $request)
    {
        $assoc = new ProjetoAgrupamento();

        $assoc->id_projeto = intval($request->id_projeto);
        $assoc->id_agrupamento = intval($request->id_elemento);
        $assoc->anoParticipacao = intval($request->anoParticipacao);

        $assoc->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirProjeto", ['id' => intval($request->id_projeto)]);
        }
        else {
            return redirect()->route("gerirProjetoColaborador", ['id' => intval($request->id_projeto)]);
        }
    }

    public function destroy($id, $id_projeto, $ano)
    {
        $linha = DB::table('projeto_agrupamento')
                    ->where([
                        ['projeto_agrupamento.id_projeto', '=', $id_projeto],
                        ['projeto_agrupamento.id_agrupamento', '=', $id],
                        ['projeto_agrupamento.anoParticipacao', '=', $ano]
                        ]);
        if($linha->first() != null) {
            $linha->delete(); 
        }
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirProjeto", ['id' => intval($id_projeto)]); 
        }
        else {
            return redirect()->route("gerirProjetoColaborador", ['id' => intval($id_projeto)]);    
        }
    }

    public function verificaAssociacao($id, $id_projeto, $ano)
    {
        $exite = false;
        $linha = DB::table('projeto_agrupamento')
                    ->where([
                        ['projeto_agrupamento.id_projeto', '=', $id_projeto],
                        ['projeto_agrupamento.id_agrupamento', '=', $id],
                        ['projeto_agrupamento.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
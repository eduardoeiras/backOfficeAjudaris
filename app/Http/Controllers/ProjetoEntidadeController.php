<?php

namespace App\Http\Controllers;

use App\Models\ProjetoEntidade;
use Illuminate\Http\Request;
use DB;

class ProjetoEntidadeController extends Controller
{

    public function store(Request $request)
    {
        $projentidade = new ProjetoEntidade();

        $projentidade->id_projeto = intval($request->id_projeto);
        $projentidade->id_entidadeOficial = intval($request->id_elemento);
        $projentidade->anoParticipacao = intval($request->anoParticipacao);

        $projentidade->save();

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
        $linha = DB::table('projeto_entidade')
                    ->where([
                        ['projeto_entidade.id_projeto', '=', $id_projeto],
                        ['projeto_entidade.id_entidadeOficial', '=', $id],
                        ['projeto_entidade.anoParticipacao', '=', $ano]
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
        $linha = DB::table('projeto_entidade')
                    ->where([
                        ['projeto_entidade.id_projeto', '=', $id_projeto],
                        ['projeto_entidade.id_entidadeOficial', '=', $id],
                        ['projeto_entidade.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
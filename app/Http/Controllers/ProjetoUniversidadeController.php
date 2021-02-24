<?php

namespace App\Http\Controllers;

use App\Models\ProjetoUniversidade;
use Illuminate\Http\Request;
use DB;
class ProjetoUniversidadeController extends Controller
{

    public function store(Request $request)
    {
        $projuniversidade = new ProjetoUniversidade();

        $projuniversidade->id_projeto = intval($request->id_projeto);
        $projuniversidade->id_universidade = intval($request->id_elemento);
        $projuniversidade->anoParticipacao = intval($request->anoParticipacao);

        $projuniversidade->save();
        
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
        $linha = DB::table('projeto_universidade')
                    ->where([
                        ['projeto_universidade.id_projeto', '=', $id_projeto],
                        ['projeto_universidade.id_universidade', '=', $id],
                        ['projeto_universidade.anoParticipacao', '=', $ano]
                        ]);

        if($linha->first() != null) {
            $linha->delete(); 
        }
        return redirect()->route("gerirProjeto", ['id' => intval($id_projeto)]);
    }

    public function verificaAssociacao($id, $id_projeto, $ano)
    {
        $exite = false;
        $linha = DB::table('projeto_universidade')
                    ->where([
                        ['projeto_universidade.id_projeto', '=', $id_projeto],
                        ['projeto_universidade.id_universidade', '=', $id],
                        ['projeto_universidade.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
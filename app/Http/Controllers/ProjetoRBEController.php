<?php

namespace App\Http\Controllers;

use App\Models\ProjetoRBE;
use Illuminate\Http\Request;
use DB;
class ProjetoRBEController extends Controller
{
    public function store(Request $request)
    {
        $projrede = new ProjetoRBE();

        $projrede->id_projeto = intval($request->id_projeto);
        $projrede->id_rbe = intval($request->id_elemento);
        $projrede->anoParticipacao = intval($request->anoParticipacao);

        $projrede->save();
        
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
        $linha = DB::table('projeto_rbe')
                    ->where([
                        ['projeto_rbe.id_projeto', '=', $id_projeto],
                        ['projeto_rbe.id_rbe', '=', $id],
                        ['projeto_rbe.anoParticipacao', '=', $ano]
                        ]);
        
        if($linha->first() != null) {
            $linha->delete(); 
        }
        return redirect()->route("gerirProjeto", ['id' => intval($id_projeto)]);
    }

    public function verificaAssociacao($id, $id_projeto, $ano)
    {
        $exite = false;
        $linha = DB::table('projeto_rbe')
                    ->where([
                        ['projeto_rbe.id_projeto', '=', $id_projeto],
                        ['projeto_rbe.id_rbe', '=', $id],
                        ['projeto_rbe.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
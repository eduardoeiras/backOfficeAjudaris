<?php

namespace App\Http\Controllers;

use App\Models\ProjetoJuri;
use Illuminate\Http\Request;
use DB;

class ProjetoJuriController extends Controller
{

    public function store(Request $request)
    {
        $projjuri = new ProjetoJuri();

        $projjuri->id_projeto = intval($request->id_projeto);
        $projjuri->id_juri = intval($request->id_elemento);
        $projjuri->anoParticipacao = intval($request->anoParticipacao);
        $projjuri->tipoParticipacao = intval($request->cargo);

        $projjuri->save();
        
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
        $linha = DB::table('projeto_juri')
        ->where([
            ['projeto_juri.id_projeto', '=', $id_projeto],
            ['projeto_juri.id_juri', '=', $id],
            ['projeto_juri.anoParticipacao', '=', $ano]
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

    public static function verificaAssociacao($id, $id_projeto, $ano)
    {
        $exite = false;
        $linha = DB::table('projeto_juri')
                    ->where([
                        ['projeto_juri.id_projeto', '=', $id_projeto],
                        ['projeto_juri.id_juri', '=', $id],
                        ['projeto_juri.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
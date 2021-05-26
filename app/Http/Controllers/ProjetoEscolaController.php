<?php

namespace App\Http\Controllers;

use App\Models\ProjetoEscola;
use Illuminate\Http\Request;
use DB;

class ProjetoEscolaController extends Controller
{

    public function store(Request $request)
    {
        $projescola = new ProjetoEscola();

        $projescola->id_projeto = intval($request->id_projeto);
        $projescola->id_escolaSolidaria = intval($request->id_elemento);
        $projescola->anoParticipacao = intval($request->anoParticipacao);

        $projescola->save();

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
        $linha = DB::table('projeto_escola')
                    ->where([
                        ['projeto_escola.id_projeto', '=', $id_projeto],
                        ['projeto_escola.id_escolaSolidaria', '=', $id],
                        ['projeto_escola.anoParticipacao', '=', $ano]
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
        $linha = DB::table('projeto_escola')
                    ->where([
                        ['projeto_escola.id_projeto', '=', $id_projeto],
                        ['projeto_escola.id_escolaSolidaria', '=', $id],
                        ['projeto_escola.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
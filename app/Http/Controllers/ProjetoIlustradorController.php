<?php

namespace App\Http\Controllers;

use App\Models\ProjetoIlustrador;
use Illuminate\Http\Request;
use DB;

class ProjetoIlustradorController extends Controller
{

    public function store(Request $request)
    {
        $projilustradro = new ProjetoIlustrador();

        $projilustradro->id_projeto = intval($request->id_projeto);
        $projilustradro->id_ilustradorSolidario = intval($request->id_elemento);
        $projilustradro->anoParticipacao = intval($request->anoParticipacao);

        $projilustradro->save();
        
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
        $linha = DB::table('projeto_ilustrador')
                    ->where([
                        ['projeto_ilustrador.id_projeto', '=', $id_projeto],
                        ['projeto_ilustrador.id_ilustradorSolidario', '=', $id],
                        ['projeto_ilustrador.anoParticipacao', '=', $ano]
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
        $linha = DB::table('projeto_ilustrador')
                    ->where([
                        ['projeto_ilustrador.id_projeto', '=', $id_projeto],
                        ['projeto_ilustrador.id_ilustradorSolidario', '=', $id],
                        ['projeto_ilustrador.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
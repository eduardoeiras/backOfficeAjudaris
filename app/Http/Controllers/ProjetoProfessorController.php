<?php

namespace App\Http\Controllers;

use App\Models\ProjetoProfessor;
use Illuminate\Http\Request;
use DB;

class ProjetoProfessorController extends Controller
{
    public function store(Request $request)
    {
        $projcontador = new ProjetoProfessor();

        $projcontador->id_projeto = intval($request->id_projeto);
        $projcontador->id_professor = intval($request->id_elemento);
        $projcontador->anoParticipacao = intval($request->anoParticipacao);
        $projcontador->id_cargo = intval($request->cargo);

        $projcontador->save();
        
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
        $linha = DB::table('projeto_professor')
                    ->where([
                        ['projeto_professor.id_projeto', '=', $id_projeto],
                        ['projeto_professor.id_professor', '=', $id],
                        ['projeto_professor.anoParticipacao', '=', $ano]
                        ]);
        
        
        if($linha->first() != null) {
            $linha->delete(); 
        }
        return redirect()->route("gerirProjeto", ['id' => intval($id_projeto)]);
    }

    public function verificaAssociacao($id, $id_projeto, $ano)
    {
        $exite = false;
        $linha = DB::table('projeto_professor')
                    ->where([
                        ['projeto_professor.id_projeto', '=', $id_projeto],
                        ['projeto_professor.id_professor', '=', $id],
                        ['projeto_professor.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
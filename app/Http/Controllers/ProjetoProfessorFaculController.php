<?php

namespace App\Http\Controllers;

use App\Models\ProjetoProfessorFacul;
use Illuminate\Http\Request;
use DB;

class ProjetoProfessorFaculController extends Controller
{

    public function store(Request $request)
    {
        $projprofaculdade = new ProjetoProfessorFacul();

        $projprofaculdade->id_projeto = intval($request->id_projeto);
        $projprofaculdade->id_professorFaculdade = intval($request->id_elemento);
        $projprofaculdade->anoParticipacao = intval($request->anoParticipacao);

        $projprofaculdade->save();
        
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
        $linha = DB::table('projeto_prof_faculdade')
                    ->where([
                        ['projeto_prof_faculdade.id_projeto', '=', $id_projeto],
                        ['projeto_prof_faculdade.id_professorFaculdade', '=', $id],
                        ['projeto_prof_faculdade.anoParticipacao', '=', $ano]
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
        $linha = DB::table('projeto_prof_faculdade')
                    ->where([
                        ['projeto_prof_faculdade.id_projeto', '=', $id_projeto],
                        ['projeto_prof_faculdade.id_professorFaculdade', '=', $id],
                        ['projeto_prof_faculdade.anoParticipacao', '=', $ano]
                        ])
                    ->get();
        if(count($linha) > 0) {
            $exite = true;
        }

        return \json_encode($exite);
    }
}
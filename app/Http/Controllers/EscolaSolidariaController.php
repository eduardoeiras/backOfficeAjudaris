<?php

namespace App\Http\Controllers;

use App\Models\EscolaSolidaria;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\EscolaSolidariaProf;

class EscolaSolidariaController extends Controller
{
    public function index()
    {
        $escsolidarias = EscolaSolidaria::all();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/escolasSolidarias', ['data' => $escsolidarias]);
        }
        else {
            return view('colaborador/escolasSolidarias', ['data' => $escsolidarias]);
        }
    }

    public function store(Request $request)
    {
        $escsolidarias = new EscolaSolidaria();

        $escsolidarias->nome = $request->nome;
        $escsolidarias->telefone = $request->telefone;
        $escsolidarias->telemovel = $request->telemovel;
        $escsolidarias->contactoAssPais = $request->contactoAssPais;
        $escsolidarias->id_agrupamento = $request->agrupamento;
        $escsolidarias->disponivel = $request->disponibilidade;

        $escsolidarias->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("escolas");
        }
        else {
            return redirect()->route("escolasColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_escola = \intval($id);
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $contactoAssPais = $request->contactoAssPais;
        $id_agrupamento = $request->agrupamento;
        $disponibilidade = $request->disponibilidade;

        $escola = EscolaSolidaria::find($id_escola);
        if($escola != null) {
            $escola->nome = $nome;
            $escola->telefone = $telefone;
            $escola->telemovel = $telemovel;
            $escola->contactoAssPais = $contactoAssPais;
            $escola->id_agrupamento = $id_agrupamento;
            $escola->disponivel = $disponibilidade;

            $escola->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("escolas");
            }
            else {
                return redirect()->route("escolasColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $escola = EscolaSolidaria::find($id);
        if($escola->professores()->first() != null) {
            return redirect()->route("escolas");
        }
        if($escola->projetos()->first() != null) {
            $escola->projetos()->where('id_escolaSolidaria', $id)->delete();
        }
        $escola->delete();
        
        return redirect()->route("escolas");

    }

    public function getEscolaPorId($id) {
        
        $escola = DB::table('escola_solidaria')->where('id_escolaSolidaria', $id)->first();
        if($escola != null) {
            return response()->json($escola);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $ilustradores = DB::table('escola_solidaria')
                    ->select('escola_solidaria.id_escolaSolidaria', 'escola_solidaria.telefone', 'escola_solidaria.telemovel', 'escola_solidaria.nome')
                    ->where([
                        ['escola_solidaria.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($ilustradores);
    }

    public function gerirEscola($id) {
        $escola = EscolaSolidaria::find($id);

        \session(['id_escola' => $id]);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirProfessoresEscola', ['title' => 'Escola: '.$escola->nome]);
        }
        else {
            return view('colaborador/gerirProfessoresEscola', ['title' => 'Escola: '.$escola->nome]);
        }

    }

    public function getProfessores() {
        $id = intval(\session('id_escola'));
        
        $professores = DB::table('professor')
                        ->join('escola_professor', 'professor.id_professor', '=', 'escola_professor.id_professor')
                        ->select('professor.id_professor' , 'professor.nome', 'professor.telefone', 'professor.telemovel', 'professor.email')
                        ->where([
                            ['escola_professor.id_escola', '=', $id]
                            ])
                        ->get();


        return response()->json($professores);  
    }

    public function getNomeEscolaPorId($id) {
        $escola = EscolaSolidaria::find($id);
        $nome = null;

        if($escola != null) {
            $nome = $escola->nome;
        }

        return $nome;
    }

    public function associarProfessor(Request $request) {
        $novaAssoc = new EscolaSolidariaProf();

        $novaAssoc->id_escola = $request->id_escola;
        $novaAssoc->id_professor = $request->id_professor;

        $novaAssoc->save();

        $nomeEscola = self::getNomeEscolaPorId($request->id_escola);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirEscola", $request->id_escola);
        }
        else {
            return redirect()->route("gerirEscolaColaborador", $request->id_escola);
        }
    }

    public function deleteAssociacao($id_professor, $id_escola) {
        
        $query = DB::table('escola_professor')
                    ->where([
                        ['escola_professor.id_escola', '=', $id_escola],
                        ['escola_professor.id_professor', '=', $id_professor]
                        ]);

        $associacao = $query->first();

        if($associacao != null) {
            $query->delete();
        }

        return redirect()->route("gerirEscola", $id_escola);
    }
}
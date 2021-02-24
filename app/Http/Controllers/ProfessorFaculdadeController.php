<?php

namespace App\Http\Controllers;

use App\Models\ProfessorFaculdade;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class ProfessorFaculdadeController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $profacul = ProfessorFaculdade::all();
        if($user->tipoUtilizador == 0) {
            return view('admin\profs_faculdade', ['data' => $profacul]);
        }
        else {
            return view('colaborador\profs_faculdade', ['data' => $profacul]);
        }
        
    }

    public function store(Request $request)
    {
        $profacul = new ProfessorFaculdade();

        $profacul->cargo = $request->cargo;
        $profacul->nome = $request->nome;
        $profacul->telefone = $request->telefone;
        $profacul->telemovel = $request->telemovel;
        $profacul->email = $request->email;
        $profacul->observacoes = $request->observacoes;
        $profacul->disponivel = $request->disponibilidade;

        $profacul->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("profsFaculdade");
        }
        else {
            return redirect()->route("profsFaculdadeColaborador");
        }

    }

    public function update($id, Request $request)
    {
        $id_profacul = \intval($id);
        $cargo = $request->cargo;
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $email = $request->email;
        $observacoes = $request->observacoes;
        $disponibilidade = $request->disponibilidade;
        
        $prof = ProfessorFaculdade::find($id_profacul);
        if($prof != null) {
            $prof->cargo = $cargo;
            $prof->nome = $nome;
            $prof->telefone = $telefone;
            $prof->telemovel = $telemovel;
            $prof->email = $email;
            $prof->observacoes = $observacoes;
            $prof->disponivel = $disponibilidade;

            $prof->save();
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("profsFaculdade");
            }
            else {
                return redirect()->route("profsFaculdadeColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $profacul = ProfessorFaculdade::find($id);
        if($profacul->projetos()->first() != null) {
            $profacul->projetos()->where('id_professorFaculdade', $id)->delete();
        }
        if($profacul->universidades()->first() != null) {
            $profacul->universidades()->where('id_professorFaculdade', $id)->delete();
        } 
        $profacul->delete();
        

        return redirect()->route("profsFaculdade");
    }

    public function getProfPorId($id) {
        
        $profacul = DB::table('professor_faculdade')->where('id_professorFaculdade', $id)->first();
        if($profacul != null) {
            return response()->json($profacul);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $professores = DB::table('professor_faculdade')
                    ->select('professor_faculdade.id_professorFaculdade', 'professor_faculdade.telemovel', 'professor_faculdade.telefone', 'professor_faculdade.nome')
                    ->where([
                        ['professor_faculdade.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($professores);
    }

    public function existeAssociacao($id_professor, $id_universidade) {
        
        $professor = DB::table('professor_faculdade')
                    ->join('universidade_prof_faculdade', 'professor_faculdade.id_professorFaculdade', '=', 'universidade_prof_faculdade.id_professorFaculdade')
                    ->where([
                        ['universidade_prof_faculdade.id_professorFaculdade', '=', $id_professor],
                        ['universidade_prof_faculdade.id_universidade', '=', $id_universidade]
                        ])
                    ->first();

        if($professor != null) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getDisponiveisSemEscola($id_universidade) {
        $profsSemUniversidade = array();
        
        $profs = DB::table('professor_faculdade')
                    ->select('professor_faculdade.id_professorFaculdade', 'professor_faculdade.cargo', 
                    'professor_faculdade.telemovel', 'professor_faculdade.telefone', 'professor_faculdade.email', 'professor_faculdade.nome')
                    ->get();

        foreach($profs as $professor) {
            $existe = self::existeAssociacao($professor->id_professorFaculdade, $id_universidade);
            if($existe == false) {
                array_push($profsSemUniversidade, $professor);
            }
        }
        
        return \json_encode($profsSemUniversidade);
    }
}
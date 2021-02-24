<?php

namespace App\Http\Controllers;

use App\Models\Universidade;
use Illuminate\Http\Request;
use DB;

class UniversidadeController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $universidades = Universidade::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/universidades', ['data' => $universidades]);
        }
        else {
            return view('colaborador/universidades', ['data' => $universidades]);
        }
    }

    public function store(Request $request)
    {
        $curso = $request->get("curso");
        $tipo = $request->get("tipo");
        $nome = $request->get("nome");
        $telefone = $request->get("telefone");
        $telemovel = $request->get("telemovel");
        $email = $request->get("email");
        $disponibilidade = $request->get("disponibilidade");
        
        $universidade = new Universidade();
            
        $universidade->curso = $curso;
        $universidade->tipo = $tipo;
        $universidade->nome = $nome;
        $universidade->telefone = $telefone;
        $universidade->telemovel = $telemovel;
        $universidade->email = $email;
        $universidade->disponivel = $disponibilidade;
            
        $universidade->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("universidades");
        }
        else {
            return redirect()->route("universidadesColaborador");
        }
        
    }
    
    public function update($id, Request $request)
    {
        $id_universidade = \intval($id);
        $curso = $request->get("curso");
        $tipo = $request->get("tipo");
        $nome = $request->get("nome");
        $telefone = $request->get("telefone");
        $telemovel = $request->get("telemovel");
        $email = $request->get("email");
        $disponibilidade = $request->get("disponibilidade");
        
        $universidade = Universidade::find($id_universidade);
        if($universidade != null) {            
            $universidade->curso = $curso;
            $universidade->tipo = $tipo;
            $universidade->nome = $nome;
            $universidade->telefone = $telefone;
            $universidade->telemovel = $telemovel;
            $universidade->email = $email;
            $universidade->disponivel = $disponibilidade;
            
            $universidade->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("universidades");
            }
            else {
                return redirect()->route("universidadesColaborador");
            }
        }
    }
    
    public function destroy($id)
    {
       $universidade = Universidade::find($id);
       $universidade->delete();
       return redirect()->route("universidades"); 
    }

    public function getUniversidadePorId($id) {
        
        $uni = DB::table('universidade')->where('id_universidade', $id)->first();
        if($uni != null) {
            return response()->json($uni);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $juris = DB::table('universidade')
                    ->select('universidade.id_universidade', 'universidade.telemovel', 'universidade.telefone', 'universidade.nome')
                    ->where([
                        ['universidade.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($juris);
    }

    public function gerirProfessoresUniversidade($id) {
        $universidade = Universidade::find($id);

        \session(['id_universidade' => $id]);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirProfessoresUniversidade', ['title' => 'Universidade: <br><br>'.$universidade->nome.' - '.$universidade->tipo]);
        }
        else {
            return view('colaborador/gerirProfessoresUniversidade', ['title' => 'Universidade: <br><br>'.$universidade->nome.' - '.$universidade->tipo]);
        }
    }
}
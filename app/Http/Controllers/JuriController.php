<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use Illuminate\Http\Request;
use DB;
class JuriController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $juris = Juri::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/juris', ['data' => $juris]);
        }
        else {
            return view('colaborador/juris', ['data' => $juris]);
        }
    }

    public function store(Request $request)
    {
        $juris = new Juri();

        $juris->nome = $request->nome;
        $juris->email = $request->email;
        $juris->telefone = $request->telefone;
        $juris->telemovel = $request->telemovel;
        $juris->disponivel = $request->disponibilidade;

        $juris->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("juris");
        }
        else {
            return redirect()->route("jurisColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_juri = \intval($id);
        $disponivel = $request->disponibilidade;
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $email = $request->email;
        
        $juri = Juri::find($id_juri);
        if($juri != null) {
            $juri->disponivel = $disponivel;
            $juri->nome = $nome;
            $juri->telefone = $telefone;
            $juri->telemovel = $telemovel;
            $juri->email = $email;

            $juri->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("juris");
            }
            else {
                return redirect()->route("jurisColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $juri = Juri::find($id);
        if($juri->projetos()->first() != null) {
            $juri->projetos()->where('id_juri', $id)->delete();
        }
        $juri->delete();
        
        return redirect()->route("juris");

    }

    public function getJuriPorId($id) {
        
        $juri = DB::table('juri')->where('id_juri', $id)->first();
        if($juri != null) {
            return response()->json($juri);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $juris = DB::table('juri')
                    ->select('juri.id_juri', 'juri.telemovel', 'juri.telefone', 'juri.nome')
                    ->where([
                        ['juri.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($juris);
    }
}
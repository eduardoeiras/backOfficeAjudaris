<?php

namespace App\Http\Controllers;

use App\Models\RBE;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class RBEController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $rbes = RBE::all();
        if($user->tipoUtilizador == 0) {
            return view('admin\rbes', ['data' => $rbes]);
        }
        else {
            return view('colaborador\rbes', ['data' => $rbes]);
        }
    }

    public function store(Request $request)
    {
        $rbe = new RBE();

        $rbe->regiao = $request->regiao;
        $rbe->nomeCoordenador = $request->nome;
        $rbe->id_concelho = $request->concelhos;
        $rbe->disponivel = $request->disponibilidade;

        $rbe->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("rbes");
        }
        else {
            return redirect()->route("rbesColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_rbe = \intval($id);
        $regiao = $request->regiao;
        $nomeCoordenador = $request->nome;
        $id_concelho = $request->concelho;
        $disponivel = $request->disponibilidade;
        
        $rbe = RBE::find($id_rbe);
        if($rbe != null) {
            $rbe->regiao = $regiao;
            $rbe->nomeCoordenador = $nomeCoordenador;
            $rbe->id_concelho = $id_concelho;
            $rbe->disponivel = $disponivel;

            $rbe->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("rbes");
            }
            else {
                return redirect()->route("rbesColaborador");
            }
        }
    }


    public function destroy($id)
    {
        $rbe = RBE::find($id);
        if($rbe->projetos()->first() != null) {
            $rbe->projetos()->where('id_rbe', $id)->delete();
        }
        $rbe->delete();
        return redirect()->route("rbes");
    }

    public function getRbePorId($id) {
        
        $rbe = DB::table('rbe')->where('id_rbe', $id)->first();
        if($rbe != null) {
            return response()->json($rbe);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $professores = DB::table('rbe')
                    ->select('rbe.id_rbe', 'rbe.regiao', 'rbe.nomeCoordenador', 'concelho.nome')
                    ->join('concelho', 'rbe.id_concelho', '=', 'concelho.id_concelho')
                    ->where([
                        ['rbe.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($professores);
    }
}
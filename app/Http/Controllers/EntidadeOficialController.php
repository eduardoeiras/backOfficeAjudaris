<?php

namespace App\Http\Controllers;

use App\Models\EntidadeOficial;
use Illuminate\Http\Request;
use DB;
use Session;
class EntidadeOficialController extends Controller
{
    
    public function index()
    {
        $user = session()->get("utilizador");
        $entidades = EntidadeOficial::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/entidades', ['data' => $entidades]);
        }
        else {
            return view('colaborador/entidades', ['data' => $entidades]);
        }
    }

    public function store(Request $request)
    {
        $entOficial = new EntidadeOficial();

        $entOficial->nome = $request->nome;
        $entOficial->email = $request->email;
        $entOficial->entidade = $request->entidade;
        $entOficial->telefone = $request->telefone;
        $entOficial->telemovel = $request->telemovel;
        $entOficial->observacoes = $request->observacoes;

        $entOficial->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("entidades");
        }
        else {
            return redirect()->route("entidadesColaborador");
        }
    }

    public function update($id ,Request $request)
    {
        $id_entidadeOficial = \intval($id);
        $nome = $request->nome;
        $email = $request->email;
        $entidade = $request->entidade;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $observacoes = $request->observacoes;
        $disponivel = $request->disponibilidade;

        $entOficial = EntidadeOficial::find($id_entidadeOficial);
        if($entOficial != null){
            $entOficial->nome = $nome;
            $entOficial->email = $email;
            $entOficial->entidade = $entidade;
            $entOficial->telefone = $telefone;
            $entOficial->telemovel = $telemovel;
            $entOficial->disponivel = $disponivel;
            $entOficial->observacoes = $observacoes;

            $entOficial->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("entidades");
            }
            else {
                return redirect()->route("entidadesColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $entidades = EntidadeOficial::find($id);
        if($entidades->projetos()->first() != null) {
            $entidades->projetos()->where('id_entidadeOficial', $id)->delete();
        }
        $entidades->delete();
        
        return redirect()->route("entidades");

    }

    public function getEntidadePorId($id) {
        
        $entidades = DB::table('entidade_oficial')->where('id_entidadeOficial', $id)->first();
        if($entidades != null) {
            return response()->json($entidades);
        }
        else{
            return null;
        }
    }

    public function getDisponiveis() {
        $entidades = DB::table('entidade_oficial')
                    ->select('entidade_oficial.id_entidadeOficial', 'entidade_oficial.telefone', 'entidade_oficial.telemovel', 'entidade_oficial.email', 'entidade_oficial.nome')
                    ->where([
                        ['entidade_oficial.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($entidades);
    }
}
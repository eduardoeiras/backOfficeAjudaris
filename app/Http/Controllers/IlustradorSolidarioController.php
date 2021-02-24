<?php

namespace App\Http\Controllers;

use App\Models\IlustradorSolidario;
use Illuminate\Http\Request;
use DB;
use Session;
class IlustradorSolidarioController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $ilustradores = IlustradorSolidario::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/ilustradores', ['data' => $ilustradores]);
        }
        else {
            return view('colaborador/ilustradores', ['data' => $ilustradores]);
        }
    }

    public function store(Request $request)
    {
        $ilusolidario = new IlustradorSolidario();

        $ilusolidario->volumeLivro = $request->volumeLivro;
        $ilusolidario->disponivel = $request->disponibilidade;
        $ilusolidario->nome = $request->nome;
        $ilusolidario->telefone = $request->telefone;
        $ilusolidario->telemovel = $request->telemovel;
        $ilusolidario->email = $request->email;
        $ilusolidario->observacoes = $request->obs;

        $ilusolidario->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("ilustradores");
        }
        else {
            return redirect()->route("ilustradoresColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_ilustradorSolidario = \intval($id);
        $volumeLivro = $request->volumeLivro;
        $disponivel = $request->disponibilidade;
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $email = $request->email;
        $observacoes = $request->obs;
        
        $ilusolidario = IlustradorSolidario::find($id_ilustradorSolidario);
        if($ilusolidario != null) {
            $ilusolidario->volumeLivro = $volumeLivro;
            $ilusolidario->disponivel = $disponivel;
            $ilusolidario->nome = $nome;
            $ilusolidario->telefone = $telefone;
            $ilusolidario->telemovel = $telemovel;
            $ilusolidario->email = $email;
            $ilusolidario->observacoes = $observacoes; 

            $ilusolidario->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("ilustradores");
            }
            else {
                return redirect()->route("ilustradoresColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $ilustrador = IlustradorSolidario::find($id);
        if($ilustrador->projetos()->first() != null) {
            $ilustrador->projetos()->where('id_ilustradorSolidario', $id)->delete();
        }
        $ilustrador->delete();

        
        return redirect()->route("ilustradores");

    }
    
    public function getIlustradorPorId($id) {
        
        $ilustrador = DB::table('ilustrador_solidario')->where('id_ilustradorSolidario', $id)->first();
        if($ilustrador != null) {
            return response()->json($ilustrador);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
            $ilustradores = DB::table('ilustrador_solidario')
                        ->select('ilustrador_solidario.id_ilustradorSolidario', 'ilustrador_solidario.telemovel', 'ilustrador_solidario.telefone', 'ilustrador_solidario.nome')
                        ->where([
                            ['ilustrador_solidario.disponivel', '=', 0]
                            ])
                        ->get();  
        
        return \json_encode($ilustradores);
    }
}
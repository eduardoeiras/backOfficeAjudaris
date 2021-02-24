<?php

namespace App\Http\Controllers;

use App\Models\ContadorHistoria;
use Illuminate\Http\Request;
use DB;

class ContadorHistoriaController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $contadorHistorias = ContadorHistoria::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/contadores', ['data' => $contadorHistorias]);
        }
        else {
            return view('colaborador/contadores', ['data' => $contadorHistorias]);
        }
    }

    public function store(Request $request)
    {
        $contadorHistoria = new ContadorHistoria();

        $contadorHistoria->nome = $request->nome;
        $contadorHistoria->email = $request->email;
        $contadorHistoria->telefone = $request->telefone;
        $contadorHistoria->telemovel = $request->telemovel;
        $contadorHistoria->disponivel = $request->disponibilidade;
        $contadorHistoria->observacoes = $request->obs;

        $contadorHistoria->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("contadores");
        }
        else {
            return redirect()->route("contadoresColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_contadorHistorias = \intval($id);
        $disponivel = $request->disponibilidade;
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $email = $request->email;
        $observacoes = $request->obs;
        
        $contador = ContadorHistoria::find($id_contadorHistorias);
        if($contador != null) {
            $contador->disponivel = $disponivel;
            $contador->nome = $nome;
            $contador->telefone = $telefone;
            $contador->telemovel = $telemovel;
            $contador->email = $email;
            $contador->observacoes = $observacoes; 

            $contador->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("contadores");
            }
            else {
                return redirect()->route("contadoresColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $contador = ContadorHistoria::find($id);
        if($contador->projetos()->first() != null) {
            $contador->projetos()->where('id_contador', $id)->delete();
        }
        $contador->delete();

        return redirect()->route("contadores");
 
    }

    public function getContadorPorId($id) {
        
        $contador = DB::table('contador_historias')->where('id_contadorHistorias', $id)->first();
        if($contador != null) {
            return response()->json($contador);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $contadores = DB::table('contador_historias')
                    ->select('contador_historias.id_contadorHistorias', 'contador_historias.telemovel', 'contador_historias.telefone', 'contador_historias.email', 'contador_historias.nome')
                    ->where([
                        ['contador_historias.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($contadores);
    }
}
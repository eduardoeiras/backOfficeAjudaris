<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Comunicacao;
use Illuminate\Http\Request;
use DB;

class ComunicacoesController extends Controller
{
    public function index($id, $nome)
    {
        $user = session()->get("utilizador");
        $comunicacoes = DB::table('comunicacao')
            ->select('comunicacao.*')
            ->where('comunicacao.id_colaborador', '=', $id)
            ->get();
        
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirComunicacoes', ['data' => $comunicacoes, 'id_colaborador' => $id, 'nome' => $nome]);
        }
        else {
            return view('colaborador/gerirComunicacoes', ['data' => $comunicacoes, 'id_colaborador' => $id, 'nome' => $nome]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->data;
        $observacoes = $request->obs;
        $id_colaborador = $request->id_colaborador;
        $nome = $request->nome;

        $comunicacao = new Comunicacao();
        $comunicacao->data = $data;
        $comunicacao->observacoes = $observacoes;
        $comunicacao->id_colaborador = $id_colaborador;
        $comunicacao->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirComunicacoes", ['id' => $id_colaborador, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirComunicacoesColaborador", ['id' => $id_colaborador, 'nome' => $nome]);
        }
    }

    public function update($id, Request $request)
    {
        $id_comunicacao = \intval($id);
        $id_colaborador = \intval($request->id_colaborador);
        $observacoes = $request->obs;
        $nome = $request->nome;
        
        $comunicacao = Comunicacao::find($id_comunicacao);
        if($comunicacao != null) {
            $comunicacao->observacoes = $observacoes;
            $comunicacao->save();
        }
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirComunicacoes", ['id' => $id_colaborador, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirComunicacoesColaborador", ['id' => $id_colaborador, 'nome' => $nome]);
        }
        
    }

    public function destroy($id, Request $request)
    {
        $comunicacao = Comunicacao::find($id);
        if($comunicacao != null) {
            $comunicacao->id_colaborador = null;
            $comunicacao->delete();
        }

        return redirect()->route("gerirComunicacoes", ['id' => $request->id_colaborador, 'nome' => $request->nome]);
    }

    public function getPorId($id) {
        $comunicacao = Comunicacao::find($id);
        
        if($comunicacao != null) {
            return response()->json($comunicacao);  
        }
        else {
            return null;
        }
        
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class ConcelhoController extends Controller
{
    public function index()
    {
        
        $concelhos = Concelho::all();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
           return view('admin/concelhos', ['data' => $concelhos]);
        }
        else {
            return view('colaborador/concelhos', ['data' => $concelhos]);
        }
        
    }

    public function store(Request $request)
    {
        $concelho = new Concelho();

        $concelho->nome = $request->nome;

        $concelho->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
           return redirect()->route("concelhos");
        }
        else {
            return redirect()->route("concelhosColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_concelho = \intval($id);
        $nome = $request->nome;
        
        $concelho = Concelho::find($id_concelho);
        if($concelho != null) {
            $concelho->nome = $nome;

            $concelho->save();
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
            return redirect()->route("concelhos");
            }
            else {
                return redirect()->route("concelhosColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $concelho = Concelho::find($id);
        $concelho->delete(); 
        
        return redirect()->route("concelhos");
    }

    public function verificaRbes($id) {
        $concelho = Concelho::find($id);
        if($concelho->rbes()->first() != null) {
            $msg = 'O concelho, '.$concelho->nome.', possui Redes de Bibliotecas Escolares Associadas
            e não pode ser eliminado!';
            return $msg;
        }
        else {
            return null; 
        }
    }

    public function getAll()
    {
        $concelhos = Concelho::all();
        if($concelhos != null) {
            return response()->json($concelhos);
        }
        else {
            return null;
        }
    }

    public static function getNomePorId($id)
    {
        $concelho = DB::table('concelho')->where('id_concelho', $id)->first();
        if($concelho != null) {
            return $concelho->nome;
        }
        else {
            return null;
        }
    }

    public function getConcelhoPorId($id) {
        
        $concelho = DB::table('concelho')->where('id_concelho', $id)->first();
        if($concelho != null) {
            return response()->json($concelho);  
        }
        else {
            return null;
        }
        
    }

    public static function existeConcelho($concelho)
    {
        $concelho = DB::table('concelho')
                    ->where('concelho.nome', '=', "$concelho")
                    ->first();
        
        if($concelho != null) {
            return 1;     
        }
        else {
            return 0;
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\TrocaAgrupamento;
use Illuminate\Http\Request;
use DB;
use Session;
class TrocaAgrupamentoController extends Controller
{

    public function index()
    {
        $user = session()->get("utilizador");
        $trocas = TrocaAgrupamento::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/trocasAgrupamento', ['data' => $trocas]);
        }
        else{
            return view('colaborador/trocasAgrupamento', ['data' => $trocas]);
        }

        
    }
    
    public function store(Request $request)
    {
        $trocas = new TrocaAgrupamento();

        $trocas->agrupamentoAntigo = $request->agrupamentoAntigo;
        $trocas->novoAgrupamento = $request->novoAgrupamento;
        $trocas->observacoes = $request->observacoes;
        //$trocas->id_professor = $request->id_professor;

        $trocas->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("trocasAgrupamento");
        }
        else {
            return redirect()->route("trocasAgrupamentoColaborador");
        }
    }
    
    public function update($id, Request $request)
    {
        $id_troca = \intval($id);
        $agrupamentoAntigo = $request->agrupamentoAntigo;
        $novoAgrupamento = $request->novoAgrupamento;
        $observacoes = $request->obs;
        
        $troca = TrocaAgrupamento::find($id_troca);
        if($troca != null) {
            
            $troca->observacoes = $observacoes; 

            $troca->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("trocasAgrupamento");
            }
            else {
                return redirect()->route("trocasAgrupamentoColaborador");
            }
        }
    }
    
    public function destroy($id)
    {
        $troca = TrocaAgrupamento::find($id);
        if($troca->professor()->first() != null) {
            $troca->professor()->where('id_troca', $id)->delete();
        }
        $troca->delete();
        return redirect()->route("trocasAgrupamento");
    }
    
    public function getTrocaPorId($id) {
        
        $troca = DB::table('troca_agrupamento')->where('id_troca', $id)->first();
        if($troca != null) {
            return response()->json($troca);  
        }
        else {
            return null;
        }
        
    }
    
}
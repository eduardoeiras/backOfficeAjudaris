<?php

namespace App\Http\Controllers;

use App\Models\TrocaAgrupamento;
use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Colaborador;
use DB;
use Session;
use DataTables;

class TrocaAgrupamentoController extends Controller
{

    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/trocasAgrupamento');
        }
        else{
            return view('colaborador/trocasAgrupamento');
        }

        
    }
    
    public function store(Request $request)
    {
        $trocas = new TrocaAgrupamento();

        $trocas->agrupamentoAntigo = $request->agrupamentoAntigo;
        $trocas->novoAgrupamento = $request->novoAgrupamento;
        $trocas->observacoes = $request->observacoes;
        $trocas->id_professor = $request->id_professor;

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

    public function getAll() {

        $trocas = DB::table('troca_agrupamento')->select('troca_agrupamento.*');

        return Datatables::of($trocas)
        ->editColumn('nome', function ($model) {
            $professor = Professor::find($model->id_professor);
            $colaborador = Colaborador::find($professor->id_colaborador);
            return $colaborador->nome;
        })
            ->addColumn('opcoes', function($model) {
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_troca.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_troca.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_troca.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true); 

    }
    
}
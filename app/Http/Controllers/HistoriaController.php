<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use Illuminate\Http\Request;
use DB;
use DataTables;

class HistoriaController extends Controller
{
    public function index($id, $nome)
    {
        session()->put('nome', $nome);
        session()->put('id_escola', $id);
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirHistorias', ['nome' => $nome, 'id_escola' => $id]);
        }
        else {
            return view('colaborador/gerirHistorias', ['nome' => $nome, 'id_escola' => $id]);
        }
    }

    public function store(Request $request)
    {
        $historia = new Historia();
        $historia->ano = $request->anoHistoria;
        $historia->titulo = $request->titulo;
        if($request->urlFicheiro != '') {
            $historia->urlFicheiro = $request->urlFicheiro;    
        }
        $historia->id_escolaSolidaria = session()->get('id_escola');
        $historia->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirHistorias", ["id" => session()->get('id_escola'), "nome" => session()->get('nome')]);
        }
        else {
            return redirect()->route("gerirHistoriasColaborador", ["id" => session()->get('id_escola'), "nome" => session()->get('nome')]);
        }
    }

    public function update($id, Request $request)
    {
        $historia = Historia::find($id);
        $ano = $request->anoHistoria;
        $titulo = $request->titulo;
        $urlFicheiro = $request->urlFicheiro;
        var_dump($ano, $titulo, $urlFicheiro);
        if($historia != null) {
            $historia->ano = $ano;
            $historia->titulo = $titulo;
            $historia->urlFicheiro = $urlFicheiro; 

            $historia->save();
        }
        
        /*
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirHistorias", ["id" => session()->get('id_escola'), "nome" => session()->get('nome')]);
            }
        else {
             return redirect()->route("gerirHistoriasColaborador", ["id" => session()->get('id_escola'), "nome" => session()->get('nome')]);
        }*/
        
    }

    public function destroy($id)
    {
        $historia = Historia::find($id);
        if($historia != null) {
            if($historia->escola()->first() != null) {
                $historia->id_escolaSolidaria = null;
            }
            $historia->delete(); 
        }
        
        return redirect()->route("gerirHistorias", ["id" => session()->get('id_escola'), "nome" => session()->get('nome')]);

    }
    
    public function getPorId($id) {
        $historia = Historia::find($id);
        
        if($historia != null) {
            return response()->json($historia);  
        }
        else {
            return null;
        }
    }

    public function getAll($id_escola) {
        
        $historias = DB::table('historia')
        ->select('historia.*')
        ->where('id_escolaSolidaria', $id_escola)->orderBy('ano', 'DESC');

        return Datatables::of($historias)
            ->editColumn('ficheiro', function ($model) {
                if($model->urlFicheiro != '' || $model->urlFicheiro != null) {
                    $btn = '<a href="'.$model->urlFicheiro.'">Ver História</a>';  
                    return $btn;  
                }
                else {
                    return ' --- ';
                }
            })
            ->addColumn('opcoes', function($model){
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_historia.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_historia.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_historia.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>';
                }
                return $btns;
         })
            ->rawColumns(['ficheiro', 'opcoes'])
            ->make(true); 

    }
}
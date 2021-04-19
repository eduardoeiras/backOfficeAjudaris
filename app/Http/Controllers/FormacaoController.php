<?php

namespace App\Http\Controllers;

use App\Models\Formacao;
use Illuminate\Http\Request;
use DB;
use Session;
use DataTables;

class FormacaoController extends Controller
{
    
    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/formacoes');
        }
        else {
            return view('colaborador/formacoes');
        }
    }

    
    public function store(Request $request)
    {
        $formacao = new Formacao();

        $formacao->nomeInstituicao = $request->nomeInstituicao;
        $formacao->email = $request->email;

        $formacao->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("formacoes");
        }
        else {
            return redirect()->route("formacoesColaborador");
        }
    }

    
    public function update($id ,Request $request)
    {
        $id_formacao = \intval($id);
        $nomeInstituicao = $request->nomeInstituicao;
        $email = $request->email;

        $formacao = Formacao::find($id_formacao);
        if($formacao != null){
            $formacao->nomeInstituicao = $nomeInstituicao;
            $formacao->email = $email;

            $formacao->save();

            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("formacoes");
            }
            else {
                return redirect()->route("formacoesColaborador");
            }
        }
    }

    
    public function destroy($id)
    {
        $formacao = Formacao::find($id);
        
        $formacao->delete();
        
        return redirect()->route("formacoes");

    }

    public function getFormacaoPorId($id) {
        
        $formacao = DB::table('formacao')->where('id_formacao', $id)->first();
        if($formacao != null) {
            return response()->json($formacao);  
        }
        else {
            return null;
        }
        
    }

    public function getAll() {
        $formacoes = DB::table('formacao')
        ->select('formacao.*');

        return Datatables::of($formacoes)
            ->addColumn('opcoes', function($model){
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_formacao.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_formacao.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_formacao.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true);

    }
}
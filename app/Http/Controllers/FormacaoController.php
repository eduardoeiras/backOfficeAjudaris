<?php

namespace App\Http\Controllers;

use App\Models\Formacao;
use Illuminate\Http\Request;
use DB;
use Session;
class FormacaoController extends Controller
{
    
    public function index()
    {
        $user = session()->get("utilizador");
        $formacoes = Formacao::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/formacoes', ['data' => $formacoes]);
        }
        else {
            return view('colaborador/formacoes', ['data' => $formacoes]);
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
}
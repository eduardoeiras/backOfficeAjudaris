<?php

namespace App\Http\Controllers;

use App\Models\Formacao;
use Illuminate\Http\Request;
use DB;
use Session;
use SoulDoit\DataTable\SSP;

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
        $dt = [
            ['label'=>'Número identificador', 'db'=>'id_formacao', 'dt'=>0, 'formatter'=>function($value, $model){
                return $value;
            }],
            ['label'=>'Nome da Instituição', 'db'=>'nomeInstituicao', 'dt'=>1, 'formatter'=>function($value, $model){
                if($value != null) {
                    return $value;
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Email', 'db'=>'email', 'dt'=>2, 'formatter'=>function($value, $model){
                if($value != null) {
                    return $value;
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Opções', 'db'=>'id_formacao', 'dt'=>3, 'formatter'=>function($value, $model){ 
                $user = session()->get("utilizador");
                $url = 'gerirEscola'.$value;
                if($user->tipoUtilizador == 0) {
                    $btns = ['<td>
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>
                    </td>'];
                }
                else {
                    $btns = ['<td>
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    </td>'];
                }
                return implode(" ", $btns); 
            }],
        ];
        $dt_obj = new SSP('App\Models\Formacao', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
}
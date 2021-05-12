<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class FicheiroController extends Controller
{
    public function receberFicheiro(Request $request)
    {
        if($request->file('upload_file')->isValid()) {
            $nomeUnico = uniqid() . $request->file('upload_file')->getClientOriginalName();
            if(strlen($nomeUnico) < 164) {
                $request->file('upload_file')->move(public_path('ficheiros\\'), $nomeUnico);
                
                $urlParaFicheiro = 'http://backofficeajudaris/ficheiros/'.$nomeUnico;

                $resposta = [
                    'cod' => 200,
                    'msg' => 'Ficheiro guardado com êxito!',
                    'url' => $urlParaFicheiro
                ];    
            }
            else {
                $resposta = [
                    'cod' => 500,
                    'msg' => 'Nome do ficheiro original demasiado grande!\n
                    Por favor reduza o nome do ficheiro e tente novamente.',
                    'url' => ''
                ];
            }
        }
        else {
            $resposta = [
                'cod' => 500,
                'msg' => 'Erro no recebimento do ficheiro!',
                'url' => ''
            ];

        }
        return response()->json($resposta);
    }

    public function receberHistoria(Request $request)
    {
        if($request->file('upload_file')->isValid()) {
            $nomeUnico = uniqid() . $request->file('upload_file')->getClientOriginalName();
            if(strlen($nomeUnico) < 164) {
                $request->file('upload_file')->move(public_path('ficheiros\\historias'), $nomeUnico);
                
                $urlParaFicheiro = 'http://backofficeajudaris/ficheiros/historias/'.$nomeUnico;

                $resposta = [
                    'cod' => 200,
                    'msg' => 'Ficheiro guardado com êxito!',
                    'url' => $urlParaFicheiro
                ];    
            }
            else {
                $resposta = [
                    'cod' => 500,
                    'msg' => 'Nome do ficheiro original demasiado grande!\n
                    Por favor reduza o nome do ficheiro e tente novamente.',
                    'url' => ''
                ];
            }
        }
        else {
            $resposta = [
                'cod' => 500,
                'msg' => 'Erro no recebimento do ficheiro!',
                'url' => ''
            ];

        }
        return response()->json($resposta);
    }

    public function getPdf($id){
        //Obter projeto e caminho do pdf
        $projeto = DB::table('projeto')
                    ->select('projeto.regulamento')
                    ->where('projeto.id_projeto', $id)
                    ->first();

        if($projeto != null) {
            if($projeto->regulamento != null) {
                echo url($projeto->regulamento);
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }
}
<?php

namespace App\Http\Controllers;

use DB;

class ColaboradorController extends Controller
{
    public static function getEmails($id)
    {
        $emails = DB::table('email')
        ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->select('email.email', 'email.id_email')
        ->where('email.id_colaborador', '=', $id)
        ->get();
        
        if($emails != null) {
           return response()->json($emails); 
        }
        else {
            return null;
        }
    }

    public static function getLastId()
    {
        $id = DB::select('SELECT MAX(id_colaborador) as id_colaborador FROM colaborador');
        
        if($id != null) {
           return $id; 
        }
        else {
            return null;
        }
    }
    
}
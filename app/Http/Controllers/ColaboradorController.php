<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\CodPostalRua;
use DB;

class ColaboradorController extends Controller
{
    public static function getEmails($id)
    {
        $emails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email', 'email.id_email')
                    ->where('email.id_colaborador', '=', intval($id))
                    ->get();
        
        if($emails != null) {
           return response()->json($emails); 
        }
        else {
            return null;
        }
    }

    public static function existeEmail($email, $id_colaborador)
    {
        $email = DB::table('email')
                    ->where('email.email', '=', "$email")
                    ->where('email.id_colaborador', '=', $id_colaborador)
                    ->first();
        
        if($email != null) {
           return true; 
        }
        else {
            return false;
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

    public static function updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito) {
        if($cod_postal != null) {
            $cod_postal->localidade = $localidade;
            $cod_postal->distrito = $distrito;
            $cod_postal->save();
            $colaborador->codPostal = $codPostal; 
        }
        else {
            $novoCodPostal = new CodPostal();
            $novoCodPostal->codPostal = $codPostal;
            $novoCodPostal->localidade = $localidade;
            $novoCodPostal->save();
            $colaborador->codPostal = $codPostal;
        }
    }

    public static function updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua) {
        if($cod_postal_rua->first() != null) {
            $cod_postal_rua->rua = $rua;
            $cod_postal_rua->save();
            $colaborador->codPostalRua = $codPostalRua;
        }
        else {
            $novoCodPostalRua = new CodPostalRua();
            $novoCodPostalRua->codPostal = $codPostal;
            $novoCodPostalRua->codPostalRua = $codPostalRua;
            $novoCodPostalRua->rua = $rua;
            $novoCodPostalRua->save();
            $colaborador->codPostalRua = $codPostalRua;
        }
    }
    
}
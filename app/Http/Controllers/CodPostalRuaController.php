<?php

namespace App\Http\Controllers;

use DB;
class CodPostalRuaController extends Controller
{
    public static function getRua($codPostalRua){
        $cod_postalRua = DB::table('cod_postal_rua')->where('codPostalRua', $codPostalRua)->first();
        if($cod_postalRua != null){
            return $cod_postalRua->rua;
        }
        else{
            return null;
        }
    }

    public static function getNumPortaRua($codPostalRua){
        $cod_postalRua = DB::table('cod_postal_rua')->where('codPostalRua', $codPostalRua)->first();
        if($cod_postalRua != null){
            return $cod_postalRua->numPorta;
        }
        else{
            return null;
        }
    }
}
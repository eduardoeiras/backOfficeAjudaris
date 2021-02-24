<?php

namespace App\Http\Controllers;

use App\Models\CodPostalRua;
use Illuminate\Http\Request;
use DB;
class CodPostalRuaController extends Controller
{
    public function store(Request $request)
    {
        $codPostalRua = new CodPostalRua();

        $codPostalRua->codPostalRua = $request->codPostalRua;
        $codPostalRua->codPostal = $request->codPostal;
        $codPostalRua->rua = $request->rua;
        $codPostalRua->numPorta = $request->numPorta;

        $codPostalRua->save();
    }
    
    public function update(codPostalRua $cod_postalRua, Request $request)
    {
        $codPostalRua = $cod_postalRua;
        $rua = $request->rua;
        $numPorta = $request->numPorta;
    }

    public function destroy(codPostalRua $cod_postalRua)
    {
        $codPostalRua = CodPostalRua::find($cod_postalRua);
        if($codPostalRua->codPostal()->first() != null){
            $codPostalRua->codPostal()->where('codPostalRua', $cod_postalRua)->delete();
        }
    }

    public static function getCodPostalRua($codPostalRua){
        $cod_postalRua = DB::table('cod_postal_rua')->where('codPostalRua', $codPostalRua)->first();
        if($cod_postalRua != null){
            return $cod_postalRua->codPostalRua;
        }
        else{
            return null;
        }
    }

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
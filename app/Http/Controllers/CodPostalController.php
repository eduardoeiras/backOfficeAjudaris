<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use Illuminate\Http\Request;
use DB;
class CodPostalController extends Controller
{

    public function getAll() {

        $codPostal = Agrupamento::all();
        
        if($codPostal != null) {
            return  response()->json($codPostal);
        }
        else {
            return null;
        }
        
    }

    public static function getLocalidade($codPostal){
        $cod_postal = DB::table('cod_postal')->where('codPostal', $codPostal)->first();
        if($cod_postal != null){
            return $cod_postal->localidade;
        }
        else{
            return null;
        }
    }
}
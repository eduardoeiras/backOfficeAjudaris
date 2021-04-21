<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use App\Models\Rbe_concelho;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class ConcelhoController extends Controller
{
    public function index()
    {
        
        $concelhos = Concelho::all();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
           return view('admin/concelhos', ['data' => $concelhos]);
        }
        else {
            return view('colaborador/concelhos', ['data' => $concelhos]);
        }
        
    }

    public function store(Request $request)
    {
        $concelho = new Concelho();

        $concelho->nome = $request->nome;

        $concelho->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
           return redirect()->route("concelhos");
        }
        else {
            return redirect()->route("concelhosColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_concelho = \intval($id);
        $nome = $request->nome;
        
        $concelho = Concelho::find($id_concelho);
        if($concelho != null) {
            $concelho->nome = $nome;

            $concelho->save();
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
            return redirect()->route("concelhos");
            }
            else {
                return redirect()->route("concelhosColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $concelho = Concelho::find($id);
        $concelho->delete(); 
        
        return redirect()->route("concelhos");
    }

    public static function verificaAssociacao($id_concelho, $id_rbe) {
        $associacao = DB::table('Rbe_concelho')
                        ->where([
                            ['Rbe_concelho.id_concelho', '=', $id_concelho],
                            ['Rbe_concelho.id_rbe', '=', $id_rbe]])
                        ->first(); 

        if($associacao != null) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function criaAssociaConcelhos($concelhos, $id_rbe)
    {
        if($concelhos != null) {
            foreach($concelhos as $concelho) {

                $concelhoBD = DB::table('concelho')
                            ->where('concelho.nome', '=', $concelho)
                            ->first(); 

                $id_concelho = null;
                            
                if($concelhoBD == null) {
                    $con = new Concelho();
                    $con->nome = $concelho;
                    $con->save(); 
                    $id_concelho = self::getLastId();  
                }
                else {
                    $id_concelho = $concelhoBD->id_concelho;
                }
                
                if(!self::verificaAssociacao($id_concelho, $id_rbe)) {
                    $associacao = new Rbe_concelho();
                    $associacao->id_rbe = $id_rbe;
                    $associacao->id_concelho = $id_concelho;
                    $associacao->save();    
                }
            }    
        }
        
    }

    public static function removeAssociaConcelhos($concelhos, $id_rbe)
    {
        foreach($concelhos as $concelho) {

            $con = DB::table('concelho')
                        ->where('concelho.nome', '=', $concelho)
                        ->first(); 

            if($con != null) {
                if(self::verificaAssociacao($con->id_concelho, $id_rbe)) {
                    $assoc = DB::table('rbe_concelho')
                    ->where([['rbe_concelho.id_concelho', '=', $con->id_concelho], ['rbe_concelho.id_rbe', '=', $id_rbe]]);

                    if($assoc->first() != null) {
                        $assoc->delete(); 
                    }
                }    
            }
        }
    }

    public static function getLastId()
    {
        $id = DB::select('SELECT MAX(id_concelho) as id_concelho FROM concelho')[0]->id_concelho;
        
        if($id != null) {
           return $id; 
        }
        else {
            return null;
        }
    }
}
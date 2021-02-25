<?php

namespace App\Http\Controllers;

use App\Models\Agrupamento;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use DB;

class AgrupamentoController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $agrupamentos = Agrupamento::all();
        if($user->tipoUtilizador == 0) {
            return view('admin/agrupamentos', ['data' => $agrupamentos]);
        }
        else {
            return view('colaborador/agrupamentos', ['data' => $agrupamentos]);
        }
    }

    public function store(Request $request)
    {
        $agrupamento = new Agrupamento();
        
        $agrupamento->nome = $request->nome;
        $agrupamento->telefone = $request->telefone;
        $agrupamento->email = $request->email;
        $agrupamento->nomeDiretor = $request->nomeDiretor;
        $agrupamento->numPorta = $request->numPorta;

        $codPostal = $request->codPostal;
        $codPostalRua = $request->codPostalRua;
        $rua = $request->rua;
        $localidade = $request->localidade;
        
        $cod_postal = CodPostal::find($codPostal);
        $cod_postal_rua = DB::table('cod_postal_rua')
                                    ->where([
                                        ['cod_postal_rua.codPostal', '=', $codPostal],
                                        ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                        ]);

        if($cod_postal != null) {
            $agrupamento->codPostal = $codPostal; 
        }
        else {
            $novoCodPostal = new CodPostal();
            $novoCodPostal->codPostal = $codPostal;
            $novoCodPostal->localidade = $localidade;
            $novoCodPostal->save();
            $agrupamento->codPostal = $codPostal;
        }
        if($cod_postal_rua->first() != null) {
            $agrupamento->codPostalRua = $codPostalRua;
        }
        else {
            $novoCodPostalRua = new CodPostalRua();
            $novoCodPostalRua->codPostal = $codPostal;
            $novoCodPostalRua->codPostalRua = $codPostalRua;
            $novoCodPostalRua->rua = $rua;
            $novoCodPostalRua->save();
            $agrupamento->codPostalRua = $codPostalRua;
        }
        $agrupamento->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("agrupamentos");
        }
        else {
            return redirect()->route("agrupamentosColaborador");
        }
    }
    
    public function update($id ,Request $request)
    {
        $id_agrupamento = \intval($id);
        $nome = $request->nome;
        $telefone = $request->telefone;
        $email = $request->email;
        $nomeDiretor = $request->nomeDiretor;
        $codPostal = $request->codPostal;
        $localidade = $request->localidade;
        $codPostalRua = $request->codPostalRua;
        $numPorta = $request->numPorta;
        $rua = $request->rua;

        $agrupamento = Agrupamento::find($id_agrupamento);
        $cod_postal = CodPostal::find($codPostal);
        $cod_postal_rua = DB::table('cod_postal_rua')
                                    ->where([
                                        ['cod_postal_rua.codPostal', '=', $codPostal],
                                        ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                        ]);
        if($agrupamento != null) {
            $agrupamento->nome = $nome;
            $agrupamento->telefone = $telefone;
            $agrupamento->email = $email;
            $agrupamento->nomeDiretor = $nomeDiretor;
            $agrupamento->numPorta = $numPorta;
            if($cod_postal != null) {
                $agrupamento->codPostal = $codPostal; 
            }
            else {
                $novoCodPostal = new CodPostal();
                $novoCodPostal->codPostal = $codPostal;
                $novoCodPostal->localidade = $localidade;
                $novoCodPostal->save();
                $agrupamento->codPostal = $codPostal;
            }
            if($cod_postal_rua->first() != null) {
                $agrupamento->codPostalRua = $codPostalRua;
            }
            else {
                $novoCodPostalRua = new CodPostalRua();
                $novoCodPostalRua->codPostal = $codPostal;
                $novoCodPostalRua->codPostalRua = $codPostalRua;
                $novoCodPostalRua->rua = $rua;
                $novoCodPostalRua->save();
                $agrupamento->codPostalRua = $codPostalRua;
            }
            $agrupamento->save();
        }
            
       
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("agrupamentos");
        }
        else {
            return redirect()->route("agrupamentosColaborador");
        }
    }
    
    public function destroy($id)
    {
        $agrupamento = Agrupamento::find($id);
        if($agrupamento->codPostal != null){
            $agrupamento->codPostal = null;
        }
        if($agrupamento->codPostalRua != null){
            $agrupamento->codPostalRua = null;
            
        }
        $agrupamento->save();
        if($agrupamento->escolas()->first() != null){
            $agrupamento->escolas()->where('id_agrupamento', $id)->delete();
        }
        if($agrupamento->professores()->first() != null){
            $agrupamento->professores()->where('id_agrupamento', $id)->delete();
        }
        $agrupamento->delete();

        return redirect()->route("agrupamentos");

    }

    public static function getNomeAgrupamentoPorId($id) {
        
        $agrupamento = DB::table('agrupamento')->where('id_agrupamento', $id)->first();
        if($agrupamento != null) {
            return $agrupamento->nome;  
        }
        else {
            return null;
        }
        
    }

    public function getAgrupamentoPorId($id) {
        
        $agrupamento = DB::table('agrupamento')
                    ->join('cod_postal', 'agrupamento.codPostal', '=' , 'cod_postal.codPostal')
                    ->join('cod_postal_rua', 'agrupamento.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
                    ->select('agrupamento.*', 'cod_postal.localidade', 'cod_postal_rua.rua')
                    ->first();
                      
        if($agrupamento != null) {
            return response()->json($agrupamento);  
        }
        else {
            return null;
        }
        
    }

    public function getAll() {

        $agrupamentos = Agrupamento::all();
        
        if($agrupamentos != null) {
            return  response()->json($agrupamentos);
        }
        else {
            return null;
        }
        
    }

    public function getAllComLocalidade() {
        $agrupamentos = DB::table('agrupamento')
                ->join('cod_postal', 'agrupamento.codPostal', '=', 'cod_postal.codPostal')
                ->select('agrupamento.id_agrupamento', 'agrupamento.nome' , 'agrupamento.telefone', 'agrupamento.telefone',
                 'agrupamento.email', 'agrupamento.nomeDiretor', 'agrupamento.codPostal', 'agrupamento.codPostalRua',
                 'agrupamento.numPorta', 'cod_postal.localidade')
                ->get();
        
        if($agrupamentos != null) {
            return  response()->json($agrupamentos);
        }
        else {
            return null;
        }
    }
    
}
<?php

namespace App\Http\Controllers;

use App\Models\Agrupamento;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use DB;

class AgrupamentoController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $agrupamentos = DB::table(DB::raw('agrupamento', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'agrupamento.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('agrupamento.id_agrupamento', 'agrupamento.nomeDiretor', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($agrupamentos as $agrup) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email', 'email.id_email')
            ->where('email.id_colaborador', '=', $agrup->id_colaborador)
            ->get();
            
            $agrupamento = array(
                "agrupamento" => $agrup,
                "emails" => $emails
            );
            array_push($resposta, $agrupamento);
        }
        
        
        if($user->tipoUtilizador == 0) {
            return view('admin/agrupamentos', ['data' => $resposta]);
        }
        else {
            return view('colaborador/agrupamentos', ['data' => $resposta]);
        }
    }

    public function store(Request $request)
    {
        $colaborador = new Colaborador();

        $colaborador->nome = $request->nome;
        $colaborador->telefone = $request->telefone;
        $colaborador->telemovel = $request->telemovel;
        $colaborador->numPorta = $request->numPorta;
        $colaborador->disponivel = $request->disponibilidade;

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
            $colaborador->codPostal = $codPostal; 
        }
        else {
            $novoCodPostal = new CodPostal();
            $novoCodPostal->codPostal = $codPostal;
            $novoCodPostal->localidade = $localidade;
            $novoCodPostal->save();
            $colaborador->codPostal = $codPostal;
        }
        if($cod_postal_rua->first() != null) {
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

        $colaborador->save();

        $idColab = ColaboradorController::getLastId()[0]->id_colaborador;
        
        $email = new Email();
        $email->email = $request->email;
        $email->id_colaborador = $idColab;
        $email->save();

        $agrupamento = new Agrupamento();
        $agrupamento->nomeDiretor = $request->nomeDiretor;
        $agrupamento->id_colaborador = $idColab;
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
        //$emails = $request->emails;
        $nomeDiretor = $request->nomeDiretor;
        $codPostal = $request->codPostal;
        $localidade = $request->localidade;
        $codPostalRua = $request->codPostalRua;
        $numPorta = $request->numPorta;
        $rua = $request->rua;

        $agrupamento = Agrupamento::find($id_agrupamento);
        $colaborador = Colaborador::find($agrupamento->id_colaborador);

        $cod_postal = CodPostal::find($codPostal);
        $cod_postal_rua = DB::table('cod_postal_rua')
                                    ->where([
                                        ['cod_postal_rua.codPostal', '=', $codPostal],
                                        ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                        ]);
        
        if($agrupamento != null && $colaborador != null) {
            $colaborador->nome = $nome;
            $colaborador->telefone = $telefone;
            //$agrupamento->email = $email;
            $agrupamento->nomeDiretor = $nomeDiretor;
            $colaborador->numPorta = $numPorta;
            if($cod_postal != null) {
                $colaborador->codPostal = $codPostal; 
            }
            else {
                $novoCodPostal = new CodPostal();
                $novoCodPostal->codPostal = $codPostal;
                $novoCodPostal->localidade = $localidade;
                $novoCodPostal->save();
                $colaborador->codPostal = $codPostal;
            }
            if($cod_postal_rua->first() != null) {
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
            $colaborador->save();
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
        $colaborador = Colaborador::find($agrupamento->id_colaborador);
        if($colaborador->codPostal != null){
            $colaborador->codPostal = null;
        }
        if($colaborador->codPostalRua != null){
            $colaborador->codPostalRua = null;    
        }
        $colaborador->save();
        if($colaborador->comunicacoes()->first() != null) {
            $colaborador->comunicacoes()->where('id_colaborador', $agrupamento->id_colaborador)->delete();
        }
        if($colaborador->emails()->first() != null) {
            $colaborador->emails()->where('id_colaborador', $agrupamento->id_colaborador)->delete();
        }
        if($agrupamento->escolas()->first() != null) {
            $agrupamento->escolas()->where('id_agrupamento', $id)->delete();
        }
        if($agrupamento->professores()->first() != null) {
            $agrupamento->professores()->where('id_agrupamento', $id)->delete();
        }
        $agrupamento->delete();
        $colaborador->delete();

        return redirect()->route("agrupamentos");
    }

    public static function getNomeAgrupamentoPorId($id) {
        
        $agrupamento = DB::table('colaborador')
            ->join('colaborador', 'agrupamento.id_colaborador', 'colaborador.id_colaborador')
            ->where('id_agrupamento', $id)->first();
        if($agrupamento != null) {
            return $agrupamento->nome;  
        }
        else {
            return null;
        }
        
    }

    public function getAgrupamentoPorId($id) {
        
        $agrup = Agrupamento::find($id);
        $colaborador = Colaborador::find($agrup->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);
                    
        $agrupamento = array(
            "id_agrupamento" => $agrup->id_agrupamento,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "nomeDiretor" => $agrup->nomeDiretor,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $agrupamento);
          
        if($agrupamento != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
    }

    /*
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
    }*/
    
}
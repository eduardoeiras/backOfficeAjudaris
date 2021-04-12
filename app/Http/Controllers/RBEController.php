<?php

namespace App\Http\Controllers;

use App\Models\RBE;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Concelho;
use App\Models\Rbe_concelho;
use DB;
use Session;
use Auth;

class RBEController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $rbes = DB::table(DB::raw('rbe', 'colaborador', 'cod_postal', 'cod_postal_rua', 'rbe_concelho'))
        ->join('colaborador', 'rbe.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->join('rbe_concelho', 'rbe_concelho.id_rbe', '=', 'rbe.id_rbe')
        ->select('rbe.id_rbe', 'rbe.regiao', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($rbes as $rbe) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $rbe->id_colaborador)
            ->get();

            $concelho = DB::table('concelho')
            ->join('rbe_concelho', 'rbe_concelho.id_concelho', '=', 'concelho.id_concelho')
            ->select('concelho.nome')
            ->where('rbe_concelho.id_rbe', '=', $rbe->id_rbe)
            ->get();
            
            $rbibe = array(
                "entidade" => $rbe,
                "emails" => $emails,
                "concelhos" => $concelho
            );
            array_push($resposta, $rbibe);
        }

        if($user->tipoUtilizador == 0) {
            return view('admin\rbes', ['data' => $resposta]);
        }
        else {
            return view('colaborador\rbes', ['data' => $resposta]);
        }
    }

    public function store(Request $request)
    {
        //Obtenção dos atributos de um colaborador
        $nome = $request->nome;
        $observacoes = $request->observacoes;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $codPostal = $request->codPostal;
        $localidade = $request->localidade;
        $codPostalRua = $request->codPostalRua;
        $numPorta = $request->numPorta;
        $rua = $request->rua;
        $distrito = $request->distrito;
        $disponibilidade = $request->disponibilidade;
        $emails = $request->emails;
        
        $concelhos = $request->concelhos;
        
        //Obtenção do atributo do agrupamento
        $regiao = $request->regiao;
        
        //Obtenção do id do colaborador criado
        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);

        $rbe = new RBE();
        $rbe->regiao = $regiao;
        $rbe->id_colaborador = $idColab;
        $rbe->save();
        
        $id_rbe = self::getLastId();
        
        ConcelhoController::criaAssociaConcelhos($concelhos, $id_rbe);
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("rbes");
        }
        else {
            return redirect()->route("rbesColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_rbe = \intval($id);
        $nome = $request->nome;
        $observacoes = $request->observacoes;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $codPostal = $request->codPostal;
        $disponibilidade = $request->disponibilidade;
        $localidade = $request->localidade;
        $codPostalRua = $request->codPostalRua;
        $numPorta = $request->numPorta;
        $rua = $request->rua;
        $distrito = $request->distrito;
        $emails = $request->emails;
        $emailsToDelete = $request->deletedEmails;

        $concelhos = $request->concelhos;
        $concelhosToDelete = $request->deletedConcelhos;

        $regiao = $request->regiao;
        
        $rbe = RBE::find($id_rbe);
        if($rbe != null) {
            ColaboradorController::update($rbe->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);

            ConcelhoController::criaAssociaConcelhos($concelhos, $id_rbe);
            
            $rbe->regiao = $regiao;
            $rbe->save();
        }    
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("rbes");
        }
        else {
            return redirect()->route("rbesColaborador");
        }
        
    }


    public function destroy($id)
    {
        $rbe = RBE::find($id);
        if($rbe != null) {
            $idColaborador = $rbe->id_colaborador;
            if($rbe->projetos()->first() != null) {
                $rbe->projetos()->where('id_rbe', $id)->delete();
            }
            $rbe->delete();
            ColaboradorController::delete($idColaborador);
        }
        return redirect()->route("rbes");
    }

    public function getRbePorId($id) {
        
        $rbe = RBE::find($id);
        $colaborador = Colaborador::find($rbe->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $rbe = array(
            "id_rbe" => $rbe->id_rbe,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "regiao" => $rbe->regiao,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $rbe);

        if($rbe != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $rbes = DB::table('rbe')
                    ->join('colaborador', 'rbe.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('rbe.id_rbe as id', 'rbe.regiao', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();
                    
        $resposta = array();
        foreach($rbes as $entidade) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $entidade->id_colaborador)
            ->get();
                        
            $ent = array(
                "entidade" => $entidade,
                "emails" => $emails
            );
            array_push($resposta, $ent);
        }   

    
        return \json_encode($resposta);
    }

    public static function getLastId()
    {
        $id = DB::select('SELECT MAX(id_rbe) as id_rbe FROM rbe')[0]->id_rbe;
        
        if($id != null) {
           return $id; 
        }
        else {
            return null;
        }
    }
}
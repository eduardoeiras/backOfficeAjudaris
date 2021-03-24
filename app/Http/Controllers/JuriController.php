<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use Illuminate\Http\Request;
use App\Models\Colaborador;
use \App\Models\CodPostal;
use DB;
class JuriController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $juris = DB::table(DB::raw('juri', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'juri.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('juri.id_juri', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($juris as $jur){
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $jur->id_colaborador)
            ->get();
            
            $juri = array(
                "entidade" => $jur,
                "emails" => $emails
            );
            array_push($resposta, $juri);
        }
        
        if($user->tipoUtilizador == 0) {
            return view('admin/juris', ['data' => $resposta]);
        }
        else {
            return view('colaborador/juris', ['data' => $resposta]);
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

        //Obtenção do id do colaborador criado
        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);

        $juris = new Juri();
        $juris->id_colaborador = $idColab;
        $juris->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("juris");
        }
        else {
            return redirect()->route("jurisColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_juri = \intval($id);
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
        
        $juri = Juri::find($id_juri);
        if($juri != null) {
            ColaboradorController::update($juri->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);

            $juri->save();
        }
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("juris");
            }
            else {
                return redirect()->route("jurisColaborador");
            }
        
    }

    public function destroy($id)
    {
        $juri = Juri::find($id);
        if($juri != null) {
            $idColaborador = $juri->id_colaborador;
            if($juri->projetos()->first() != null) {
                $juri->projetos()->where('id_juri', $id)->delete();
            }
            $juri->delete();
            ColaboradorController::delete($idColaborador);
        }
        
        return redirect()->route("juris");

    }

    public function getJuriPorId($id) {

        $jur = Juri::find($id);
        $colaborador = Colaborador::find($jur->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();

        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);        
        
        $juri = array(
            "id_juri" => $jur->id_juri,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $juri);
        
        if($juri != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $entidades = DB::table('juri')
                    ->join('colaborador', 'juri.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('juri.id_juri', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.email', 'colaborador.nome')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($entidades);
    }
}
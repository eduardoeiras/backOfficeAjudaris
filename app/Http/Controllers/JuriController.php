<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use Illuminate\Http\Request;
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
            return view('admin/juris', ['data' => $juris]);
        }
        else {
            return view('colaborador/juris', ['data' => $juris]);
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
        $disponivel = $request->disponibilidade;
        $nome = $request->nome;
        $telefone = $request->telefone;
        $telemovel = $request->telemovel;
        $email = $request->email;
        
        $juri = Juri::find($id_juri);
        if($juri != null) {
            $juri->disponivel = $disponivel;
            $juri->nome = $nome;
            $juri->telefone = $telefone;
            $juri->telemovel = $telemovel;
            $juri->email = $email;

            $juri->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("juris");
            }
            else {
                return redirect()->route("jurisColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $juri = Juri::find($id);
        if($juri->projetos()->first() != null) {
            $juri->projetos()->where('id_juri', $id)->delete();
        }
        $juri->delete();
        
        return redirect()->route("juris");

    }

    public function getJuriPorId($id) {
        
        $juri = DB::table('juri')->where('id_juri', $id)->first();
        if($juri != null) {
            return response()->json($juri);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $juris = DB::table('juri')
                    ->select('juri.id_juri', 'juri.telemovel', 'juri.telefone', 'juri.nome')
                    ->where([
                        ['juri.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($juris);
    }
}
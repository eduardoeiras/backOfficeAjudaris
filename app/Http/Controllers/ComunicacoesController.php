<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Comunicacao;
use Illuminate\Http\Request;
use DB;

class ComunicacoesController extends Controller
{
    public function index($id, $nome)
    {
        $user = session()->get("utilizador");
        $comunicacoes = DB::table('comunicacao')
            ->select('comunicacao.*')
            ->where('comunicacao.id_colaborador', '=', $id)
            ->get();
        session("id_colaborador", $id);
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirComunicacoes', ['data' => $comunicacoes, 'id_colaborador' => $id, 'nome' => $nome]);
        }
        else {
            return view('colaborador/gerirComunicacoes', ['data' => $comunicacoes, 'id_colaborador' => $id, 'nome' => $nome]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->data;
        $observacoes = $request->obs;
        $id_colaborador = $request->id_colaborador;
        $nome = $request->nome;
        
        $comunicacao = new Comunicacao();
        $comunicacao->data = $data;
        $comunicacao->observacoes = $observacoes;
        $comunicacao->id_colaborador = $id_colaborador;
        $comunicacao->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirComunicacoes", ['id' => $id_colaborador, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirComunicacoesColaborador", ['id' => $id_colaborador, 'nome' => $nome]);
        }
    }

    public function update($id, Request $request)
    {
        $id_contadorHistorias = \intval($id);
        $nome = $request->nome;
        $observacoes = $request->obs;
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
        
        $contador = ContadorHistoria::find($id_contadorHistorias);
        if($contador != null) {
            ColaboradorController::update($contador->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
        } 
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("contadores");
        }
        else {
            return redirect()->route("contadoresColaborador");
        }
    }

    public function destroy($id, Request $request)
    {
        $comunicacao = Comunicacao::find($id);
        if($comunicacao != null) {
            $comunicacao->id_colaborador = null;
            $comunicacao->delete();
        }

        return redirect()->route("gerirComunicacoes", ['id' => $request->id_colaborador, 'nome' => $request->nome]);
    }

    public function getContadorPorId($id) {
        $contador = ContadorHistoria::find($id);
        $colaborador = Colaborador::find($contador->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $contador = array(
            "id_contadorHistorias" => $contador->id_contadorHistorias,
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
        array_push($resposta, $contador);

        if($contador != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $contadores = DB::table('contador_historias')
                    ->join('colaborador', 'contador_historias.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('contador_historias.id_contadorHistorias as id', 'colaborador.telemovel', 'colaborador.telefone', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();
                    
        $resposta = array();

        foreach($contadores as $entidade) {
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
}
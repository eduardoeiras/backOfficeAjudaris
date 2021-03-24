<?php

namespace App\Http\Controllers;

use App\Models\Universidade;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use DB;

class UniversidadeController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $universidades = DB::table(DB::raw('universidade', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'universidade.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('universidade.id_universidade', 'universidade.curso', 'universidade.tipo', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($universidades as $unis) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $unis->id_colaborador)
            ->get();
            
            $universidade = array(
                "entidade" => $unis,
                "emails" => $emails
            );
            array_push($resposta, $universidade);
        }

        if($user->tipoUtilizador == 0) {
            return view('admin/universidades', ['data' => $resposta]);
        }
        else {
            return view('colaborador/universidades', ['data' => $resposta]);
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

        $curso = $request->curso;
        $tipo = $request->tipo;

        //Obtenção do id do colaborador criado
        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        $universidade = new Universidade();    
        $universidade->curso = $curso;
        $universidade->tipo = $tipo;
        $universidade->id_colaborador = $idColab;
        $universidade->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("universidades");
        }
        else {
            return redirect()->route("universidadesColaborador");
        }
        
    }
    
    public function update($id, Request $request)
    {
        //Obtenção de todos os dados do request
        $id_universidade = \intval($id);
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

        $curso = $request->curso;
        $tipo = $request->tipo;
        
        $universidade = Universidade::find($id_universidade);
        if($universidade != null) {            
            ColaboradorController::update($universidade->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            
            $universidade->curso = $curso;
            $universidade->tipo = $tipo;
            $universidade->save();
        }    
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("universidades");
        }
        else {
            return redirect()->route("universidadesColaborador");
        }
        
    }
    
    public function destroy($id)
    {
       $universidade = Universidade::find($id);
       if($universidade != null) {
        $idColaborador = $universidade->id_colaborador;
        
        $universidade->delete();
        ColaboradorController::delete($idColaborador);
    }
       return redirect()->route("universidades"); 
    }

    public function getUniversidadePorId($id) {
        
        $uni = Universidade::find($id);
        $colaborador = Colaborador::find($uni->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $univer = array(
            "id_universidade" => $uni->id_universidade,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "curso" => $uni->curso,
            "tipo" => $uni->tipo,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $univer);

        if($uni != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $entidades = DB::table('universidade')
                    ->join('colaborador', 'universidade.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('universidade.id_universidade', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.email', 'colaborador.nome')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($entidades);
    }

    public function gerirProfessoresUniversidade($id) {
        $universidade = Universidade::find($id);

        \session(['id_universidade' => $id]);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirProfessoresUniversidade', ['title' => 'Universidade: <br><br>'.$universidade->nome.' - '.$universidade->tipo]);
        }
        else {
            return view('colaborador/gerirProfessoresUniversidade', ['title' => 'Universidade: <br><br>'.$universidade->nome.' - '.$universidade->tipo]);
        }
    }
}
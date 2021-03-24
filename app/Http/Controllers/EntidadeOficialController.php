<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EntidadeOficial;
use Illuminate\Http\Request;
use DB;
class EntidadeOficialController extends Controller
{
    
    public function index()
    {
        $user = session()->get("utilizador");
        
        $entidades = DB::table(DB::raw('entidade_oficial', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'entidade_oficial.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('entidade_oficial.id_entidadeOficial', 'entidade_oficial.entidade', 'entidade_oficial.nif', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($entidades as $entidade) {
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
        if($user->tipoUtilizador == 0) {
            return view('admin/entidades', ['data' => $resposta]);
        }
        else {
            return view('colaborador/entidades', ['data' => $resposta]);
        }
    }

    public function store(Request $request)
    {
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

        $entidade = $request->entidade;
        $nif = $request->nif;

        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        $entOficial = new EntidadeOficial();
        $entOficial->entidade = $entidade;
        $entOficial->nif = $nif;
        $entOficial->id_colaborador = $idColab;
        $entOficial->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("entidades");
        }
        else {
            return redirect()->route("entidadesColaborador");
        }
    }

    public function update($id ,Request $request)
    {
        $id_entidadeOficial = \intval($id);
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

        $entidade = $request->entidade;
        $nif = $request->nif;

        $entOficial = EntidadeOficial::find($id_entidadeOficial);
        if($entOficial != null){
            ColaboradorController::update($entOficial->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            $entOficial->nif = $nif;
            $entOficial->entidade = $entidade;
            $entOficial->save();
        }
        $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("entidades");
            }
            else {
                return redirect()->route("entidadesColaborador");
            }
    }

    public function destroy($id)
    {
        $entidade = EntidadeOficial::find($id);
        if($entidade != null) {
            $idColaborador = $entidade->id_colaborador;
            if($entidade->projetos()->first() != null) {
                $entidade->projetos()->where('id_entidadeOficial', $id)->delete();
            }
            $entidade->delete();
            ColaboradorController::delete($idColaborador);
        }
        
        return redirect()->route("entidades");

    }

    public function getEntidadePorId($id) {

        $entidade = EntidadeOficial::find($id);
        $colaborador = Colaborador::find($entidade->id_colaborador);

        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $entidadeOficial = array(
            "id_entidadeOficial" => $entidade->id_entidadeOficial,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "entidade" => $entidade->entidade,
            "nif" => $entidade->nif,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $entidadeOficial);
        
        if($entidadeOficial != null) {
            return response()->json($resposta);
        }
        else{
            return null;
        }
    }

    public function getDisponiveis() {
        $entidades = DB::table('entidade_oficial')
                    ->join('colaborador', 'entidade_oficial.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('entidade_oficial.id_entidadeOficial', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.email', 'colaborador.nome')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($entidades);
    }
}
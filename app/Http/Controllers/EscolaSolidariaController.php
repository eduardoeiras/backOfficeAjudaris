<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use Illuminate\Http\Request;
use DB;
use App\Models\EscolaSolidariaProf;

class EscolaSolidariaController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");

        $escolas = DB::table(DB::raw('escola_solidaria', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'escola_solidaria.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('escola_solidaria.id_escolaSolidaria', 'escola_solidaria.contactoAssPais', 'escola_solidaria.id_agrupamento',
         'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($escolas as $escola) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $escola->id_colaborador)
            ->get();
            
            $ent = array(
                "entidade" => $escola,
                "emails" => $emails
            );
            array_push($resposta, $ent);
        }

        if($user->tipoUtilizador == 0) {
            return view('admin/escolasSolidarias', ['data' => $resposta]);
        }
        else {
            return view('colaborador/escolasSolidarias', ['data' => $resposta]);
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
        
        $contAssPais = $request->contactoAssPais;
        $id_agrupamento = $request->agrupamento;

        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        $escsolidarias = new EscolaSolidaria();
        $escsolidarias->contactoAssPais = $contAssPais;
        $escsolidarias->id_agrupamento = $id_agrupamento;
        $escsolidarias->id_colaborador = $idColab;
        $escsolidarias->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("escolas");
        }
        else {
            return redirect()->route("escolasColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_escola = \intval($id);
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
        
        $contactoAssPais = $request->contactoAssPais;
        $id_agrupamento = $request->agrupamento;

        $escola = EscolaSolidaria::find($id_escola);
        if($escola != null) {
            ColaboradorController::update($escola->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            $escola->contactoAssPais = $contactoAssPais;
            $escola->id_agrupamento = $id_agrupamento;
            $escola->save();
        }
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("escolas");
        }
        else {
            return redirect()->route("escolasColaborador");
        }
    }

    public function destroy($id)
    {
        $escola = EscolaSolidaria::find($id);
        if($escola != null) {
            $idColaborador = $escola->id_colaborador;
            if($escola->professores()->first() != null) {
                return redirect()->route("escolas");
            }
            if($escola->projetos()->first() != null) {
                $escola->projetos()->where('id_escolaSolidaria', $id)->delete();
            }
            $escola->delete();
            ColaboradorController::delete($idColaborador);    
        }
        return redirect()->route("escolas");
    }

    public function getEscolaPorId($id) {

        $escola = EscolaSolidaria::find($id);
        $colaborador = Colaborador::find($escola->id_colaborador);

        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $escolaSolidaria = array(
            "id_escolaSolidaria" => $escola->id_escolaSolidaria,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "contactoAssPais" => $escola->contactoAssPais,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails,
            "id_agrupamento" => $escola->id_agrupamento
        );

        $resposta = array();
        array_push($resposta, $escolaSolidaria);
        
        
        if($escolaSolidaria != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $ilustradores = DB::table('escola_solidaria')
                    ->select('escola_solidaria.id_escolaSolidaria', 'escola_solidaria.telefone', 'escola_solidaria.telemovel', 'escola_solidaria.nome')
                    ->where([
                        ['escola_solidaria.disponivel', '=', 0]
                        ])
                    ->get();  
    
        return \json_encode($ilustradores);
    }

    public function gerirEscola($id) {
        $escola = EscolaSolidaria::find($id);

        \session(['id_escola' => $id]);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirProfessoresEscola', ['title' => 'Escola: '.$escola->nome]);
        }
        else {
            return view('colaborador/gerirProfessoresEscola', ['title' => 'Escola: '.$escola->nome]);
        }

    }

    public function getProfessores() {
        $id = intval(\session('id_escola'));
        
        $professores = DB::table('professor')
                        ->join('escola_professor', 'professor.id_professor', '=', 'escola_professor.id_professor')
                        ->select('professor.id_professor' , 'professor.nome', 'professor.telefone', 'professor.telemovel', 'professor.email')
                        ->where([
                            ['escola_professor.id_escola', '=', $id]
                            ])
                        ->get();


        return response()->json($professores);  
    }

    public function getNomeEscolaPorId($id) {
        $escola = DB::table('colaborador')
            ->join('colaborador', 'escola_solidaria.id_colaborador', 'colaborador.id_colaborador')
            ->where('id_agrupamento', $id)->first();
        
            $nome = null;

        if($escola != null) {
            $nome = $escola->nome;
        }

        return $nome;
    }

    public function associarProfessor(Request $request) {
        $novaAssoc = new EscolaSolidariaProf();

        $novaAssoc->id_escola = $request->id_escola;
        $novaAssoc->id_professor = $request->id_professor;

        $novaAssoc->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirEscola", $request->id_escola);
        }
        else {
            return redirect()->route("gerirEscolaColaborador", $request->id_escola);
        }
    }

    public function deleteAssociacao($id_professor, $id_escola) {
        
        $query = DB::table('escola_professor')
                    ->where([
                        ['escola_professor.id_escola', '=', $id_escola],
                        ['escola_professor.id_professor', '=', $id_professor]
                        ]);

        $associacao = $query->first();

        if($associacao != null) {
            $query->delete();
        }

        return redirect()->route("gerirEscola", $id_escola);
    }
}
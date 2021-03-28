<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\Professor;
use Illuminate\Http\Request;
use DB;
use Session;
class ProfessorController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $professores = DB::table(DB::raw('professor', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'professor.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('professor.id_professor', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($professores as $prof) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $prof->id_colaborador)
            ->get();
            
            $professor = array(
                "entidade" => $prof,
                "emails" => $emails
            );
            array_push($resposta, $professor);
        }

        if($user->tipoUtilizador == 0) {
            return view('admin/professores', ['data' => $resposta]);
        }
        else {
            return view('colaborador/professores', ['data' => $resposta]);
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
        
        $professor = new Professor();
        $professor->id_colaborador = $idColab;
        $professor->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("professores");
        }
        else {
            return redirect()->route("professoresColaborador");
        }
    }
    
    public function update($id ,Request $request)
    {
        //Obtenção dos atributos de um colaborador
        $id_professor = \intval($id);
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
        $emailsToDelete = $request->deletedEmails;

        $professor = Professor::find($id_professor);
        if($professor != null) {
            ColaboradorController::update($professor->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
        
            $professor->save();
        }

            
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("professores");
        }
        else {
            return redirect()->route("professoresColaborador");
        }
        
    }
    
    public function destroy($id)
    {
        $professor = Professor::find($id);
        if($professor != null){
            $idColaborador = $professor->id_colaborador;
            if($professor->projetos()->first() != null) {
                $professor->projetos()->where('id_professor', $id)->delete();
            }
            if($professor->escolas()->first() != null) {
                $professor->escolas()->where('id_professor', $id)->delete();
            }
            if($professor->trocasAgrupamento()->first() != null) {
                $professor->trocasAgrupamento()->where('id_professor', $id)->delete();
            }
            if($professor->agrupamento()->first() != null) {
                $professor->id_agrupamento = null;
                $professor->save();
            }
            $professor->delete();
            ColaboradorController::delete($idColaborador);
        }

        return redirect()->route("professores");

    }

    public function getProfPorId($id) {
        
        $prof = Professor::find($id);
        $colaborador = Colaborador::find($prof->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $professor = array(
            "id_professor" => $prof->id_professor,
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
        array_push($resposta, $professor);

        if($professor != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $professores = DB::table('professor')
                    ->join('colaborador', 'professor.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('professor.id_professor as id', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();

                    
        $resposta = array();
        foreach($professores as $entidade) {
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

    public function getNumProfs() {

        $professor = Professor::all();
        
        if($professor != null) {
            return \count($professor);
        }
        else {
            return 0;
        }
        
    }

    public function existeAssociacao($id_professor, $id_escola) {
        
        $professor = DB::table('professor')
                    ->join('escola_professor', 'professor.id_professor', '=', 'escola_professor.id_professor')
                    ->where([
                        ['escola_professor.id_professor', '=', $id_professor],
                        ['escola_professor.id_escola', '=', $id_escola]
                        ])
                    ->first();

        if($professor != null) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getDisponiveisSemEscola($id_escola) {
        $profsSemEscola = array();
        
        $profs = DB::table('professor')
                    ->join('colaborador', 'professor.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('professor.id_professor', 'colaborador.telemovel', 'colaborador.telefone', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->get();

        foreach($profs as $professor) {
            $existe = self::existeAssociacao($professor->id_professor, $id_escola);
            if($existe == false) {
                $emails = DB::table('email')
                ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                ->select('email.email')
                ->where('email.id_colaborador', '=', $professor->id_colaborador)
                ->get();
                
                $prof = array(
                    "entidade" => $professor,
                    "emails" => $emails
                );

                array_push($profsSemEscola, $prof);
            }
        }
        
        return \json_encode($profsSemEscola);
    }
}
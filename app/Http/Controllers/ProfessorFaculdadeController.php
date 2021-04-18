<?php

namespace App\Http\Controllers;

use App\Models\ProfessorFaculdade;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use DB;
use Session;
use Auth;
use SoulDoit\DataTable\SSP;

class ProfessorFaculdadeController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $profacul = DB::table(DB::raw('professor_faculdade', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'professor_faculdade.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('professor_faculdade.id_professorFaculdade', 'professor_faculdade.cargo', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($profacul as $pf) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $pf->id_colaborador)
            ->get();
            
            $profefacul = array(
                "entidade" => $pf,
                "emails" => $emails
            );
            array_push($resposta, $profefacul);
        }

        if($user->tipoUtilizador == 0) {
            return view('admin\profs_faculdade', ['data' => $resposta]);
        }
        else {
            return view('colaborador\profs_faculdade', ['data' => $resposta]);
        }
        
    }

    public function store(Request $request)
    {
        //Obtenção dos atributos de um colaborador
        $nome = $request->nome;
        $observacoes = $request->obs;
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
        
        $cargo = $request->cargo;

        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);

        $profacul = new ProfessorFaculdade();
        $profacul->cargo = $cargo;
        $profacul->id_colaborador = $idColab;
        $profacul->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("profsFaculdade");
        }
        else {
            return redirect()->route("profsFaculdadeColaborador");
        }

    }

    public function update($id, Request $request)
    {
        $id_profacul = \intval($id);
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
        $cargo = $request->cargo;
        
        $prof = ProfessorFaculdade::find($id_profacul);
        if($prof != null) {
            ColaboradorController::update($prof->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            $prof->cargo = $cargo;
            $prof->save();
        }    
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("profsFaculdade");
        }
        else {
            return redirect()->route("profsFaculdadeColaborador");
        }
    }

    public function destroy($id)
    {
        $profacul = ProfessorFaculdade::find($id);
        if($profacul != null){
            $idColaborador = $profacul->id_colaborador;
            if($profacul->projetos()->first() != null) {
                $profacul->projetos()->where('id_professorFaculdade', $id)->delete();
            }
            if($profacul->universidades()->first() != null) {
                $profacul->universidades()->where('id_professorFaculdade', $id)->delete();
            } 
            $profacul->delete();
            ColaboradorController::delete($idColaborador);
        }
        

        return redirect()->route("profsFaculdade");
    }

    public function getProfPorId($id) {
        
        $profacul = ProfessorFaculdade::find($id);
        $colaborador = Colaborador::find($profacul->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $profaculd = array(
            "id_professorFaculdade" => $profacul->id_professorFaculdade,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "cargo" => $profacul->cargo,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $profaculd);

        if($profacul != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $profefacul = DB::table('professor_faculdade')
                    ->join('colaborador', 'professor_faculdade.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('professor_faculdade.id_professorFaculdade as id', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();  

        $resposta = array();
        foreach($profefacul as $entidade) {
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

    public function existeAssociacao($id_professor, $id_universidade) {
        
        $professor = DB::table('professor_faculdade')
                    ->join('universidade_prof_faculdade', 'professor_faculdade.id_professorFaculdade', '=', 'universidade_prof_faculdade.id_professorFaculdade')
                    ->where([
                        ['universidade_prof_faculdade.id_professorFaculdade', '=', $id_professor],
                        ['universidade_prof_faculdade.id_universidade', '=', $id_universidade]
                        ])
                    ->first();

        if($professor != null) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getDisponiveisSemEscola($id_universidade) {
        $profsSemUniversidade = array();
        
        $profs = DB::table('professor_faculdade')
                ->join('colaborador', 'professor_faculdade.id_colaborador', '=' , 'colaborador.id_colaborador')
                ->select('professor_faculdade.id_professorFaculdade', 'professor_faculdade.cargo', 'colaborador.telemovel', 'colaborador.telefone', 'colaborador.nome', 'colaborador.id_colaborador')
                ->get();

        

        foreach($profs as $professor) {
            $existe = self::existeAssociacao($professor->id_professorFaculdade, $id_universidade);
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
                array_push($profsSemUniversidade, $prof);
            }
        }
        
        return \json_encode($profsSemUniversidade);
    }

    public function getAll() {
        $dt = [
            ['label'=>'Nome', 'db'=>'id_colaborador', 'dt'=>0, 'formatter'=>function($value, $model){
                $GLOBALS["colaboradorBD"] = Colaborador::find($value);
                return $GLOBALS["colaboradorBD"]->nome;
            }],
            ['label'=>'Cargo', 'db'=>'cargo', 'dt'=>1, 'formatter'=>function($value, $model){
                return $value;
            }],
            ['label'=>'Telemóvel', 'db'=>'id_colaborador', 'dt'=>2, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telemovel == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telemovel;
                }
            }],
            ['label'=>'Telefone', 'db'=>'id_colaborador', 'dt'=>3, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telefone == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telefone;
                }
            }],
            ['label'=>'Emails', 'db'=>'id_colaborador', 'dt'=>4, 'formatter'=>function($value, $model){
                $colabEmails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($value))
                    ->get();
                $returnValue = "";
                if(count($colabEmails) > 0) {
                    foreach($colabEmails as $email) {
                        $returnValue = $returnValue.$email->email;
                    } 
                    return $returnValue;   
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Disponibilidade', 'dt'=>5, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->disponivel == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            }],
            ['label'=>'Localidade', 'dt'=>6, 'formatter'=>function($value, $model){
                $codPostal = CodPostal::find($GLOBALS["colaboradorBD"]->codPostal);
                if($codPostal->localidade != null) {
                    return $codPostal->localidade;
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Rua', 'dt'=>7, 'formatter'=>function($value, $model){
                $codPostalRua = DB::table('cod_postal_rua')
                ->where([
                    ['cod_postal_rua.codPostal', '=', $GLOBALS["colaboradorBD"]->codPostal],
                    ['cod_postal_rua.codPostalRua', '=', $GLOBALS["colaboradorBD"]->codPostalRua],
                    ])->first();
                if($codPostalRua != null) {
                    if($codPostalRua->rua) {
                        return $codPostalRua->rua;  
                    }
                    else {
                        return " --- ";
                    }
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Código Postal', 'db'=>'id_colaborador', 'dt'=>8, 'formatter'=>function($value, $model){
                $strCodPostal = $GLOBALS["colaboradorBD"]->codPostal."-".$GLOBALS["colaboradorBD"]->codPostalRua;
                return $strCodPostal;
            }],
            ['label'=>'Opções', 'db'=>'id_professorFaculdade', 'dt'=>9, 'formatter'=>function($value, $model){ 
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = ['<td>
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>
                    <a href="gerirComunicacoes-'.$GLOBALS["colaboradorBD"]->id_colaborador.'-'.$GLOBALS["colaboradorBD"]->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    </td>'];
                }
                else {
                    $btns = ['<td>
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="gerirComunicacoes-'.$GLOBALS["colaboradorBD"]->id_colaborador.'-'.$GLOBALS["colaboradorBD"]->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    </td>'];
                }
                
                return implode(" ", $btns); 
            }],
        ];
        $dt_obj = new SSP('App\Models\ProfessorFaculdade', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
}
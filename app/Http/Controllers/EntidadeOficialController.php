<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EntidadeOficial;
use Illuminate\Http\Request;
use DB;
use SoulDoit\DataTable\SSP;

class EntidadeOficialController extends Controller
{
    
    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/entidades');
        }
        else {
            return view('colaborador/entidades');
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
                    ->select('entidade_oficial.id_entidadeOficial as id', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
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
    
        return \json_encode($resposta);
    }

    public function getAll() {
        $dt = [
            ['label'=>'Nome', 'db'=>'id_colaborador', 'dt'=>0, 'formatter'=>function($value, $model){
                $GLOBALS["colaboradorBD"] = Colaborador::find($value);
                return $GLOBALS["colaboradorBD"]->nome;
            }],
            ['label'=>'Entidade', 'db'=>'entidade', 'dt'=>1, 'formatter'=>function($value, $model){
                if($value != null) {
                    return $value;
                }
                else {
                    return " --- ";
                }
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
            ['label'=>'Disponibilidade', 'db'=>'id_colaborador', 'dt'=>5, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->disponivel == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            }],
            ['label'=>'Localidade', 'db'=>'id_colaborador', 'dt'=>6, 'formatter'=>function($value, $model){
                $codPostal = CodPostal::find($GLOBALS["colaboradorBD"]->codPostal);
                if($codPostal->localidade != null) {
                    return $codPostal->localidade;
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Rua', 'db'=>'id_colaborador', 'dt'=>7, 'formatter'=>function($value, $model){
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
            ['label'=>'Opções', 'db'=>'id_entidadeOficial', 'dt'=>9, 'formatter'=>function($value, $model){ 
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
        $dt_obj = new SSP('App\Models\EntidadeOficial', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
}
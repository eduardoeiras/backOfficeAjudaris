<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\IlustradorSolidario;
use Illuminate\Http\Request;
use DB;
use SoulDoit\DataTable\SSP;

class IlustradorSolidarioController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/ilustradores');
        }
        else {
            return view('colaborador/ilustradores');
        }
    }

    public function store(Request $request)
    {
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
        
        $volumeLivro = $request->volumeLivro;

        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        $ilusolidario = new IlustradorSolidario();
        $ilusolidario->volumeLivro = $volumeLivro;
        $ilusolidario->id_colaborador = $idColab;
        $ilusolidario->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("ilustradores");
        }
        else {
            return redirect()->route("ilustradoresColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_ilustradorSolidario = \intval($id);
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

        $volumeLivro = $request->volumeLivro;

        $ilusolidario = IlustradorSolidario::find($id_ilustradorSolidario);
        if($ilusolidario != null) {
            ColaboradorController::update($ilusolidario->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            $ilusolidario->volumeLivro = $volumeLivro;
            $ilusolidario->save();
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("ilustradores");
            }
            else {
                return redirect()->route("ilustradoresColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $ilustrador = IlustradorSolidario::find($id);
        if($ilustrador != null) {
            $idColaborador = $ilustrador->id_colaborador;
            if($ilustrador->projetos()->first() != null) {
                $ilustrador->projetos()->where('id_ilustradorSolidario', $id)->delete();
            }
            $ilustrador->delete(); 
            ColaboradorController::delete($idColaborador);    
        }
        
        return redirect()->route("ilustradores");

    }
    
    public function getIlustradorPorId($id) {

        $ilustrador = IlustradorSolidario::find($id);
        $colaborador = Colaborador::find($ilustrador->id_colaborador);

        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $ilustradorSolidario = array(
            "id_ilustradorSolidario" => $ilustrador->id_ilustradorSolidario,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "volumeLivro" => $ilustrador->volumeLivro,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $ilustradorSolidario);
        
        if($ilustradorSolidario != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
            $ilustradores = DB::table('ilustrador_solidario')
                        ->join('colaborador', 'ilustrador_solidario.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('ilustrador_solidario.id_ilustradorSolidario as id', 'colaborador.telemovel', 'colaborador.telefone', 'colaborador.nome', 'colaborador.id_colaborador')
                        ->where([
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();
        
        $resposta = array();

        foreach($ilustradores as $entidade) {
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
            ['label'=>'Telemóvel', 'db'=>'id_colaborador', 'dt'=>1, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telemovel == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telemovel;
                }
            }],
            ['label'=>'Telefone', 'db'=>'id_colaborador', 'dt'=>2, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telefone == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telefone;
                }
            }],
            ['label'=>'Emails', 'db'=>'id_colaborador', 'dt'=>3, 'formatter'=>function($value, $model){
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
            ['label'=>'Disponibilidade', 'dt'=>4, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->disponivel == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            }],
            ['label'=>'Volume do Livro', 'db' => 'volumeLivro', 'dt'=>5, 'formatter'=>function($value, $model){
                if($value != null) {
                    return $value;
                }
                else {
                    return ' --- ';
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
            ['label'=>'Opções', 'db'=>'id_ilustradorSolidario', 'dt'=>9, 'formatter'=>function($value, $model){ 
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
        $dt_obj = new SSP('App\Models\IlustradorSolidario', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\RBE;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Concelho;
use App\Models\Rbe_concelho;
use DB;
use Session;
use Auth;
use SoulDoit\DataTable\SSP;

class RBEController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin\rbes');
        }
        else {
            return view('colaborador\rbes');
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
        
        $concelhos = $request->concelhos;
        
        //Obtenção do atributo do agrupamento
        $regiao = $request->regiao;
        
        //Obtenção do id do colaborador criado
        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);

        $rbe = new RBE();
        $rbe->regiao = $regiao;
        $rbe->id_colaborador = $idColab;
        $rbe->save();
        
        $id_rbe = self::getLastId();
        
        ConcelhoController::criaAssociaConcelhos($concelhos, $id_rbe);
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("rbes");
        }
        else {
            return redirect()->route("rbesColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_rbe = \intval($id);
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

        $concelhos = $request->concelhos;
        $concelhosToDelete = $request->deletedConcelhos;
        
        $regiao = $request->regiao;
        
        $rbe = RBE::find($id_rbe);
        if($rbe != null) {
            ColaboradorController::update($rbe->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
            if($concelhos != null) {
                if(count($concelhos) > 0) {
                    ConcelhoController::criaAssociaConcelhos($concelhos, $id_rbe);    
                }    
            }
            if($concelhosToDelete != null) {
                if(count($concelhosToDelete) > 0) {
                    ConcelhoController::removeAssociaConcelhos($concelhosToDelete, $id_rbe);    
                }    
            }
            
            $rbe->regiao = $regiao;
            $rbe->save();
        }  
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("rbes");
        }
        else {
            return redirect()->route("rbesColaborador");
        }
    }


    public function destroy($id)
    {
        $rbe = RBE::find($id);
        if($rbe != null) {
            $idColaborador = $rbe->id_colaborador;
            if($rbe->projetos()->first() != null) {
                $rbe->projetos()->where('id_rbe', $id)->delete();
            }
            if($rbe->concelhos()->first() != null) {
                $rbe->concelhos()->where('id_rbe', $id)->delete();
            }

            $rbe->delete();
            ColaboradorController::delete($idColaborador);
        }
        return redirect()->route("rbes");
    }

    public function getRbePorId($id) {
        
        $rbe = RBE::find($id);
        $colaborador = Colaborador::find($rbe->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $concelho = DB::table('concelho')
            ->join('rbe_concelho', 'rbe_concelho.id_concelho', '=', 'concelho.id_concelho')
            ->select('concelho.nome')
            ->where('rbe_concelho.id_rbe', '=', $rbe->id_rbe)
            ->get();

        $rbe = array(
            "id_rbe" => $rbe->id_rbe,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "regiao" => $rbe->regiao,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails,
            "concelhos" => $concelho
        );

        $resposta = array();
        array_push($resposta, $rbe);

        if($rbe != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $rbes = DB::table('rbe')
                    ->join('colaborador', 'rbe.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('rbe.id_rbe as id', 'rbe.regiao', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();
                    
        $resposta = array();
        foreach($rbes as $entidade) {
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

    public static function getLastId()
    {
        $id = DB::select('SELECT MAX(id_rbe) as id_rbe FROM rbe')[0]->id_rbe;
        
        if($id != null) {
           return $id; 
        }
        else {
            return null;
        }
    }

    public function getAll() {
        $dt = [
            ['label'=>'Região', 'db'=>'regiao', 'dt'=>0, 'formatter'=>function($value, $model){
                return $value;
            }],
            ['label'=>'Nome do Coordenador', 'db'=>'id_colaborador', 'dt'=>1, 'formatter'=>function($value, $model){
                $GLOBALS["colaboradorBD"] = Colaborador::find($value);
                return $GLOBALS["colaboradorBD"]->nome;
            }],
            ['label'=>'Concelhos', 'db'=>'id_rbe', 'dt'=>2, 'formatter'=>function($value, $model){
                $concelhos = DB::table('concelho')
                    ->join('rbe_concelho', 'rbe_concelho.id_concelho', '=', 'concelho.id_concelho')
                    ->select('concelho.nome')
                    ->where('rbe_concelho.id_rbe', '=', $value)
                    ->get();
                $strLinha = "";
                foreach($concelhos as $concelho) {
                    $strLinha = $strLinha.$concelho->nome."\n";
                }
                return $strLinha;
            }],
            ['label'=>'Telemóvel', 'db'=>'id_colaborador', 'dt'=>3, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telemovel == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telemovel;
                }
            }],
            ['label'=>'Telefone', 'db'=>'id_colaborador', 'dt'=>4, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->telefone == null) {
                    return ' ---- ';
                }
                else {
                    return $GLOBALS["colaboradorBD"]->telefone;
                }
            }],
            ['label'=>'Emails', 'db'=>'id_colaborador', 'dt'=>5, 'formatter'=>function($value, $model){
                $colabEmails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($value))
                    ->get();
                $returnValue = "";
                if(count($colabEmails) > 0) {
                    foreach($colabEmails as $email) {
                        $returnValue = $returnValue.$email->email."\n";
                    } 
                    return $returnValue;   
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Disponibilidade', 'db'=>'id_colaborador', 'dt'=>6, 'formatter'=>function($value, $model){
                if($GLOBALS["colaboradorBD"]->disponivel == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            }],
            ['label'=>'Localidade', 'db'=>'id_colaborador', 'dt'=>7, 'formatter'=>function($value, $model){
                $codPostal = CodPostal::find($GLOBALS["colaboradorBD"]->codPostal);
                if($codPostal->localidade != null) {
                    return $codPostal->localidade;
                }
                else {
                    return " --- ";
                }
            }],
            ['label'=>'Rua', 'db'=>'id_colaborador', 'dt'=>8, 'formatter'=>function($value, $model){
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
            ['label'=>'Código Postal', 'db'=>'id_colaborador', 'dt'=>9, 'formatter'=>function($value, $model){
                $strCodPostal = $GLOBALS["colaboradorBD"]->codPostal."-".$GLOBALS["colaboradorBD"]->codPostalRua;
                return $strCodPostal;
            }],
            ['label'=>'Opções', 'db'=>'id_rbe', 'dt'=>10, 'formatter'=>function($value, $model){ 
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
        $dt_obj = new SSP('App\Models\RBE', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Agrupamento;
use Illuminate\Http\Request;
use \App\Models\CodPostal;
use \App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use DB;

class AgrupamentoController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        $agrupamentos = DB::table(DB::raw('agrupamento', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'agrupamento.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('agrupamento.id_agrupamento', 'agrupamento.nomeDiretor', 'agrupamento.nif', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();

        $resposta = array();

        foreach($agrupamentos as $agrup) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $agrup->id_colaborador)
            ->get();
            
            $agrupamento = array(
                "entidade" => $agrup,
                "emails" => $emails
            );
            array_push($resposta, $agrupamento);
        }
        
        
        if($user->tipoUtilizador == 0) {
            return view('admin/agrupamentos', ['data' => $resposta]);
        }
        else {
            return view('colaborador/agrupamentos', ['data' => $resposta]);
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

        //Obtenção do atributo do agrupamento
        $nomeDiretor = $request->nomeDiretor;
        $nif = $request->nif;

        //Obtenção do id do colaborador criado
        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        //Criação do registo na tabela agrupamento com o id do colaborador criado
        $agrupamento = new Agrupamento();
        $agrupamento->nomeDiretor = $nomeDiretor;
        $agrupamento->nif = $nif;
        $agrupamento->id_colaborador = $idColab;
        $agrupamento->save();
        
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("agrupamentos");
        }
        else {
            return redirect()->route("agrupamentosColaborador");
        }
    }
    
    public function update($id ,Request $request)
    {
        //Obtenção de todos os dados do request
        $id_agrupamento = \intval($id);
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
        
        //Obtenção do nome do diretor do request e do agrupamento
        $nomeDiretor = $request->nomeDiretor;
        $nif = $request->nif;
        $agrupamento = Agrupamento::find($id_agrupamento);
        if($agrupamento != null) {
            //Chamar a função de update do registo do colaborador associado ao agrupamento, neste caso
            ColaboradorController::update($agrupamento->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
        
            //Update do nome do diretor no registo da tabela agrupamento
            $agrupamento->nomeDiretor = $nomeDiretor;
            $agrupamento->nif = $nif;  
            $agrupamento->save(); 
        }
         
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("agrupamentos");
        }
        else {
            return redirect()->route("agrupamentosColaborador");
        }
        
    }
    
    public function destroy($id)
    {
        $agrupamento = Agrupamento::find($id);
        if($agrupamento != null) {
            $idColaborador = $agrupamento->id_colaborador;
            if($agrupamento->escolas()->first() != null) {
                $agrupamento->escolas()->where('id_agrupamento', $id)->delete();
            }
            if($agrupamento->professores()->first() != null) {
                $agrupamento->professores()->where('id_agrupamento', $id)->delete();
            }
            $agrupamento->delete();   
            ColaboradorController::delete($idColaborador);
        }
        
        return redirect()->route("agrupamentos");
    }

    public static function getNomeAgrupamentoPorId($id) {
        
        $agrupamento = DB::table(DB::raw('agrupamento', 'colaborador'))
            ->join('colaborador', 'agrupamento.id_colaborador', 'colaborador.id_colaborador')
            ->select('agrupamento.id_agrupamento', 'colaborador.nome')
            ->where('id_agrupamento', $id)->first();
        if($agrupamento != null) {
            return $agrupamento->nome;  
        }
        else {
            return null;
        }
        
    }

    public function getAgrupamentoPorId($id) {
        
        $agrup = Agrupamento::find($id);
        $colaborador = Colaborador::find($agrup->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);
                    
        $agrupamento = array(
            "id_agrupamento" => $agrup->id_agrupamento,
            "nome" => $colaborador->nome,
            "telefone" => $colaborador->telefone,
            "telemovel" => $colaborador->telemovel,
            "disponivel" => $colaborador->disponivel,
            "observacoes" => $colaborador->observacoes,
            "nomeDiretor" => $agrup->nomeDiretor,
            "nif" => $agrup->nif,
            "rua" => $codPostalRua->rua,
            "numPorta" => $colaborador->numPorta,
            "localidade" => $codPostal->localidade,
            "codPostal" => $colaborador->codPostal,
            "codPostalRua" => $colaborador->codPostalRua,
            "distrito" => $codPostal->distrito,
            "emails" => $emails
        );

        $resposta = array();
        array_push($resposta, $agrupamento);
          
        if($agrupamento != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
    }
    /*
        $agrupamentos = DB::table(DB::raw('agrupamento', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'agrupamento.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('agrupamento.id_agrupamento', 'agrupamento.nomeDiretor', 'colaborador.nome', 'cod_postal.localidade', 'cod_postal.distrito')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal')
        ->get();
                
        
        if($agrupamentos != null) {
            return  response()->json($agrupamentos);
        }
        else {
            return null;
        }
    */
    public function getAll() {
        $dt = [
            ['label'=>'Nome de Utilizador', 'db'=>'nomeUtilizador', 'dt'=>0],
            ['label'=>'Nome', 'db'=>'nome', 'dt'=>1],
            ['label'=>'Passsword', 'db'=>'password', 'dt'=>2],
            ['label'=>'Emails', 'db'=>'email', 'dt'=>3, 'formatter'=>function($value, $model){
                if($value == null) {
                    return ' ---- ';
                }
                else {
                    return $value;
                }
            }],
            ['label'=>'Telemóvel', 'db'=>'telemovel', 'dt'=>4, 'formatter'=>function($value, $model){
                if($value == null) {
                    return ' --- ';
                }
                else {
                    return $value;
                }
            }],
            ['label'=>'Telefone', 'db'=>'telefone', 'dt'=>5, 'formatter'=>function($value, $model){
                if($value == null) {
                    return ' ---- ';
                }
                else {
                    return $value;
                }
            }],
            ['label'=>'Departamento', 'db'=>'departamento', 'dt'=>6],
            ['label'=>'Opções', 'db'=>'id_utilizador', 'dt'=>8, 'formatter'=>function($value, $model){ 
                $btns = ['<td>
                        <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$value.', true)"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Edit">&#xE254;</i></a>
                        <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$value.')"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Delete">&#xE872;</i></a>
                        </td>'];
                return implode(" ", $btns); 
            }],
        ];
        $dt_obj = new SSP('App\Models\Agrupamento', $dt);

        echo json_encode($dt_obj->getDtArr());
    }
    
}
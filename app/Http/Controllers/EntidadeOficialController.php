<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EntidadeOficial;
use Illuminate\Http\Request;
use DB;
use DataTables;

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

        $entidades = DB::table(DB::raw('entidade_oficial', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'entidade_oficial.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('entidade_oficial.id_entidadeOficial', 'entidade_oficial.entidade', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal');

        return Datatables::of($entidades)
            ->editColumn('emails', function ($model) {
                $colabEmails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($model->id_colaborador))
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
            })
            ->editColumn('telefone', function ($model) {
                if($model->telefone != null) {
                    return $model->telefone;
                }
                else {
                    return " --- ";
                }
            })
            ->editColumn('telemovel', function ($model) {
                if($model->telemovel != null) {
                    return $model->telemovel;
                }
                else {
                    return " --- ";
                }
            })
            ->editColumn('disponibilidade', function ($model) {
                if($model->disponivel == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            })
            ->editColumn('rua', function ($model) {
                if($model->rua != null) {
                    return $model->rua;
                }
                else {
                    return " --- ";
                }
            })
            ->editColumn('localidade', function ($model) {
                if($model->localidade != null) {
                    return $model->localidade;
                }
                else {
                    return " --- ";
                }
            })
            ->editColumn('cod_postal', function ($model) {
                if($model->codPostal != ' ' && $model->codPostalRua != ' ') {
                    $strCodPostal = $model->codPostal."-".$model->codPostalRua;
                }
                else {
                    $strCodPostal = " --- ";
                }
                
                return $strCodPostal;
            })
            ->addColumn('opcoes', function($model){
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = '
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_entidadeOficial.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_entidadeOficial.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    ';
                }
                else {
                    $btns = '
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_entidadeOficial.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    ';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true);

    }
}
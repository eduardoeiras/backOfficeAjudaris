<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\ContadorHistoria;
use Illuminate\Http\Request;
use DB;
use DataTables;

class ContadorHistoriaController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/contadores');
        }
        else {
            return view('colaborador/contadores');
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

        $idColab = ColaboradorController::create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal, $codPostalRua,
        $rua, $localidade, $distrito, $emails);
        
        $contadorHistoria = new ContadorHistoria();
        $contadorHistoria->id_colaborador = $idColab;
        $contadorHistoria->save();
        
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("contadores");
        }
        else {
            return redirect()->route("contadoresColaborador");
        }
    }

    public function update($id, Request $request)
    {
        $id_contadorHistorias = \intval($id);
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
        
        $contador = ContadorHistoria::find($id_contadorHistorias);
        if($contador != null) {
            ColaboradorController::update($contador->id_colaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta,
            $disponibilidade, $codPostal, $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete);
        } 
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("contadores");
        }
        else {
            return redirect()->route("contadoresColaborador");
        }
    }

    public function destroy($id)
    {
        $contador = ContadorHistoria::find($id);
        if($contador != null) {
            $idColaborador = $contador->id_colaborador;
            if($contador->projetos()->first() != null) {
                $contador->projetos()->where('id_contador', $id)->delete();
            }
            $contador->delete();
            ColaboradorController::delete($idColaborador);
        }
        

        return redirect()->route("contadores");
 
    }

    public function getContadorPorId($id) {
        $contador = ContadorHistoria::find($id);
        $colaborador = Colaborador::find($contador->id_colaborador);
        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $contador = array(
            "id_contadorHistorias" => $contador->id_contadorHistorias,
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
        array_push($resposta, $contador);

        if($contador != null) {
            return response()->json($resposta);  
        }
        else {
            return null;
        }
        
    }

    public function getDisponiveis() {
        $contadores = DB::table('contador_historias')
                    ->join('colaborador', 'contador_historias.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('contador_historias.id_contadorHistorias as id', 'colaborador.telemovel', 'colaborador.telefone', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();
    
    $resposta = array();

    foreach($contadores as $entidade) {
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

        $contadoresHistorias = DB::table(DB::raw('contador_historias', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'contador_historias.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('contador_historias.id_contadorHistorias', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal');

        return Datatables::of($contadoresHistorias)
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
            })->editColumn('telefone', function ($model) {
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
            ->editColumn('cod_postal', function ($model) {
                $strCodPostal = $model->codPostal."-".$model->codPostalRua;
                return $strCodPostal;
            })
            ->addColumn('opcoes', function($model){
                $user = session()->get("utilizador");
                if($user->tipoUtilizador == 0) {
                    $btns = '
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_contadorHistorias.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_contadorHistorias.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>´
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    ';
                }
                else {
                    $btns = '
                    <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_contadorHistorias.')"><i
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
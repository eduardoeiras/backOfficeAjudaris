<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\IlustradorSolidario;
use Illuminate\Http\Request;
use DB;
use DataTables;

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
        
        $ilustradores = DB::table(DB::raw('ilustrador_solidario', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'ilustrador_solidario.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('ilustrador_solidario.*', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal');

        return Datatables::of($ilustradores)
            ->editColumn('emails', function ($model) {
                $colabEmails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($model->id_colaborador))
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
            ->editColumn('volumeLivro', function ($model) {
                if($model->volumeLivro != null) {
                    return $model->volumeLivro;
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
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_ilustradorSolidario.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_ilustradorSolidario.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_ilustradorSolidario.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true); 

    }
}
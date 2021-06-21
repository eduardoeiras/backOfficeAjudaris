<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use App\Models\Professor;
use Illuminate\Http\Request;
use DB;
use App\Models\EscolaSolidariaProf;
use App\Models\Agrupamento;
use App\Models\TrocaAgrupamento;
use DateTimeZone;
use DateTime;
use DataTables;

class EscolaSolidariaController extends Controller
{
    public function index()
    {
        $user = session()->get("utilizador");

        if($user->tipoUtilizador == 0) {
            return view('admin/escolasSolidarias');
        }
        else {
            return view('colaborador/escolasSolidarias');
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
                $escola->professores()->where('id_escola', $id)->delete();
            }
            if($escola->projetos()->first() != null) {
                $escola->projetos()->where('id_escolaSolidaria', $id)->delete();
            }
            if($escola->historias()->first() != null) {
                $escola->historias()->where('id_escolaSolidaria', $id)->delete();
            }
            if($escola->livrosAno()->first() != null) {
                $escola->livrosAno()->where('id_escola', $id)->delete();
            }
            $escola->id_agrupamento = null;
            $escola->save();
            $escola->delete();
            ColaboradorController::delete($idColaborador);    
        }
        return redirect()->route("escolas");
    }

    public function changeInterlocutor($id_professor, $id_escola)
    {
        $associacao = DB::table('escola_professor')
                    ->where([
                        ['escola_professor.id_escola', '=', $id_escola],
                        ['escola_professor.id_professor', '=', $id_professor]
                    ]);
        
        if($associacao->first() != null){
            if($associacao->first()->interlocutor == 0){
                $associacao->update(['interlocutor' => 1]);
            }else {
                $associacao->update(['interlocutor' => 0]);
            }
            return 1;
        }else {
            return 0;
        }
        
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
        $escolas = DB::table('escola_solidaria')
                    ->join('colaborador', 'escola_solidaria.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('escola_solidaria.id_escolaSolidaria as id', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.nome', 'colaborador.id_colaborador')
                    ->where([
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();
        $resposta = array();

        foreach($escolas as $entidade) {
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

    public function gerirEscola($id) {
        $escola = EscolaSolidaria::find($id);
        if($escola != null) {
            $colaborador = Colaborador::find($escola->id_colaborador);
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return view('admin/gerirProfessoresEscola', ['title' => 'Escola: '.$colaborador->nome, 'id_escola' => $id]);
            }
            else {
                return view('colaborador/gerirProfessoresEscola', ['title' => 'Escola: '.$colaborador->nome, 'id_escola' => $id]);
            }
        }
    }

    public function getProfessores($id) {
        
        $professores = DB::table('professor')
                        ->join('escola_professor', 'professor.id_professor', '=', 'escola_professor.id_professor')
                        ->join('colaborador', 'professor.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('escola_professor.interlocutor', 'professor.id_professor' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.id_colaborador')
                        ->where([
                            ['escola_professor.id_escola', '=', $id]
                            ])
                        ->get();
        
        $resposta = array();

        foreach($professores as $professor) {
            $emails = DB::table('email')
            ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
            ->select('email.email')
            ->where('email.id_colaborador', '=', $professor->id_colaborador)
            ->get();
            
            $prof = array(
                "entidade" => $professor,
                "emails" => $emails
            );
            array_push($resposta, $prof);
        }


        return response()->json($resposta);  
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
        $escola = EscolaSolidaria::find($request->id_escola);
        $professor = Professor::find($request->id_professor);
        $numAgrupamentos = DB::table('escola_professor')
        ->join('escola_solidaria', 'escola_professor.id_escola', 'escola_solidaria.id_escolaSolidaria')
        ->select('escola_solidaria.id_agrupamento')
        ->where('escola_professor.id_professor', $request->id_professor)->distinct('escola_solidaria.id_agrupamento')->count('escola_solidaria.id_agrupamento');
        if($professor->id_agrupamento != null) {
            if($numAgrupamentos == 1) {
                $idAgrupamentoOld = $professor->id_agrupamento; 
                $nomeAgrupamentoOld = DB::table('agrupamento')
                ->join('colaborador', 'agrupamento.id_colaborador', 'colaborador.id_colaborador')
                ->select('colaborador.nome')
                ->where('id_agrupamento', $idAgrupamentoOld)->first()->nome;

                $nomeAgrupamentoNew = DB::table('agrupamento')
                ->join('colaborador', 'agrupamento.id_colaborador', 'colaborador.id_colaborador')
                ->select('colaborador.nome')
                ->where('id_agrupamento', $escola->id_agrupamento)->first()->nome;

                $date = new DateTime( 'now', new DateTimeZone("Europe/Lisbon") );

                $trocas = new TrocaAgrupamento();
                $trocas->agrupamentoAntigo = $nomeAgrupamentoOld;
                $trocas->data = $date->format('Y-m-d H:i:s');
                $trocas->novoAgrupamento = $nomeAgrupamentoNew;
                $trocas->id_professor = $request->id_professor;
                $trocas->save();

            }    
        }
        $novaAssoc = new EscolaSolidariaProf();
        $novaAssoc->id_escola = $request->id_escola;
        $novaAssoc->id_professor = $request->id_professor;
        $novaAssoc->interlocutor = 0;
        $novaAssoc->save();
        $professor->update(['id_agrupamento' => $escola->id_agrupamento]);

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

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirEscola", $id_escola);
        }
        else {
            return redirect()->route("gerirEscolaColaborador", $id_escola);
        }
    }

    public function getAll() {

        $escolas = DB::table(DB::raw('escola_solidaria', 'agrupamento', 'colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('colaborador', 'escola_solidaria.id_colaborador', '=' , 'colaborador.id_colaborador')
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('escola_solidaria.*', 'colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal');

        return Datatables::of($escolas)
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
            ->editColumn('contactoAssPais', function ($model) {
                if($model->contactoAssPais != null) {
                    return $model->contactoAssPais;
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
            ->editColumn('agrupamento', function ($model) {
                $nomeAgrupamento = AgrupamentoController::getNomeAgrupamentoPorId($model->id_agrupamento);
                 if($nomeAgrupamento != null) {
                     return $nomeAgrupamento;
                 }
                 else {
                     return " --- ";
                 }
            })
            ->addColumn('opcoes', function($model){
                $user = session()->get("utilizador");
                $url = 'gerirEscola'.$model->id_escolaSolidaria;
                if($user->tipoUtilizador == 0) {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_escolaSolidaria.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$model->id_escolaSolidaria.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>
                    <a href="'.$url.'"><img src="http://backofficeAjudaris/images/gerir_professores.png"></img></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    <a href="gerirLivrosAno-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_livros_ano.png"></img></a>
                    <a href="gerirHistorias-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_historias.png"></img></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_escolaSolidaria.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="'.$url.'"><img src="http://backofficeAjudaris/images/gerir_professores.png"></img></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    <a href="gerirLivrosAno-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_livros_ano.png"></img></a>
                    <a href="gerirHistorias-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_historias.png"></img></a>';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true);

    }
}
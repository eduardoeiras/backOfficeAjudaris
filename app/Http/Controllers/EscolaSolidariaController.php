<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\Colaborador;
use App\Models\EscolaSolidaria;
use App\Models\Professor;
use Illuminate\Http\Request;
use DB;
use App\Models\EscolaSolidariaProf;
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
                return redirect()->route("escolas");
            }
            if($escola->projetos()->first() != null) {
                $escola->projetos()->where('id_escolaSolidaria', $id)->delete();
            }
            $escola->delete();
            ColaboradorController::delete($idColaborador);    
        }
        return redirect()->route("escolas");
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
                        ->select('professor.id_professor' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'colaborador.id_colaborador')
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
        $novaAssoc = new EscolaSolidariaProf();

        $novaAssoc->id_escola = $request->id_escola;
        $novaAssoc->id_professor = $request->id_professor;

        $novaAssoc->save();

        $professor = Professor::find($request->id_professor);
        $escola = EscolaSolidaria::find($request->id_escola);
        $professor->id_agrupamento = $escola->id_agrupamento;
        $professor->save();

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
            ->editColumn('cod_postal', function ($model) {
                $strCodPostal = $model->codPostal."-".$model->codPostalRua;
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
                    <a href="gerirLivrosAno-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_livros_ano.png"></img></a>';
                }
                else {
                    $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_escolaSolidaria.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
                    <a href="'.$url.'"><img src="http://backofficeAjudaris/images/gerir_professores.png"></img></a>
                    <a href="gerirComunicacoes-'.$model->id_colaborador.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
                    <a href="gerirLivrosAno-'.$model->id_escolaSolidaria.'-'.$model->nome.'"><img src="http://backofficeAjudaris/images/gerir_livros_ano.png"></img></a>';
                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true);

    }
}
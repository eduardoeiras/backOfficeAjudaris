<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;
use DB;
use Session;

class ProjetoController extends Controller
{

    public function index()
    {
        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            $projetos = Projeto::all();
            return view('admin/pagInicial', ['projetos' => $projetos]);
        }
        else {
            $projetos = DB::table('projeto')
                    ->join('projeto_utilizador', 'projeto.id_projeto', '=', 'projeto_utilizador.id_projeto')
                    ->select('projeto.id_projeto', 'projeto.regulamento', 'projeto.nome', 'projeto.objetivos', 'projeto.publicoAlvo', 'projeto.observacoes')
                    ->where([
                        ['projeto_utilizador.id_utilizador', '=', $user->id_utilizador]
                        ])
                    ->get();
            return view('colaborador/pagInicial', ['projetos' => $projetos]);
        }
    }

    public function update($id, Request $request)
    {
        $id_projeto = \intval($id);
        $nome = $request->nome;
        $objetivos = $request->objetivos;
        $publicoAlvo = $request->publicoAlvo;
        $observacoes = $request->observacoes;
        
        $projeto = Projeto::find($id_projeto);
        if($projeto != null) {
            $projeto->nome = $nome;
            $projeto->objetivos = $objetivos;
            $projeto->regulamento = $request->urlFicheiro;
            $projeto->publicoAlvo = $publicoAlvo;
            $projeto->observacoes = $observacoes;

            $projeto->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("dashboardAdmin");
            }
            else {
                return redirect()->route("dashboardColaborador");
            }
        }
    }

    public function store(Request $request)
    {
        $projeto = new Projeto();

        $projeto->nome = $request->nome;
        $projeto->objetivos = $request->objetivos;
        if($request->urlFicheiro != '') {
            $projeto->regulamento = $request->urlFicheiro;    
        }
        $projeto->publicoAlvo = $request->publicoAlvo;
        $projeto->observacoes = $request->observacoes;

        $projeto->save();
        
        $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("dashboardAdmin");
            }
            else {
                return redirect()->route("dashboardColaborador");
            }
    }

    public function destroy($id)
    {
        $projeto = Projeto::find($id);
        if($projeto != null) {
            if($projeto->utilizadores()->first() != null) {
                $projeto->utilizadores()->where('id_projeto', $id)->delete();
            } 
            if($projeto->ilustradores()->first() != null) {
                $projeto->ilustradores()->where('id_projeto', $id)->delete();
            }
            if($projeto->juris()->first() != null) {
                $projeto->juris()->where('id_projeto', $id)->delete();
            }
            if($projeto->professores()->first() != null) {
                $projeto->professores()->where('id_projeto', $id)->delete();
            }
            if($projeto->professoresFacul()->first() != null) {
                $projeto->professoresFacul()->where('id_projeto', $id)->delete();
            }
            if($projeto->rbes()->first() != null) {
                $projeto->rbes()->where('id_projeto', $id)->delete();
            }
            if($projeto->universidades()->first() != null) {
                $projeto->universidades()->where('id_projeto', $id)->delete();
            }
            if($projeto->contadores()->first() != null) {
                $projeto->contadores()->where('id_projeto', $id)->delete();
            }
            if($projeto->entidades()->first() != null) {
                $projeto->entidades()->where('id_projeto', $id)->delete();
            }
            if($projeto->escolas()->first() != null) {
                $projeto->escolas()->where('id_projeto', $id)->delete();
            }
            $projeto->delete();    
        }
        $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("dashboardAdmin");
            }
            else {
                return redirect()->route("dashboardColaborador");
            }
    }

    public function getProjetoPorId($id)
    {
        $projeto = DB::table('projeto')
        ->where('id_projeto', $id)->first();
        if($projeto != null) {
            return response()->json(array('sucesso' => true, 'projeto' => $projeto));
        }
    }
    
    public function gerirParticipantes($id) {
        $projeto = Projeto::find($id);

        \session(['id_projeto' => $id]);

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return view('admin/gerirParticipantesProjeto', ['title' => 'Projeto: '.$projeto->nome]);
        }
        else {
            return view('colaborador/gerirParticipantesProjeto', ['title' => 'Projeto: '.$projeto->nome]);
        }
    }

    public function getParticipantes() {
        $id = intval(\session('id_projeto'));
        $anoAtual = \intval(date("Y"));
        $id_projeto = \intval($id);

        $data = self::criarRespostaParticipantes($id_projeto, $anoAtual);

        return \json_encode($data);
    }

    public function criarRespostaParticipantes($id_projeto, $anoAtual) {
        $entidades = null;
        $escolas = null;
        $ilustradores = null;
        $contadores = null;
        $juris = null;
        $professores = null;
        $profsFacul = null;
        $rbes = null;
        $universidades = null;
        
        $entidades = self::getEntidadesDoProjeto($id_projeto, $anoAtual);
        $escolas = self::getEscolasDoProjeto($id_projeto, $anoAtual);
        $ilustradores = self::getIlustradoresDoProjeto($id_projeto, $anoAtual);
        $contadores = self::getContadoresDoProjeto($id_projeto, $anoAtual);
        $juris = self::getJurisDoProjeto($id_projeto, $anoAtual);
        $professores = self::getProfessoresDoProjeto($id_projeto, $anoAtual);
        $profsFacul = self::getProfessoresFacDoProjeto($id_projeto, $anoAtual);
        $rbes = self::getRbesDoProjeto($id_projeto, $anoAtual);
        $universidades = self::getUniversidadesDoProjeto($id_projeto, $anoAtual);    
        
        $data = array(
            'id_projeto' => $id_projeto,
            'ano' => $anoAtual,
            'entidades' => $entidades,
            'escolas' => $escolas,
            'ilustradores' => $ilustradores,
            'contadores' => $contadores,
            'juris' => $juris,
            'professores' => $professores,
            'profsFac' => $profsFacul,
            'rbes' => $rbes,
            'universidades' =>$universidades
        );

        return $data;
    }

    public function construirEntidadesComEmails($entidades) {
        $resposta = array();
                foreach($entidades as $ent) {
                    $emails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', $ent->id_colaborador)
                    ->get();
                            
                    $entidade = array(
                        "entidade" => $ent,
                        "emails" => $emails
                    );
                    array_push($resposta, $entidade);
                }
        return $resposta;  
    }

    public function getEntidadesDoProjeto($id_projeto, $ano)
    {
            $entidades = DB::table('entidade_oficial')
                        ->join('projeto_entidade', 'entidade_oficial.id_entidadeOficial', '=', 'projeto_entidade.id_entidadeOficial')
                        ->join('colaborador', 'entidade_oficial.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('entidade_oficial.id_entidadeOficial' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_entidade.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_entidade.id_projeto', '=', $id_projeto],
                            ['projeto_entidade.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();
            
            $resposta = array();
            if($entidades != null && count($entidades) > 0) {
                $resposta = self::construirEntidadesComEmails($entidades);    
            }
            
        return $resposta;
    }

    public function getEscolasDoProjeto($id_projeto, $ano)
    {
            $escolas = DB::table('escola_solidaria')
                        ->join('projeto_escola', 'escola_solidaria.id_escolaSolidaria', '=', 'projeto_escola.id_escolaSolidaria')
                        ->join('colaborador', 'escola_solidaria.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('escola_solidaria.id_escolaSolidaria' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_escola.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_escola.id_projeto', '=', $id_projeto],
                            ['projeto_escola.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();
            
            $resposta = array();
            if($escolas != null && count($escolas) > 0) {
                $resposta = self::construirEntidadesComEmails($escolas);    
            }
            
        return $resposta;
    }

    public function getIlustradoresDoProjeto($id_projeto, $ano)
    {
            $ilustradores = DB::table('ilustrador_solidario')
                        ->join('projeto_ilustrador', 'ilustrador_solidario.id_ilustradorSolidario', '=', 'projeto_ilustrador.id_ilustradorSolidario')
                        ->join('colaborador', 'ilustrador_solidario.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('ilustrador_solidario.id_ilustradorSolidario' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_ilustrador.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_ilustrador.id_projeto', '=', $id_projeto],
                            ['projeto_ilustrador.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();

            $resposta = array();
            if($ilustradores != null && count($ilustradores) > 0) {
                $resposta = self::construirEntidadesComEmails($ilustradores);    
            }
            
        return $resposta;
    }

    public function getContadoresDoProjeto($id_projeto, $ano)
    {
            $contadores = DB::table('contador_historias')
                    ->join('projeto_contador', 'contador_historias.id_contadorHistorias', '=', 'projeto_contador.id_contador')
                    ->join('colaborador', 'contador_historias.id_colaborador', '=', 'colaborador.id_colaborador')
                    ->select('contador_historias.id_contadorHistorias' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_contador.anoParticipacao', 'colaborador.id_colaborador')
                    ->where([
                        ['projeto_contador.id_projeto', '=', $id_projeto],
                        ['projeto_contador.anoParticipacao', '=', $ano],
                        ['colaborador.disponivel', '=', 0]
                        ])
                    ->get();

            $resposta = array();
            if($contadores != null && count($contadores) > 0) {
                $resposta = self::construirEntidadesComEmails($contadores);    
            } 
            
        return $resposta;
    }

    public function getJurisDoProjeto($id_projeto, $ano)
    {
            $juris = DB::table('juri')
                        ->join('projeto_juri', 'juri.id_juri', '=', 'projeto_juri.id_juri')
                        ->join('colaborador', 'juri.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('juri.id_juri' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_juri.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_juri.id_projeto', '=', $id_projeto],
                            ['projeto_juri.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();

            $resposta = array();
            if($juris != null && count($juris) > 0) {
                $resposta = self::construirEntidadesComEmails($juris);
            }

            
        return $resposta;
    }

    public function getProfessoresDoProjeto($id_projeto, $ano)
    {
            $professores = DB::table('professor')
                        ->join('projeto_professor', 'professor.id_professor', '=', 'projeto_professor.id_professor')
                        ->join('colaborador', 'professor.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('professor.id_professor' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_professor.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_professor.id_projeto', '=', $id_projeto],
                            ['projeto_professor.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();

            $resposta = array();
            if($professores != null && count($professores) > 0) {
                $resposta = self::construirEntidadesComEmails($professores);    
            }

        return $resposta;
    }

    public function getProfessoresFacDoProjeto($id_projeto, $ano)
    {
            $profsFac = DB::table('professor_faculdade')
                        ->join('projeto_prof_faculdade', 'professor_faculdade.id_professorFaculdade', '=', 'projeto_prof_faculdade.id_professorFaculdade')
                        ->join('colaborador', 'professor_faculdade.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('professor_faculdade.id_professorFaculdade' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_prof_faculdade.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_prof_faculdade.id_projeto', '=', $id_projeto],
                            ['projeto_prof_faculdade.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();
            
            $resposta = array();
            if($profsFac != null && count($profsFac) > 0) {
                $resposta = self::construirEntidadesComEmails($profsFac);   
            }

        
        return $resposta;
    }

    public function getRbesDoProjeto($id_projeto, $ano)
    {
            $rbes = DB::table('rbe')
                        ->join('projeto_rbe', 'rbe.id_rbe', '=', 'projeto_rbe.id_rbe')
                        ->join('colaborador', 'rbe.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('rbe.id_rbe' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'rbe.regiao', 'projeto_rbe.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_rbe.id_projeto', '=', $id_projeto],
                            ['projeto_rbe.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();

            $resposta = array();
            if($rbes != null && count($rbes) > 0) {
                $resposta = self::construirEntidadesComEmails($rbes);
            }
                        
        return $resposta;
    }

    public function getUniversidadesDoProjeto($id_projeto, $ano)
    {
            $universidades = DB::table('universidade')
                        ->join('projeto_universidade', 'universidade.id_universidade', '=', 'projeto_universidade.id_universidade')
                        ->join('colaborador', 'universidade.id_colaborador', '=', 'colaborador.id_colaborador')
                        ->select('universidade.id_universidade' , 'colaborador.nome', 'colaborador.telefone', 'colaborador.telemovel', 'projeto_universidade.anoParticipacao', 'colaborador.id_colaborador')
                        ->where([
                            ['projeto_universidade.id_projeto', '=', $id_projeto],
                            ['projeto_universidade.anoParticipacao', '=', $ano],
                            ['colaborador.disponivel', '=', 0]
                            ])
                        ->get();
            
            $resposta = array();
            if($universidades != null && count($universidades) > 0) {
                $resposta = self::construirEntidadesComEmails($universidades);   
            }
            
        return $resposta;
    }

    public function existeAssociacao($id_utilizador, $id_projeto) {
        
        $projeto = DB::table('projeto')
                    ->join('projeto_utilizador', 'projeto.id_projeto', '=', 'projeto_utilizador.id_projeto')
                    ->where([
                        ['projeto_utilizador.id_utilizador', '=', $id_utilizador],
                        ['projeto_utilizador.id_projeto', '=', $id_projeto]
                        ])
                    ->first();

        if($projeto != null) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getSemAssociacao($id) {
        $projetosReturn = array();
        
        $projetos = DB::table('projeto')
                    ->select('projeto.id_projeto', 'projeto.nome', 'projeto.observacoes')
                    ->get();

        foreach($projetos as $proj) {
            $existe = self::existeAssociacao($id, $proj->id_projeto);
            if($existe == false) {
                array_push($projetosReturn, $proj);
            }
        }
        
        return \json_encode($projetosReturn);
    }
}
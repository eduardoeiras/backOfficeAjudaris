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
                    ->select('projeto.id_projeto' , 'projeto.nome', 'projeto.objetivos', 'projeto.publicoAlvo', 'projeto.observacoes')
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
        $projeto->regulamento = $request->urlFicheiro;
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

        $data = self::criarRespostaParticipantes($id_projeto, $anoAtual, null);

        return \json_encode($data);
    }

    public function criarRespostaParticipantes($id_projeto, $anoAtual, $pesq) {
        $entidades = null;
        $escolas = null;
        $ilustradores = null;
        $contadores = null;
        $juris = null;
        $professores = null;
        $profsFacul = null;
        $rbes = null;
        $universidades = null;
        
        if($pesq != null) {
            $entidades = self::getEntidadesDoProjeto($id_projeto, $anoAtual, $pesq);
            $escolas = self::getEscolasDoProjeto($id_projeto, $anoAtual, $pesq);
            $ilustradores = self::getIlustradoresDoProjeto($id_projeto, $anoAtual, $pesq);
            $contadores = self::getContadoresDoProjeto($id_projeto, $anoAtual, $pesq);
            $juris = self::getJurisDoProjeto($id_projeto, $anoAtual, $pesq);
            $professores = self::getProfessoresDoProjeto($id_projeto, $anoAtual, $pesq);
            $profsFacul = self::getProfessoresFacDoProjeto($id_projeto, $anoAtual, $pesq);
            $rbes = self::getRbesDoProjeto($id_projeto, $anoAtual, $pesq);
            $universidades = self::getUniversidadesDoProjeto($id_projeto, $anoAtual, $pesq);
        }
        else {
            $entidades = self::getEntidadesDoProjeto($id_projeto, $anoAtual, null);
            $escolas = self::getEscolasDoProjeto($id_projeto, $anoAtual, null);
            $ilustradores = self::getIlustradoresDoProjeto($id_projeto, $anoAtual, null);
            $contadores = self::getContadoresDoProjeto($id_projeto, $anoAtual, null);
            $juris = self::getJurisDoProjeto($id_projeto, $anoAtual, null);
            $professores = self::getProfessoresDoProjeto($id_projeto, $anoAtual, null);
            $profsFacul = self::getProfessoresFacDoProjeto($id_projeto, $anoAtual, null);
            $rbes = self::getRbesDoProjeto($id_projeto, $anoAtual, null);
            $universidades = self::getUniversidadesDoProjeto($id_projeto, $anoAtual, null);    
        }
        
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

    public function getEntidadesDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $entidades = DB::table('entidade_oficial')
                        ->join('projeto_entidade', 'entidade_oficial.id_entidadeOficial', '=', 'projeto_entidade.id_entidadeOficial')
                        ->select('entidade_oficial.id_entidadeOficial' , 'entidade_oficial.nome', 'entidade_oficial.telefone', 'entidade_oficial.telemovel', 'entidade_oficial.email', 'projeto_entidade.anoParticipacao')
                        ->where([
                            ['projeto_entidade.id_projeto', '=', $id_projeto],
                            ['projeto_entidade.anoParticipacao', '=', $ano],
                            ['entidade_oficial.nome', 'LIKE', '%'.$pesq.'%'],
                            ['entidade_oficial.disponivel', '=', 0]
                            ])
                        ->get();  
        }
        else {
            $entidades = DB::table('entidade_oficial')
                        ->join('projeto_entidade', 'entidade_oficial.id_entidadeOficial', '=', 'projeto_entidade.id_entidadeOficial')
                        ->select('entidade_oficial.id_entidadeOficial' , 'entidade_oficial.nome', 'entidade_oficial.telefone', 'entidade_oficial.telemovel', 'entidade_oficial.email', 'projeto_entidade.anoParticipacao')
                        ->where([
                            ['projeto_entidade.id_projeto', '=', $id_projeto],
                            ['projeto_entidade.anoParticipacao', '=', $ano],
                            ['entidade_oficial.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        return $entidades;
    }

    public function getEscolasDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $escolas = DB::table('escola_solidaria')
                        ->join('projeto_escola', 'escola_solidaria.id_escolaSolidaria', '=', 'projeto_escola.id_escolaSolidaria')
                        ->select('escola_solidaria.id_escolaSolidaria' , 'escola_solidaria.nome', 'escola_solidaria.telefone', 'escola_solidaria.telemovel', 'projeto_escola.anoParticipacao')
                        ->where([
                            ['projeto_escola.id_projeto', '=', $id_projeto],
                            ['projeto_escola.anoParticipacao', '=', $ano],
                            ['escola_solidaria.nome', 'LIKE', '%'.$pesq.'%'],
                            ['escola_solidaria.disponivel', '=', 0]
                            ])
                        ->get();
        }
        else {
            $escolas = DB::table('escola_solidaria')
                        ->join('projeto_escola', 'escola_solidaria.id_escolaSolidaria', '=', 'projeto_escola.id_escolaSolidaria')
                        ->select('escola_solidaria.id_escolaSolidaria' , 'escola_solidaria.nome', 'escola_solidaria.telefone', 'escola_solidaria.telemovel', 'projeto_escola.anoParticipacao')
                        ->where([
                            ['projeto_escola.id_projeto', '=', $id_projeto],
                            ['projeto_escola.anoParticipacao', '=', $ano],
                            ['escola_solidaria.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        
        return $escolas;
    }

    public function getIlustradoresDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $ilustradores = DB::table('ilustrador_solidario')
                    ->join('projeto_ilustrador', 'ilustrador_solidario.id_ilustradorSolidario', '=', 'projeto_ilustrador.id_ilustradorSolidario')
                    ->select('ilustrador_solidario.id_ilustradorSolidario' , 'ilustrador_solidario.nome', 'ilustrador_solidario.telefone', 'ilustrador_solidario.telemovel', 'ilustrador_solidario.email', 'projeto_ilustrador.anoParticipacao')
                    ->where([
                        ['projeto_ilustrador.id_projeto', '=', $id_projeto],
                        ['projeto_ilustrador.anoParticipacao', '=', $ano],
                        ['ilustrador_solidario.nome', 'LIKE', '%'.$pesq.'%'],
                        ['ilustrador_solidario.disponivel', '=', 0]
                        ])
                    ->get();
        }
        else {
            $ilustradores = DB::table('ilustrador_solidario')
                        ->join('projeto_ilustrador', 'ilustrador_solidario.id_ilustradorSolidario', '=', 'projeto_ilustrador.id_ilustradorSolidario')
                        ->select('ilustrador_solidario.id_ilustradorSolidario' , 'ilustrador_solidario.nome', 'ilustrador_solidario.telefone', 'ilustrador_solidario.telemovel', 'ilustrador_solidario.email', 'projeto_ilustrador.anoParticipacao')
                        ->where([
                            ['projeto_ilustrador.id_projeto', '=', $id_projeto],
                            ['projeto_ilustrador.anoParticipacao', '=', $ano],
                            ['ilustrador_solidario.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        
        return $ilustradores;
    }

    public function getContadoresDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $contadores = DB::table('contador_historias')
                    ->join('projeto_contador', 'contador_historias.id_contadorHistorias', '=', 'projeto_contador.id_contador')
                    ->select('contador_historias.id_contadorHistorias' , 'contador_historias.nome', 'contador_historias.telefone', 'contador_historias.telemovel', 'contador_historias.email', 'projeto_contador.anoParticipacao')
                    ->where([
                        ['projeto_contador.id_projeto', '=', $id_projeto],
                        ['projeto_contador.anoParticipacao', '=', $ano],
                        ['contador_historias.nome', 'LIKE', '%'.$pesq.'%'],
                        ['contador_historias.disponivel', '=', 0]
                        ])
                    ->get(); 
        }
        else {
            $contadores = DB::table('contador_historias')
                    ->join('projeto_contador', 'contador_historias.id_contadorHistorias', '=', 'projeto_contador.id_contador')
                    ->select('contador_historias.id_contadorHistorias' , 'contador_historias.nome', 'contador_historias.telefone', 'contador_historias.telemovel', 'contador_historias.email', 'projeto_contador.anoParticipacao')
                    ->where([
                        ['projeto_contador.id_projeto', '=', $id_projeto],
                        ['projeto_contador.anoParticipacao', '=', $ano],
                        ['contador_historias.disponivel', '=', 0]
                        ])
                    ->get();  
        }
       
        
        return $contadores;
    }

    public function getJurisDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $juris = DB::table('juri')
            ->join('projeto_juri', 'juri.id_juri', '=', 'projeto_juri.id_juri')
            ->select('juri.id_juri' , 'juri.nome', 'juri.telefone', 'juri.telemovel', 'juri.email', 'projeto_juri.anoParticipacao')
            ->where([
                ['projeto_juri.id_projeto', '=', $id_projeto],
                ['projeto_juri.anoParticipacao', '=', $ano],
                ['juri.nome', 'LIKE', '%'.$pesq.'%'],
                ['juri.disponivel', '=', 0]
                ])
            ->get();
        }
        else {
            $juris = DB::table('juri')
                        ->join('projeto_juri', 'juri.id_juri', '=', 'projeto_juri.id_juri')
                        ->select('juri.id_juri' , 'juri.nome', 'juri.telefone', 'juri.telemovel', 'juri.email', 'projeto_juri.anoParticipacao')
                        ->where([
                            ['projeto_juri.id_projeto', '=', $id_projeto],
                            ['projeto_juri.anoParticipacao', '=', $ano],
                            ['juri.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        
        return $juris;
    }

    public function getProfessoresDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $professores = DB::table('professor')
                    ->join('projeto_professor', 'professor.id_professor', '=', 'projeto_professor.id_professor')
                    ->select('professor.id_professor' , 'professor.nome', 'professor.telefone', 'professor.telemovel', 'professor.email', 'projeto_professor.anoParticipacao')
                    ->where([
                        ['projeto_professor.id_projeto', '=', $id_projeto],
                        ['projeto_professor.anoParticipacao', '=', $ano],
                        ['professor.nome', 'LIKE', '%'.$pesq.'%'],
                        ['professor.disponivel', '=', 0]
                        ])
                    ->get();
        }
        else {
            $professores = DB::table('professor')
                        ->join('projeto_professor', 'professor.id_professor', '=', 'projeto_professor.id_professor')
                        ->select('professor.id_professor' , 'professor.nome', 'professor.telefone', 'professor.telemovel', 'professor.email', 'projeto_professor.anoParticipacao')
                        ->where([
                            ['projeto_professor.id_projeto', '=', $id_projeto],
                            ['projeto_professor.anoParticipacao', '=', $ano],
                            ['professor.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        
        return $professores;
    }

    public function getProfessoresFacDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $profsFac = DB::table('professor_faculdade')
                    ->join('projeto_prof_faculdade', 'professor_faculdade.id_professorFaculdade', '=', 'projeto_prof_faculdade.id_professorFaculdade')
                    ->select('professor_faculdade.id_professorFaculdade' , 'professor_faculdade.nome', 'professor_faculdade.telefone', 'professor_faculdade.telemovel', 'professor_faculdade.email', 'projeto_prof_faculdade.anoParticipacao')
                    ->where([
                        ['projeto_prof_faculdade.id_projeto', '=', $id_projeto],
                        ['projeto_prof_faculdade.anoParticipacao', '=', $ano],
                        ['professor_faculdade.nome', 'LIKE', '%'.$pesq.'%'],
                        ['professor_faculdade.disponivel', '=', 0]
                        ])
                    ->get();
        }
        else {
            $profsFac = DB::table('professor_faculdade')
                        ->join('projeto_prof_faculdade', 'professor_faculdade.id_professorFaculdade', '=', 'projeto_prof_faculdade.id_professorFaculdade')
                        ->select('professor_faculdade.id_professorFaculdade' , 'professor_faculdade.nome', 'professor_faculdade.telefone', 'professor_faculdade.telemovel', 'professor_faculdade.email', 'projeto_prof_faculdade.anoParticipacao')
                        ->where([
                            ['projeto_prof_faculdade.id_projeto', '=', $id_projeto],
                            ['projeto_prof_faculdade.anoParticipacao', '=', $ano],
                            ['professor_faculdade.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        
        return $profsFac;
    }

    public function getRbesDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $rbes = DB::table('rbe')
                    ->join('projeto_rbe', 'rbe.id_rbe', '=', 'projeto_rbe.id_rbe')
                    ->select('rbe.id_rbe' , 'rbe.nomeCoordenador', 'rbe.regiao', 'projeto_rbe.anoParticipacao')
                    ->where([
                        ['projeto_rbe.id_projeto', '=', $id_projeto],
                        ['projeto_rbe.anoParticipacao', '=', $ano],
                        ['rbe.nomeCoordenador', 'LIKE', '%'.$pesq.'%'],
                        ['rbe.disponivel', '=', 0]
                        ])
                    ->get();
        }
        else {
            $rbes = DB::table('rbe')
                        ->join('projeto_rbe', 'rbe.id_rbe', '=', 'projeto_rbe.id_rbe')
                        ->select('rbe.id_rbe' , 'rbe.nomeCoordenador', 'rbe.regiao', 'projeto_rbe.anoParticipacao')
                        ->where([
                            ['projeto_rbe.id_projeto', '=', $id_projeto],
                            ['projeto_rbe.anoParticipacao', '=', $ano],
                            ['rbe.disponivel', '=', 0]
                            ])
                        ->get();    
        }
        
        return $rbes;
    }

    public function getUniversidadesDoProjeto($id_projeto, $ano, $pesq)
    {
        if($pesq != null) {
            $universidades = DB::table('universidade')
                    ->join('projeto_universidade', 'universidade.id_universidade', '=', 'projeto_universidade.id_universidade')
                    ->select('universidade.id_universidade' , 'universidade.nome', 'universidade.telefone', 'universidade.telemovel', 'universidade.email', 'projeto_universidade.anoParticipacao')
                    ->where([
                        ['projeto_universidade.id_projeto', '=', $id_projeto],
                        ['projeto_universidade.anoParticipacao', '=', $ano],
                        ['universidade.nome', 'LIKE', '%'.$pesq.'%'],
                        ['universidade.disponivel', '=', 0]
                        ])
                    ->get();
        }
        else {
            $universidades = DB::table('universidade')
                        ->join('projeto_universidade', 'universidade.id_universidade', '=', 'projeto_universidade.id_universidade')
                        ->select('universidade.id_universidade' , 'universidade.nome', 'universidade.telefone', 'universidade.telemovel', 'universidade.email', 'projeto_universidade.anoParticipacao')
                        ->where([
                            ['projeto_universidade.id_projeto', '=', $id_projeto],
                            ['projeto_universidade.anoParticipacao', '=', $ano],
                            ['universidade.disponivel', '=', 0]
                            ])
                        ->get();    
        }

        return $universidades;
    }

    public function participantesPesq($tipo, $ano, $pesq) {
        $id = \session('id_projeto');

        if($tipo != 'todos') {
            if($pesq != 'null') {
                $data = self::criarRespostaPesquisa($id, $ano, $tipo, $pesq);
            }
            else {
                $data = self::criarRespostaPesquisa($id, $ano, $tipo, null);
            }
        }
        else {
            if($pesq != 'null') {
                $data = self::criarRespostaParticipantes($id, $ano, $pesq);
            }
            else {
                $data = self::criarRespostaParticipantes($id, $ano, null);    
            }
        }

        return json_encode($data);
    }

    public function criarRespostaPesquisa($id_projeto, $anoAtual, $tipo, $pesq) {
        $entidades = null;
        $escolas = null;
        $ilustradores = null;
        $contadores = null;
        $juris = null;
        $professores = null;
        $profsFacul = null;
        $rbes = null;
        $universidades = null;

        switch($tipo) {
            case 'ilustrador_solidario':
                if($pesq != null) {
                    $ilustradores = self::getIlustradoresDoProjeto($id_projeto, $anoAtual, $pesq);
                } 
                else {
                    $ilustradores = self::getIlustradoresDoProjeto($id_projeto, $anoAtual, $pesq);        
                }
            break;
            case 'contador_historias':
                if($pesq != null) {
                    $contadores = self::getContadoresDoProjeto($id_projeto, $anoAtual, $pesq);  
                }
                else {
                    $contadores = self::getContadoresDoProjeto($id_projeto, $anoAtual, $pesq);    
                }
            break;
            case 'entidade_oficial':
                if($pesq != null) {
                    $entidades = self::getEntidadesDoProjeto($id_projeto, $anoAtual, $pesq);
                }
                else {
                    $entidades = self::getEntidadesDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'escola_solidaria':
                if($pesq != null) {
                    $escolas = self::getEscolasDoProjeto($id_projeto, $anoAtual, $pesq);
                }
                else {
                    $escolas = self::getEscolasDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'juri':
                if($pesq != null) {
                    $juris = self::getJurisDoProjeto($id_projeto, $anoAtual, $pesq);
                }
                else {
                    $juris = self::getJurisDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'professor':
                if($pesq != null) {
                    $professores = self::getProfessoresDoProjeto($id_projeto, $anoAtual, $pesq);
                } 
                else {
                    $professores = self::getProfessoresDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'professor_faculdade':
                if($pesq != null) {
                    $profsFacul = self::getProfessoresFacDoProjeto($id_projeto, $anoAtual, $pesq);
                }
                else {
                    $profsFacul = self::getProfessoresFacDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'rbe':
                if($pesq != null) {
                    $rbes = self::getRbesDoProjeto($id_projeto, $anoAtual, $pesq);
                }
                else {
                    $rbes = self::getRbesDoProjeto($id_projeto, $anoAtual, $pesq);
                }
            break;
            case 'universidade':
                if($pesq != null) {
                    $universidades = self::getUniversidadesDoProjeto($id_projeto, $anoAtual, $pesq); 
                }
                else {
                    $universidades = self::getUniversidadesDoProjeto($id_projeto, $anoAtual, $pesq);    
                }
            break;
        }
        
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
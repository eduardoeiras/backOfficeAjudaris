<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use Illuminate\Http\Request;
use DB;
use DataTables;

class UtilizadorController extends Controller
{
    
    public function index()
    {
        $user = session()->get('utilizador');
        return view('admin.utilizadores', ['data' => $user->nomeUtilizador]);
    }

    public function store(Request $request)
    {
        $nomeUtilizador = $request->get("nomeUtilizador");
        $nome = $request->get("nome");
        $password = $request->get("password");
        $departamento = $request->get("departamento");
        $tipoUtilizador = \intval($request->get("tipoUtilizador"));
        $telefone = $request->get("telefone");
        $telemovel = $request->get("telemovel");
        $email = $request->get("email");

        $utilizador = self::getUserNome($nomeUtilizador);
        if($utilizador == null) {
            $user = new Utilizador();
            
            $user->nomeUtilizador = $nomeUtilizador;   
            $user->nome = $nome;  
            $user->password = $password;  
            $user->departamento = $departamento;  
            $user->tipoUtilizador = $tipoUtilizador;  
            $user->telefone = $telefone;  
            $user->telemovel = $telemovel;  
            $user->email = $email; 

            $user->save();

            return redirect()->route("utilizadores");
        }
        else {
            $msg = 'O nome de utilizador, '.$nomeUtilizador.', já está atribuido!';
            return view('admin.utilizadores', ['msg' => $msg]);
        }
    }

    public function update($id, Request $request)
    {
        $id_utilizador = \intval($id);
        $nomeUtilizador = $request->get("nomeUtilizador");
        $nome = $request->get("nome");
        $password = $request->get("password");
        $departamento = $request->get("departamento");
        $tipoUtilizador = \intval($request->get("tipoUtilizador"));
        $telefone = $request->get("telefone");
        $telemovel = $request->get("telemovel");
        $email = $request->get("email");
        
        $user = Utilizador::find($id_utilizador);
        if($user != null) {
            $user->nomeUtilizador = $nomeUtilizador;   
            $user->nome = $nome;  
            $user->password = $password;  
            $user->departamento = $departamento;  
            $user->tipoUtilizador = $tipoUtilizador;  
            $user->telefone = $telefone;  
            $user->telemovel = $telemovel;  
            $user->email = $email; 

            $user->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("utilizadores");
            }
            else {
                return redirect()->route("utilizadoresColaborador");
            }
        }
    }

    public function destroy($id)
    {
        $utilizador = Utilizador::find($id);
        $utilizador->delete();
        return redirect()->route("utilizadores");
    }

    public function realizarLogin()
    {
        $user = session()->get('utilizador');
        if(isset($user)) {
            //Redirecionar para a respetiva página de utilizador
            if($user->tipoUtilizador == 0) {
                return redirect()->route('dashboardAdmin');
            }
            else {
                return redirect()->route("dashboardColaborador");
            } 
        }

        $nomeUtilizador = $_POST["nome"];
        $password = $_POST["password"];

        $user = self::getUserNome($nomeUtilizador);

        if($user != null) {
            if($user->password == $password) {
                $sessionUser = DB::table('utilizador')->select("id_utilizador", "nomeUtilizador", "nome", "tipoUtilizador", "departamento", "email")
                    ->where('nomeUtilizador', $user->nomeUtilizador)->first();
                session()->put("utilizador", $sessionUser);
                if($user->tipoUtilizador == 0) {
                    return redirect()->route('dashboardAdmin');
                }
                else {
                    return redirect()->route("dashboardColaborador");
                }   
            }
            else {
                return redirect()->route("paginaLoginErro", ['msg' => 'Não existe nenhuma conta com a combinação do nome de utilizador e password inseridos!']);
            }
        }
        else {
            return redirect()->route("paginaLoginErro",  ['msg' => 'Não existe nenhuma conta com a combinação do nome de utilizador e password inseridos!']);
        }
    }
    public function realizarLogout()
    {
        session()->flush();
        return redirect()->route('paginaLogin');
    }

    public function getUserId($id)
    {
        return ['user' => User::findOrFail($id)];
    }

    public function getUserNome($nomeUtilizador)
    {
        $user = DB::table('utilizador')->where('nomeUtilizador', $nomeUtilizador)->first();
        return $user;

    }

    public function getUserPorId($id) {
        
        $user = DB::table('utilizador')->where('id_utilizador', $id)->first();
        if($user != null) {
            return response()->json($user);  
        }
        else {
            return null;
        }
        
    }

    public function getAll() {

        $utilizadores = DB::table('utilizador')
        ->select('utilizador.*');

        return Datatables::of($utilizadores)
            ->editColumn('tipoUtilizador', function ($model) {
                if($model->tipoUtilizador == 0) {
                    return 'Administrador';
                }
                else {
                    return 'Colaborador';
                }
            })
            ->addColumn('opcoes', function($model){
                $user = session()->get('utilizador');
                if(intval($model->tipoUtilizador) == 0) {
                    if($user->nomeUtilizador == $model->nomeUtilizador) {
                        $btns = '
                        <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$model->id_utilizador.', true)"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Edit">&#xE254;</i></a>
                        ';
                    }
                    else {
                        $btns = '
                        <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$model->id_utilizador.', false)"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Edit">&#xE254;</i></a>
                        <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$model->id_utilizador.')"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Delete">&#xE872;</i></a>
                        ';
                    }
                }
                else {
                    $url = 'gerirProjetosUser/'.$model->id_utilizador;
                    $btns = '
                    <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$model->id_utilizador.', false)"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$model->id_utilizador.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>
                    <a href="'.$url.'"><img src="http://backofficeAjudaris/images/projetos.png"></img></a>
                    ';

                }
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true); 

    }

    public function existeUser($name) {
        $user = DB::table('utilizador')->where('nomeUtilizador', $name)->first();
        if($user != null) {
            return 1; 
        }
        else {
            return 0;
        }
    }

    public function gerirProjetosUser($id) {
        $utilizador = Utilizador::find($id);
        if($utilizador != null) {
            \session(['id_utilizador' => $id]);

            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return view('admin/gerirProjetosUtilizador', ['title' => 'Utilizador: '.$utilizador->nomeUtilizador]); 
            }
            else {
                return view('colaborador/gerirProjetosUtilizador', ['title' => 'Utilizador: '.$utilizador->nomeUtilizador]); 
            }  
        }
    }
}

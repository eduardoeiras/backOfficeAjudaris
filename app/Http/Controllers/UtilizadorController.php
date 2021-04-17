<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use Illuminate\Http\Request;
use DB;
use SoulDoit\DataTable\SSP;

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
            ['label'=>'Tipo de Utilizador', 'db'=>'tipoUtilizador', 'dt'=>7, 'formatter'=>function($value, $model){
                if($value == 0) {
                    return 'Administrador';
                }
                else {
                    return 'Colaborador';
                }
            }],
            ['label'=>'Opções', 'db'=>'id_utilizador', 'dt'=>8, 'formatter'=>function($value, $model){ 
                $user = session()->get('utilizador');
                if(intval($model["tipoUtilizador"]) == 0) {
                    if($user->nomeUtilizador == $model["nomeUtilizador"]) {
                        $btns = ['<td>
                        <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$value.', true)"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Edit">&#xE254;</i></a>
                        <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$value.')"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Delete">&#xE872;</i></a>
                        </td>'];
                    }
                    else {
                        $btns = ['<td>
                        <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$value.', false)"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Edit">&#xE254;</i></a>
                        <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$value.')"><i
                                class="material-icons" data-toggle="tooltip"
                                title="Delete">&#xE872;</i></a>
                        </td>'];
                    }
                }
                else {
                    $url = 'gerirProjetosUser/'.$value;
                    $btns = ['<td>
                    <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador('.$value.', false)"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Edit">&#xE254;</i></a>
                    <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador('.$value.')"><i
                            class="material-icons" data-toggle="tooltip"
                            title="Delete">&#xE872;</i></a>
                    <a href="'.$url.'"><img src="http://backofficeAjudaris/images/projetos.png"></img></a>
                    </td>'];

                }
                return implode(" ", $btns); 
            }],
        ];
        $dt_obj = new SSP('App\Models\Utilizador', $dt);

        echo json_encode($dt_obj->getDtArr());
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

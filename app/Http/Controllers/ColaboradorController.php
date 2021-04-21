<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use Illuminate\Http\Request;
use DB;
use DataTables;

class ColaboradorController extends Controller
{

    public static function create($nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal,
    $codPostalRua, $rua, $localidade, $distrito, $emails) {
        $colaborador = new Colaborador();

        $colaborador->nome = $nome;
        $colaborador->telefone = $telefone;
        $colaborador->telemovel = $telemovel;
        $colaborador->numPorta = $numPorta;
        $colaborador->disponivel = $disponibilidade;
        $colaborador->observacoes = $observacoes;
        
        if($codPostal != null && $codPostalRua != null) {
            $cod_postal = CodPostal::find($codPostal);
            $cod_postal_rua = DB::table('cod_postal_rua')
                                        ->where([
                                            ['cod_postal_rua.codPostal', '=', $codPostal],
                                            ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                            ]);

            ColaboradorController::updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito);
            ColaboradorController::updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua);
        }
        else {
            $colaborador->codPostal = ' ';
            $colaborador->codPostalRua = ' ';
        }
    
        $colaborador->save();

        $idColab = ColaboradorController::getLastId()[0]->id_colaborador;
        
        if($emails != null) {
            foreach($emails as $email) {
                $newEmail = new Email();
                $newEmail->email = $email;
                $newEmail->id_colaborador = $idColab;
                $newEmail->save();   
            }    
        }
        

        return $idColab;
    }

    public static function update($idColaborador, $nome, $observacoes, $telemovel, $telefone, $numPorta, $disponibilidade, $codPostal,
    $codPostalRua, $rua, $localidade, $distrito, $emails, $emailsToDelete) {
        $colaborador = Colaborador::find($idColaborador);
        
        if($colaborador != null) {
            $colaborador->telefone = $telefone;
            $colaborador->telemovel = $telemovel;
            $colaborador->disponivel = $disponibilidade;
            $colaborador->nome = $nome;
            $colaborador->numPorta = $numPorta;
            $colaborador->observacoes = $observacoes;
            
            if($emails != null) {
                foreach($emails as $email) {
                    $existeEmail = ColaboradorController::existeEmail($email, $colaborador->id_colaborador);
                    if(!$existeEmail) {
                        $newEmail = new Email();
                        $newEmail->email = $email;
                        $newEmail->id_colaborador = $colaborador->id_colaborador;
                        $newEmail->save();
                    }  
                }    
            }
            if($emailsToDelete != null) {
                foreach($emailsToDelete as $email) {
                    $query = DB::table('email')
                        ->where('email.email', '=', $email);

                    if($query != null) {
                        $query->delete();
                    }  
                } 
            }

            if($codPostal != null && $codPostalRua != null) {
                $cod_postal = CodPostal::find($codPostal);
                $cod_postal_rua = DB::table('cod_postal_rua')
                                        ->where([
                                            ['cod_postal_rua.codPostal', '=', $codPostal],
                                            ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                            ]); 
                ColaboradorController::updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito);
                ColaboradorController::updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua);
            }
            else {
                $colaborador->codPostal = ' ';
                $colaborador->codPostalRua = ' ';
            }

            $colaborador->save();
        }
    }

    public static function edit($idColaborador, Request $request) {
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

        $colaborador = Colaborador::find($idColaborador);

        if($colaborador != null) {
            $colaborador->telefone = $telefone;
            $colaborador->telemovel = $telemovel;
            $colaborador->disponivel = $disponibilidade;
            $colaborador->nome = $nome;
            $colaborador->numPorta = $numPorta;
            $colaborador->observacoes = $observacoes;
            
            if($emails != null) {
                foreach($emails as $email) {
                    $existeEmail = ColaboradorController::existeEmail($email, $colaborador->id_colaborador);
                    if(!$existeEmail) {
                        $newEmail = new Email();
                        $newEmail->email = $email;
                        $newEmail->id_colaborador = $colaborador->id_colaborador;
                        $newEmail->save();
                    }  
                }    
            }
            if($emailsToDelete != null) {
                foreach($emailsToDelete as $email) {
                    $query = DB::table('email')
                        ->where('email.email', '=', $email);

                    if($query != null) {
                        $query->delete();
                    }  
                } 
            }

            if($codPostal != null && $codPostalRua != null) {
                $cod_postal = CodPostal::find($codPostal);
                $cod_postal_rua = DB::table('cod_postal_rua')
                                        ->where([
                                            ['cod_postal_rua.codPostal', '=', $codPostal],
                                            ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                            ]);
    
                ColaboradorController::updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito);
                ColaboradorController::updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua);
            }
            else {
                $colaborador->codPostal = ' ';
                $colaborador->codPostalRua = ' ';
            }

            $colaborador->save();
            
            $user = session()->get("utilizador");
            if($user->tipoUtilizador == 0) {
                return redirect()->route("pesquisaGeral");
            }
            else {
                return redirect()->route("pesquisaGeralColaborador");
            }
        }
    }

    public static function delete($idColaborador) {
        $colaborador = Colaborador::find($idColaborador);
        if($colaborador->codPostal != null){
            $colaborador->codPostal = null;
        }
        if($colaborador->codPostalRua != null){
            $colaborador->codPostalRua = null;    
        }
        $colaborador->save();
        if($colaborador->comunicacoes()->first() != null) {
            $colaborador->comunicacoes()->where('id_colaborador', $idColaborador)->delete();
        }
        if($colaborador->emails()->first() != null) {
            $colaborador->emails()->where('id_colaborador', $idColaborador)->delete();
        } 
        $colaborador->delete();
    }

    public static function getEmails($id)
    {
        $emails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($id))
                    ->get();
        
        if($emails != null) {
           return response()->json($emails); 
        }
        else {
            return null;
        }
    }

    public static function existeEmail($email, $id_colaborador)
    {
        $email = DB::table('email')
                    ->where('email.email', '=', "$email")
                    ->where('email.id_colaborador', '=', $id_colaborador)
                    ->first();
        
        if($email != null) {
           return true; 
        }
        else {
            return false;
        }
    }

    public static function existeEmailSemColaborador($email)
    {
        $email = DB::table('email')
                    ->where('email.email', '=', "$email")
                    ->first();
        
        if($email != null) {
           return 1; 
        }
        else {
            return 0;
        }
    }

    public static function getLastId()
    {
        $id = DB::select('SELECT MAX(id_colaborador) as id_colaborador FROM colaborador');
        
        if($id != null) {
           return $id; 
        }
        else {
            return null;
        }
    }

    public static function updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito) {
        if($cod_postal != null) {
            $cod_postal->localidade = $localidade;
            $cod_postal->distrito = $distrito;
            $cod_postal->save();
            $colaborador->codPostal = $codPostal; 
        }
        else {
            $novoCodPostal = new CodPostal();
            $novoCodPostal->codPostal = $codPostal;
            $novoCodPostal->localidade = $localidade;
            $novoCodPostal->distrito = $distrito;
            $novoCodPostal->save();
            $colaborador->codPostal = $codPostal;
        }
    }

    public static function updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua) {
        if($cod_postal_rua->first() != null) {
            $cod_postal_rua->update(['rua' => $rua]);
            $colaborador->codPostalRua = $codPostalRua;
        }
        else {
            $novoCodPostalRua = new CodPostalRua();
            $novoCodPostalRua->codPostal = $codPostal;
            $novoCodPostalRua->codPostalRua = $codPostalRua;
            $novoCodPostalRua->rua = $rua;
            $novoCodPostalRua->save();
            $colaborador->codPostalRua = $codPostalRua;
        }
    }

    public function getPorId($id) {

        $colaborador = Colaborador::find($id);

        $codPostal = CodPostal::find($colaborador->codPostal);
        $codPostalRua = DB::table('cod_postal_rua')
            ->where([
                ['cod_postal_rua.codPostal', '=', $colaborador->codPostal],
                ['cod_postal_rua.codPostalRua', '=', $colaborador->codPostalRua],
                ])->first();
        
        $emails = ColaboradorController::getEmails($colaborador->id_colaborador);

        $resColaborador = array(
            "id_colaborador" => $colaborador->id_colaborador,
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
        
        if($resColaborador != null) {
            return response()->json($resColaborador);
        }
        else{
            return null;
        }
    }

    function getColaboradores() {

        $colaboradores = DB::table(DB::raw('colaborador', 'cod_postal', 'cod_postal_rua'))
        ->join('cod_postal', 'colaborador.codPostal', '=' ,'cod_postal.codPostal')
        ->join('cod_postal_rua', 'colaborador.codPostalRua', '=' ,'cod_postal_rua.codPostalRua')
        ->select('colaborador.*', 'cod_postal.localidade', 'cod_postal.distrito', 'cod_postal_rua.rua')
        ->whereRaw('cod_postal_rua.codPostal = cod_postal.codPostal');

        return Datatables::of($colaboradores)
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
                $btns = '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$model->id_colaborador.')"><i
                class="material-icons" data-toggle="tooltip"
                title="Edit">&#xE254;</i></a>';
                
                return $btns;
         })
            ->rawColumns(['opcoes'])
            ->make(true);

    }
}
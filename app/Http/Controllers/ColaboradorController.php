<?php

namespace App\Http\Controllers;

use App\Models\CodPostal;
use App\Models\CodPostalRua;
use App\Models\Colaborador;
use App\Models\Email;
use DB;
use SoulDoit\DataTable\SSP;

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
        
        $cod_postal = CodPostal::find($codPostal);
        $cod_postal_rua = DB::table('cod_postal_rua')
                                    ->where([
                                        ['cod_postal_rua.codPostal', '=', $codPostal],
                                        ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                        ]);

        ColaboradorController::updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito);
        ColaboradorController::updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua);

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

        $cod_postal = CodPostal::find($codPostal);
        $cod_postal_rua = DB::table('cod_postal_rua')
                                    ->where([
                                        ['cod_postal_rua.codPostal', '=', $codPostal],
                                        ['cod_postal_rua.codPostalRua', '=', $codPostalRua],
                                        ]);

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
            
            ColaboradorController::updateCodPostal($colaborador, $cod_postal, $codPostal, $localidade, $distrito);
            ColaboradorController::updateCodPostalRua($colaborador, $cod_postal_rua, $codPostalRua, $codPostal, $rua);

            $colaborador->save();
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

    function getColaboradores() {
        $dt = [
            ['label'=>'Nome', 'db'=>'nome', 'dt'=>0],
            ['label'=>'Telefone', 'db'=>'telefone', 'dt'=>1, 'formatter'=>function($value, $model){
                if($value == null) {
                    return ' ---- ';
                }
                else {
                    return $value;
                }
            }],
            ['label'=>'Telemovel', 'db'=>'telemovel', 'dt'=>2, 'formatter'=>function($value, $model){
                if($value == null) {
                    return ' --- ';
                }
                else {
                    return $value;
                }
            }],
            ['label'=>'Emails', 'db'=>'id_colaborador', 'dt'=>3, 'formatter'=>function($value, $model){
                $colabEmails = DB::table('email')
                    ->join('colaborador', 'email.id_colaborador', '=' , 'colaborador.id_colaborador')
                    ->select('email.email')
                    ->where('email.id_colaborador', '=', intval($value))
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
            }],
            ['label'=>'Disponibilidade', 'db'=>'disponivel', 'dt'=>4, 'formatter'=>function($value, $model){
                if($value == 0) {
                    return 'Disponível';
                }
                else {
                    return 'Indisponível';
                }
            }],
            ['label'=>'Opções', 'db'=>'id_colaborador', 'dt'=>5, 'formatter'=>function($value, $model){ 
                $btns = [
                    '<a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$value.')"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>',
                ];
                return implode(" ", $btns); 
            }],
        ];
        $dt_obj = new SSP('App\Models\Colaborador', $dt);
        echo json_encode($dt_obj->getDtArr());
    }
}
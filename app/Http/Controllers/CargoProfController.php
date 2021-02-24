<?php

namespace App\Http\Controllers;

use App\Models\CargoProf;
use Illuminate\Http\Request;
use DB;

class CargoProfController extends Controller
{

    public function getAll() {
        $cargos = CargoProf::all();
        
        if($cargos != null) {
            return \json_encode($cargos);  
        }
        else {
            return null;
        } 
    }

    public function getPorIdProf($id, $id_projeto, $ano) {
        $cargo =  DB::table('cargo_professor')
                ->join('projeto_professor', 'cargo_professor.id_cargoProfessor', '=', 'projeto_professor.id_cargo')
                ->select('cargo_professor.nomeCargo' , 'cargo_professor.id_cargoProfessor')
                ->where([
                    ['projeto_professor.id_professor', '=', $id],
                    ['projeto_professor.id_projeto', '=', $id_projeto],
                    ['projeto_professor.anoParticipacao', '=', $ano]
                    ])
                ->first();
        
        if($cargo != null) {
            return \json_encode($cargo);  
        }
        else {
            return null;
        } 
    }
}
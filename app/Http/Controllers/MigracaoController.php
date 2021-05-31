<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Projeto;
use DB;

class MigracaoController extends Controller
{
    public function iniciarMigracao()
    {
        Excel::import(new ExcelImport, request()->file('excel'));

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
}
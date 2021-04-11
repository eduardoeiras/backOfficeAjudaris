<?php

namespace App\Http\Controllers;

use App\Models\EscolaSolidaria;
use Illuminate\Http\Request;
use DB;
use App\Models\Livros_Ano;

class LivrosAnoController extends Controller
{
    public function index($id, $nome)
    {
        $user = session()->get("utilizador");

        $livrosAno = DB::table('livros_ano')
        ->select('livros_ano.*')
        ->where('livros_ano.id_escola', '=', $id)
        ->get();

        if($user->tipoUtilizador == 0) {
            return view('admin/gerirLivrosAno', ['data' => $livrosAno, 'id_escola' => $id, 'nome' => $nome]);
        }
        else {
            return view('colaborador/gerirLivrosAno', ['data' => $livrosAno, 'id_escola' => $id, 'nome' => $nome]);
        }
    }

    public function store(Request $request)
    {
        $anoL = intval($request->anoLivros);
        $numL = intval($request->numLivros);
        $id_escola = intval($request->id_escola);
        $nome = $request->nome;

        $livroAno = new Livros_Ano();
        $livroAno->ano = $anoL;
        $livroAno->numLivros = $numL;
        $livroAno->id_escola = $id_escola;
        $livroAno->save();

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirLivrosAno", ['id' => $id_escola, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirLivrosAnoColaborador", ['id' => $id_escola, 'nome' => $nome]);
        }
    }

    public function update($ano, $id, Request $request)
    {
        $id_escola = \intval($id);
        $anoL = \intval($ano);
        $numL = intval($request->numLivros);
        $nome = $request->nome;
        
        DB::table('livros_ano')
                ->where([['livros_ano.id_escola', '=', $id_escola],
                        ['livros_ano.ano', '=', $anoL]])
                ->update([
                    'numLivros'=> $numL,
                ]);;

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirLivrosAno", ['id' => $id_escola, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirLivrosAnoColaborador", ['id' => $id_escola, 'nome' => $nome]);
        }
    }

    public function destroy($ano, $id, Request $request)
    {
        $id_escola = \intval($id);
        $anoL = \intval($ano);
        $nome = $request->nome;

        $livroAno = DB::table('livros_ano')->select('livros_ano.*')->where([
            ['livros_ano.id_escola', '=', $id_escola],
            ['livros_ano.ano', '=', $anoL]]);

        if($livroAno->first() != null) {
            $livroAno->delete();
        }

        $user = session()->get("utilizador");
        if($user->tipoUtilizador == 0) {
            return redirect()->route("gerirLivrosAno", ['id' => $id_escola, 'nome' => $nome]);
        }
        else {
            return redirect()->route("gerirLivrosAnoColaborador", ['id' => $id_escola, 'nome' => $nome]);
        }
    }

    public function getPorId($ano, $id) {

        $id_escola = \intval($id);
        $anoL = \intval($ano);

        $livrosAno = DB::table('livros_ano')
        ->select('livros_ano.*')
        ->where([['livros_ano.id_escola', '=', $id_escola],
                ['livros_ano.ano', '=', $anoL]])
        ->get();
        
        if($livrosAno != null) {
            return response()->json($livrosAno);  
        }
        else {
            return null;
        }
        
    }

    public function existeAssociacao($ano, $id) {

        $id_escola = \intval($id);
        $anoL = \intval($ano);

        $livrosAno = DB::table('livros_ano')
        ->select('livros_ano.*')
        ->where([['livros_ano.id_escola', '=', $id_escola],
                ['livros_ano.ano', '=', $anoL]])
        ->first();
        
        if($livrosAno != null) {
            return 1;  
        }
        else {
            return 0;
        }
        
    }
}
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
            return redirect()->route('dashboardAdmin');
        }
        else {
            return redirect()->route('dashboardColaborador');
        }
    }
}
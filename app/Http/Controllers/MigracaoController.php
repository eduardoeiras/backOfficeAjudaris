<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;

class MigracaoController extends Controller
{
    public function iniciarMigracao()
    {
        Excel::import(new ExcelImport, request()->file('excel'));

    }
}
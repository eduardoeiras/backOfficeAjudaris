<?php

namespace App\Imports;

use App\Colaborador;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExcelImport implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            //0 => new EstabelecimentosEnsinoSolidarioImport(),
            //1 => new IlustradorSolidarioImport(),
            //2 => new ContadoresHistoriasImport(),
            3 => new RBEImport()
        ];
    }
}
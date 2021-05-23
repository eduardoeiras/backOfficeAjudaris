<?php

namespace App\Imports;

use App\Colaborador;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExcelImport implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            0 => new EscolasAgrupamentosImport(),
            //1 => new IlustradorSolidariosImport(),
        ];
    }
}
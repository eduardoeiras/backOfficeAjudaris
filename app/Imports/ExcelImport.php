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
            4 => new Revisao_JuriImport(),
            5 => new EntidadesOficiaisImport(),
            6 => new ParceirosImport(),
            7 => new UniversidadesImport(),
        ];
    }
}
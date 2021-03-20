<?php

namespace Tests\Unit;

use App\Models\Colaborador;
use Tests\TestCase;

class AgrupamentoTeste extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $colaborador = new Colaborador();
        $colaborador->nome = "Teste";
        $colaborador->save();

        echo $colaborador->id;

        $this->assertTrue(true);
    }
}

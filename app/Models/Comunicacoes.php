<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicacao extends Model
{
    use HasFactory;

    protected $table = 'comunicacao';
    public $primaryKey = 'id_comunicacao';
    public $timestamps = false;

    protected $fillable = [
        'data',
        'observacoes',
        'numLivros',
        'id_comunicacao'
    ];

    /*public function colaborador() {
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }*/

}

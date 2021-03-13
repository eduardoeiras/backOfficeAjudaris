<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    use HasFactory;

    protected $table = 'colaborador';
    public $primaryKey = 'id_colaborador';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'telefone',
        'telemovel',
        'observacoes',
        'disponivel',
        'codPostal',
        'codPostalRua',
        'numPorta'
    ];

    public function codPostal() {
        return $this->hasOne(CodPostal::class, 'codPostal');
    }

    public function codPostalRua() {
        return $this->hasOne(CodPostalRua::class, 'codPostalRua');
    }
}

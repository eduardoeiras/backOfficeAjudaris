<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agrupamento extends Model
{
    use HasFactory;

    protected $table = 'agrupamento';
    public $primaryKey = 'id_agrupamento';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'nomeDiretor',
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

    public function escolas() {
        return $this->hasMany(EscolaSolidaria::class, 'id_agrupamento');
    }

    public function professores() {
        return $this->hasMany(Professor::class, 'id_agrupamento');
    }
}

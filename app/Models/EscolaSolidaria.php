<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscolaSolidaria extends Model
{
    use HasFactory;

    protected $table = 'escola_solidaria';
    public $primaryKey = 'id_escolaSolidaria';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'telefone',
        'telemovel',
        'contactoAssPais',
        'id_agrupamento',
        'disponivel'
    ];

    public function professores() {
        return $this->hasMany(EscolaSolidariaProf::class, 'id_escola', 'id_escolaSolidaria');
    }

    public function projetos() {
        return $this->hasMany(ProjetoEscola::class, 'id_escolaSolidaria');
    }

    public function agrupamento() {
        return $this->belongsTo(Agrupamento::class, 'id_agrupamento');
    }
}

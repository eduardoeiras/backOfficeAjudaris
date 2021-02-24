<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professor';
    public $primaryKey = 'id_professor';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'telefone',
        'telemovel',
        'email',
        'id_agrupamento',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoProfessor::class, 'id_professor');
    }

    public function escolas() {
        return $this->hasMany(EscolaSolidariaProf::class, 'id_professor');
    }
    
    public function trocasAgrupamento() {
        return $this->hasMany(TrocaAgrupamento::class, 'id_professor');
    }

    public function agrupamento() {
        return $this->hasOne(Agrupamento::class, 'id_professor');
    }

    
}

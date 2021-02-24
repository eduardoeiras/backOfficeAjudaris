<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;

    protected $table = 'projeto';
    public $primaryKey = 'id_projeto';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'objetivos',
        'regulamento',
        'publicoAlvo',
        'observacoes'
    ];

    public function utilizadores() {
        return $this->hasMany(ProjetoUtilizador::class, 'id_projeto');
    }

    public function ilustradores() {
        return $this->hasMany(ProjetoIlustrador::class, 'id_projeto');
    }

    public function juris() {
        return $this->hasMany(ProjetoJuri::class, 'id_projeto');
    }

    public function professores() {
        return $this->hasMany(ProjetoProfessor::class, 'id_projeto');
    }

    public function professoresFacul() {
        return $this->hasMany(ProjetoProfessorFacul::class, 'id_projeto');
    }

    public function rbes() {
        return $this->hasMany(ProjetoRBE::class, 'id_projeto');
    }

    public function universidades() {
        return $this->hasMany(ProjetoUniversidade::class, 'id_projeto');
    }

    public function contadores() {
        return $this->hasMany(ProjetoContador::class, 'id_projeto');
    }

    public function entidades() {
        return $this->hasMany(ProjetoEntidade::class, 'id_projeto');
    }
    
    public function escolas() {
        return $this->hasMany(ProjetoEscola::class, 'id_projeto');
    }
}

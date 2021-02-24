<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoEntidade extends Model
{
    use HasFactory;

    protected $table = 'projeto_entidade';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_entidadeOficial'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_entidadeOficial',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function entidadeOficial() {
        return $this->hasOne(EntidadeOficial::class, 'id_entidadeOficial');
    }
}

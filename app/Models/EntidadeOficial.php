<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadeOficial extends Model
{
    use HasFactory;

    protected $table = 'entidade_oficial';
    public $primaryKey = 'id_entidadeOficial';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'entidade',
        'telefone',
        'telemovel',
        'observacoes',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoEntidade::class, 'id_entidadeOficial');
    }
}

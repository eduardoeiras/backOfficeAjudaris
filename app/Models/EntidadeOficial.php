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
        'entidade',
        'nif',
        'id_colaborador'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoEntidade::class, 'id_entidadeOficial');
    }

    public function colaborador() {
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }
}

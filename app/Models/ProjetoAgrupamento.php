<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoAgrupamento extends Model
{
    use HasFactory;

    protected $table = 'projeto_agrupamento';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_agrupamento'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_agrupamento',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function agrupamento() {
        return $this->hasOne(Agrupamento::class, 'id_agrupamento');
    }
}

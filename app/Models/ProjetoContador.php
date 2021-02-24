<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoContador extends Model
{
    use HasFactory;

    protected $table = 'projeto_contador';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_contador'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_contador',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function contador() {
        return $this->hasOne(ContadorHistorias::class, 'id_contador');
    }
}

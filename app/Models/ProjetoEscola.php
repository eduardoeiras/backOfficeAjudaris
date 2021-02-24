<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoEscola extends Model
{
    use HasFactory;

    protected $table = 'projeto_escola';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_escolaSolidaria'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_escolaSolidaria',
        'anoParticipacao'
    ];

    public function projeto() {
       return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function escola() {
        return $this->hasOne(EscolaSolidaria::class, 'id_escolaSolidaria');
    }
}

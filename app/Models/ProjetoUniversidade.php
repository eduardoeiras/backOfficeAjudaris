<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoUniversidade extends Model
{
    use HasFactory;

    protected $table = 'projeto_universidade';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_universidade'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_universidade',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function universidade() {
        return $this->hasOne(Universidade::class, 'id_universidade');
    }
}

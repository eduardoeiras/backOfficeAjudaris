<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoIlustrador extends Model
{
    use HasFactory;

    protected $table = 'projeto_ilustrador';
    public $primaryKey = ['id_projeto', 'id_ilustradorSolidario', 'anoParticipacao'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_ilustradorSolidario',
        'anoParticipacao'
    ];

    public function projeto() {
        $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function ilustrador() {
        $this->hasOne(IlustradorSolidario::class, 'id_ilustradorSolidario');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoProfessor extends Model
{
    use HasFactory;

    protected $table = 'projeto_professor';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_professor'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_professor',
        'anoParticipacao',
        'id_cargo'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function professor() {
        return $this->hasOne(Professor::class, 'id_professor');
    }

    public function cargo() {
        return $this->hasOne(CargoProf::class, 'id_cargo');
    }
}

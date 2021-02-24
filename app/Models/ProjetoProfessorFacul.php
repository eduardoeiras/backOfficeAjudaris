<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoProfessorFacul extends Model
{
    use HasFactory;

    protected $table = 'projeto_prof_faculdade';
    public $primaryKey = ['anoParticipacao', 'id_professorFaculdade', 'id_projeto'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_professorFaculdade'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function professorFacul() {
        return $this->hasOne(ProfessorFaculdade::class, 'id_professorFaculdade');
    }
}

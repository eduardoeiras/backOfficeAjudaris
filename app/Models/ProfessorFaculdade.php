<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorFaculdade extends Model
{
    use HasFactory;

    protected $table = 'professor_faculdade';
    public $primaryKey = 'id_professorFaculdade';
    public $timestamps = false;

    protected $fillable = [
        'cargo',
        'id_colaborador'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoProfessorFacul::class, 'id_professorFaculdade');
    }

    public function universidades() {
        return $this->hasMany(UniversidadeProfFaculdade::class, 'id_professorFaculdade');
    }

    public function colaborador(){
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }
}

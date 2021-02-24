<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoProf extends Model
{
    use HasFactory;

    protected $table = 'cargo_professor';
    public $primaryKey = 'id_cargoProfessor';
    public $timestamps = false;

    protected $fillable = [
        'nomeCargo',
    ];

    public function projetosProfessor() {
        return $this->hasMany(ProjetoProfessor::class, 'id_cargoProfessor');
    }
}

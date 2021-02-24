<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universidade extends Model
{
    use HasFactory;

    protected $table = 'universidade';
    public $primaryKey = 'id_universidade';
    public $timestamps = false;

    protected $fillable = [
        'curso',
        'tipo',
        'nome',
        'telefone',
        'telemovel',
        'email',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoUniversidade::class, 'id_universidade');
    }

    public function professores() {
        return $this->hasMany(UniversidadeProfFaculdade::class, 'id_universidade');
    }
}

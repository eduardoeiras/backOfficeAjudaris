<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContadorHistoria extends Model
{
    use HasFactory;

    protected $table = 'contador_historias';
    public $primaryKey = 'id_contadorHistorias';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'telemovel',
        'observacoes',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoContador::class, 'id_contador', 'id_contadorHistorias');
    }
}

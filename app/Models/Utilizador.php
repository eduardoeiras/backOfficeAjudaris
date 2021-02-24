<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilizador extends Model
{
    use HasFactory;

    protected $table = 'utilizador';
    public $primaryKey = 'id_utilizador';
    public $timestamps = false;

    protected $fillable = [
        'nomeUtilizador',
        'telemovel',
        'telefone',
        'email',
        'tipoUtilizador',
        'departamento',
        'password',
        'nome'
    ];
    
    
    public function projetos()
    {
        return $this->hasMany(ProjetoUtilizador::class, 'id_utilizador');
    }
}
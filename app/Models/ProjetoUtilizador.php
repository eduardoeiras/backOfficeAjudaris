<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoUtilizador extends Model
{
    use HasFactory;

    protected $table = 'projeto_utilizador';
    public $primaryKey = ['id_utilizador', 'id_projeto'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_utilizador'
    ];

    public function utilizador() {
        return $this->hasOne(Utilizador::class, 'id_utilizador');
    }

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }
}

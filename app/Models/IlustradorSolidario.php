<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlustradorSolidario extends Model
{
    use HasFactory;

    protected $table = 'ilustrador_solidario';
    public $primaryKey = 'id_ilustradorSolidario';
    public $timestamps = false;

    protected $fillable = [
        'volumeLivro',
        'disponivel',
        'nome',
        'telefone',
        'telemovel',
        'email',
        'observacoes'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoIlustrador::class, 'id_ilustradorSolidario');
    }
}

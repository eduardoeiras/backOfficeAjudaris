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
        'id_colaborador'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoIlustrador::class, 'id_ilustradorSolidario');
    }

    public function colaborador() {
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }
}

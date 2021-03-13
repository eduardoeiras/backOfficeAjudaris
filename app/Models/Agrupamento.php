<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agrupamento extends Model
{
    use HasFactory;

    protected $table = 'agrupamento';
    public $primaryKey = 'id_agrupamento';
    public $timestamps = false;

    protected $fillable = [
        'nomeDiretor',
        'id_colaborador'
    ];

    public function colaborador() {
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }

    public function escolas() {
        return $this->hasMany(EscolaSolidaria::class, 'id_agrupamento');
    }

    public function professores() {
        return $this->hasMany(Professor::class, 'id_agrupamento');
    }
}

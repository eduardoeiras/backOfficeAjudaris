<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RBE extends Model
{
    use HasFactory;

    protected $table = 'rbe';
    public $primaryKey = 'id_rbe';
    public $timestamps = false;

    protected $fillable = [
        'regiao',
        'nomeCoordenador',
        'id_concelho',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoRBE::class, 'id_rbe');
    }

    public function concelho() {
        return $this->hasOne(Concelho::class, 'id_concelho');
    }
}

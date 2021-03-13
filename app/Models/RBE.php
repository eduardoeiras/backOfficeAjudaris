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
    ];

    public function projetos() {
        return $this->hasMany(ProjetoRBE::class, 'id_rbe');
    }

    public function concelhos() {
        return $this->hasMany(Rbe_concelho::class, 'id_concelho');
    }
}

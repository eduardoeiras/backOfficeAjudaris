<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoRBE extends Model
{
    use HasFactory;

    protected $table = 'projeto_rbe';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_rbe'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_rbe',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function rbe() {
        return $this->hasOne(RBE::class, 'id_rbe');
    }
}

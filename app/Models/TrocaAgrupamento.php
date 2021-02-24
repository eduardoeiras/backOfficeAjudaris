<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrocaAgrupamento extends Model
{
    use HasFactory;

    protected $table = 'troca_agrupamento';
    public $primaryKey = 'id_troca';
    public $timestamps = false;

    protected $fillable = [
        'agrupamentoAntigo',
        'novoAgrupamento',
        'telemovelobservacoes',
        'id_professor'
    ];

    public function professor() {
        return $this->hasOne(Professor::class, 'id_troca');
    }
}

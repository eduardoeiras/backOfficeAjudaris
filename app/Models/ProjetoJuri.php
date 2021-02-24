<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoJuri extends Model
{
    use HasFactory;

    protected $table = 'projeto_juri';
    public $primaryKey = ['anoParticipacao', 'id_projeto', 'id_juri'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_projeto',
        'id_juri',
        'anoParticipacao'
    ];

    public function projeto() {
        return $this->hasOne(Projeto::class, 'id_projeto');
    }

    public function juri() {
        return $this->hasOne(Juri::class, 'id_juri');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Juri extends Model
{
    use HasFactory;

    protected $table = 'juri';
    public $primaryKey = 'id_juri';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'telemovel',
        'disponivel'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoJuri::class, 'id_juri');
    }
}

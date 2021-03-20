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
        'tipoJuri',
        'id_colaborador'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoJuri::class, 'id_juri');
    }
    
    public function colaborador(){
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }
}

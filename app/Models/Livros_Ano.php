<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livros_Ano extends Model
{
    use HasFactory;

    protected $table = 'livros_ano';
    public $primaryKey = ['ano', 'id_escola'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ano',
        'id_escola',
        'numLivros'
    ];

    public function escola() {
        return $this->hasOne(EscolaSolidaria::class, 'id_escola', 'id_escolaSolidaria');
    }
}

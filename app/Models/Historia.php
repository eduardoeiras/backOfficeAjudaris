<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historia extends Model
{
    use HasFactory;

    protected $table = 'historia';
    public $primaryKey = 'id_historia';
    public $timestamps = false;

    protected $fillable = [
        'ano',
        'urlFicheiro',
        'id_escolaSolidaria'
    ];

    public function escola() {
        return $this->hasOne(EscolaSolidaria::class, 'id_colaborador');
    }
}

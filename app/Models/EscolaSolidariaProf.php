<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscolaSolidariaProf extends Model
{
    use HasFactory;

    protected $table = 'escola_professor';
    public $primaryKey = ['id_escola', 'id_professor'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_escola', 
        'id_professor',
        'interlocutor'
    ];

    public function escola() {
        return $this->hasOne(EscolaSolidariaProf::class, 'id_escola', 'id_escolaSolidaria');
    }

    public function professor() {
        return $this->hasOne(Professor::class, 'id_professor');
    }
}

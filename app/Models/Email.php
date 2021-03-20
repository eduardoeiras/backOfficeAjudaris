<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'email';
    public $primaryKey = 'email';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'id_colaborador'
    ];

    public function colaborador() {
        return $this->hasOne(Colaborador::class, 'id_colaborador');
    }

}

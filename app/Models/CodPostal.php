<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodPostal extends Model
{
    use HasFactory;

    protected $table = 'cod_postal';
    public $primaryKey = 'codPostal';
    public $timestamps = false;

    protected $fillable = [
        'localidade'
    ];

    public function codPostalRua() {
        return $this->hasMany(CodPostalRua::class, 'codPostal');
    }
}

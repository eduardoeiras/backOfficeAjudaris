<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodPostalRua extends Model
{
    use HasFactory;

    protected $table = 'cod_postal_rua';
    protected $primaryKey = ['codPostal', 'codPostalRua'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codPostal',
        'codPostalRua',
        'rua',
    ];

    public function codPostal() {
        return $this->hasOne(CodPostal::class, 'codPostal', 'codPostalRua');
    }
}

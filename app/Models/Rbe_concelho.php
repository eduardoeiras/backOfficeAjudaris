<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rbe_concelho extends Model
{
    use HasFactory;

    protected $table = 'rbe_concelho';
    public $primaryKey = ['id_rbe', 'id_concelho'];
    public $timestamps = false;

    protected $fillable = [
        'id_rbe', 
        'id_concelho'
    ];

    public function projetos() {
        return $this->hasMany(ProjetoRBE::class, 'id_rbe');
    }

    public function concelho() {
        return $this->hasOne(Concelho::class, 'id_concelho');
    }
}

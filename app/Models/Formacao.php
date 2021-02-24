<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formacao extends Model
{
    use HasFactory;

    protected $table = 'formacao';
    public $primaryKey = 'id_formacao';
    public $timestamps = false;

    protected $fillable = [
        'nomeInstituicao',
        'email'
    ];
}

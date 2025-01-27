<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colis extends Model
{
    use HasFactory;
    protected $fillable = [
        'iduser',
        'description',
        'refcoli',
        'idtaillecoli',
        'idtype',
        'idprix',
        'status',
        'iddatecoli',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livreuravis extends Model
{
    use HasFactory;

    protected $fillable = [
        'idlivreur',
        'idclient',
        'idcoli',
        'avis',
        'message',
    ];
}

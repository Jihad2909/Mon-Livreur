<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfeuille extends Model
{
    use HasFactory;

    protected $fillable =[
        'idlivreur',
        'solde',
        'soldebloque',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colilivreur extends Model
{
    use HasFactory;

    protected $fillable =[
      'idcoli',
      'idlivraison'
    ];
}

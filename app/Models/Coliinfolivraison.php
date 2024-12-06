<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coliinfolivraison extends Model
{
    use HasFactory;
    protected $fillable = [
      'idcoli',
      'idinfoenvoyeur',
      'idinforecepture',
    ];
}

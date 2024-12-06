<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prixcoli extends Model
{
    use HasFactory;
    protected $fillable =[
      'prixbase',
      'prixclient',
      'prixfinal',
    ];
}

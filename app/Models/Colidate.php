<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colidate extends Model
{
    use HasFactory;

    protected $fillable = [
      'time',
      'date',
    ];
}

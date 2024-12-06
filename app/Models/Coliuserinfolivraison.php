<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coliuserinfolivraison extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'email',
      'phonenumber',
      'idadresse',
      'idadressetype',
    ];
}

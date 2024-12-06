<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $fillable =[
      'path',
      'url',
    ];
        public static function Uploadphoto($file,$str)
        {
            $na=$file->getClientOriginalExtension();
            $name= $str.time().'.'.$na;

            $UserPhotos['path'] = $file->move('images',$name);
            $UserPhotos['url'] =  'http://10.0.2.2:8000/images/'.$name;
            return $UserPhotos;
    }
}

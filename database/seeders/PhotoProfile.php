<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoProfile extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('photos')->delete();
        $data = [
            [
                'path' => 'avatarDefaulsImage',
            ],
            [
                'url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQkesz0_RyJN6eH9NuLgxbCM-bVCeaRawoZWQ&usqp=CAU',
            ],

        ];

        DB::table('photos')->insert($data);
    }
}

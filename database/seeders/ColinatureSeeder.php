<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColinatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colinaturenames')->delete();
        $data = [
            [
                'nature' => 'alimentaire',
            ],
            [
                'nature' => 'électronique',
            ],
            [
                'nature' => 'vêtements',
            ],

        ];

        DB::table('colinaturenames')->insert($data);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsertypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        DB::table('usertypes')->delete();
        $data = [
            [
                'typeuser' => 'client',
            ],
            [
                'typeuser' => 'livreur',
            ],
            [
                'typeuser' => 'admin',
            ],
        ];

        DB::table('usertypes')->insert($data);
    }
}

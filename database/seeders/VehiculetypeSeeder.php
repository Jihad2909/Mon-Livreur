<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculetypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        DB::table('typevehicules')->delete();
        $data = [
            [
                'nomtype' => 'moto',
            ],
            [
                'nomtype' => 'voiture',
            ],
        ];

        DB::table('typevehicules')->insert($data);
    }
}

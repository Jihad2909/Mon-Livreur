<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColitailleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colistailles')->delete();
        $data = [
            [
                'taille' => 'petit',
            ],
            [
                'taille' => 'moyenne',
            ],
            [
                'taille' => 'grande',
            ],
        ];

        DB::table('colistailles')->insert($data);
    }
}

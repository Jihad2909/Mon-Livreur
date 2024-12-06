<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColiTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('typecolis')->delete();
        $data = [
            [
                'type' => 'normal',
            ],
            [
                'type' => 'express',
            ],
        ];

        DB::table('typecolis')->insert($data);
    }
}

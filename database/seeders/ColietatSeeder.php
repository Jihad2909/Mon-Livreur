<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColietatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colietats')->delete();
        $data = [
            [
                'etat' => 'en attente',
            ], [
                'etat' => 'accepter',
            ], [
                'etat' => 'en cours',
            ],  [
                'etat' => 'rejeter',
            ],[
                'etat' => 'livrer',
            ],
        ];

        DB::table('colietats')->insert($data);
    }
}

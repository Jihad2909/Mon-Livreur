<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsertypeSeeder::class);
        $this->call(ColietatSeeder::class);
        $this->call(ColinatureSeeder::class);
        $this->call(VehiculetypeSeeder::class);
        $this->call(ColitailleSeeder::class);
        $this->call(ColiTypeSeeder::class);
    }
}

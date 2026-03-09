<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            FirstDatabaseSeeder::class,
            DataSiswaSeeder::class,
            KonsultasiSeeder::class,
        ]);
    }
}
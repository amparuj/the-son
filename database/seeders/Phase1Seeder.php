<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Phase1Seeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TableSeeder::class,
            ProductSeeder::class,
        ]);
    }
}

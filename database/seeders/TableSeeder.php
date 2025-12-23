<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Table::updateOrCreate(
                ['number' => $i],
                ['public_uuid' => (string) Str::uuid(), 'is_active' => true]
            );
        }
    }
}

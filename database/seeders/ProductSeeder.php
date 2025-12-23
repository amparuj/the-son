<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'ก๋วยเตี๋ยวไก่ตุ๋น ธรรมดา', 'price' => 25.00],
            ['name' => 'ก๋วยเตี๋ยวไก่ตุ๋น พิเศษ', 'price' => 35.00],
            ['name' => 'ข้าวมันไก่', 'price' => 45.00],
            ['name' => 'น้ำเปล่า', 'price' => 10.00],
        ];

        foreach ($items as $it) {
            Product::updateOrCreate(['name' => $it['name']], [
                'price' => $it['price'],
                'is_active' => true,
            ]);
        }
    }
}

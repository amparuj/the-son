<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Option;
use App\Models\OptionGroup;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $vegGroup = OptionGroup::firstOrCreate(['name' => 'ผัก/เครื่องโรย'], ['sort' => 1]);
            $topGroup = OptionGroup::firstOrCreate(['name' => 'ท็อปปิ้งเพิ่ม'], ['sort' => 2]);

            $veg = [
                ['name' => 'คะน้า', 'base_price' => 0, 'sort' => 1],
                ['name' => 'ถั่วงอก', 'base_price' => 0, 'sort' => 2],
                ['name' => 'กระเทียมเจียว', 'base_price' => 0, 'sort' => 3],
                ['name' => 'ผักโรย', 'base_price' => 0, 'sort' => 4],
            ];

            $top = [
                ['name' => 'ไก่ฉีก', 'base_price' => 0, 'sort' => 1],
                ['name' => 'ข้อไก่', 'base_price' => 0, 'sort' => 2],
                ['name' => 'เลือดไก่', 'base_price' => 0, 'sort' => 3],
            ];

            foreach ($veg as $v) {
                $opt = Option::firstOrCreate(['name' => $v['name']], [
                    'base_price' => $v['base_price'],
                    'sort' => $v['sort'],
                    'is_active' => true,
                ]);
                $vegGroup->options()->syncWithoutDetaching([$opt->id => ['sort' => $v['sort']]]);
            }

            foreach ($top as $t) {
                $opt = Option::firstOrCreate(['name' => $t['name']], [
                    'base_price' => $t['base_price'],
                    'sort' => $t['sort'],
                    'is_active' => true,
                ]);
                $topGroup->options()->syncWithoutDetaching([$opt->id => ['sort' => $t['sort']]]);
            }
        });
    }
}

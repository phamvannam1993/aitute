<?php

namespace Database\Seeders;

use App\Models\AIPricing;
use App\Models\ConfigAff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigAffSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=ConfigAffSeeder3
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'GÓI CHUYÊN SÂU',
                'code' => 'aff_expert'
            ],
            [
                'name' => 'GÓI TIÊU CHUẨN',
                'code' => 'aff_basic'
            ],
            [
                'name' => 'GÓI NÂNG CAO',
                'code' => 'aff_advanced'
            ]
        ];

        foreach ($data as $item) {
            ConfigAff::where('name', $item['name'])->update(['code' => $item['code']]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV16Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV16Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'soida-standard',
                'model_code' => 'soida-standard',
                'type' => 'soida-standard',
                'cost_normal' => 1500,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
            [
                'id' => $maxId + 2,
                'name' => 'soida-advanced',
                'model_code' => 'soida-advanced',
                'type' => 'soida-advanced',
                'cost_normal' => 800,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}
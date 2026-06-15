<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV17Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV17Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'upscale-image',
                'model_code' => 'upscale-image',
                'type' => 'upscale-image',
                'cost_normal' => 500,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV8Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV8Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'Pebblely',
                'model_code' => 'pebblely',
                'type' => 'image',
                'cost_normal' => 2500,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

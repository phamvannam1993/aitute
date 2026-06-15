<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV18Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV18Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'image_combine',
                'model_code' => 'image_combine',
                'type' => 'image_combine',
                'cost_normal' => 500,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}
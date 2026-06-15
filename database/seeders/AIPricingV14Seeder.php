<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV14Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV14Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'my_ai_image',
                'model_code' => 'my_ai_image',
                'type' => 'my_ai_image',
                'cost_normal' => 250000,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

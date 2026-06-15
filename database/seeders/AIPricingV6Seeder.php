<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV6Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV6Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'elevenlab',
                'model_code' => 'elevenlab',
                'type' => 'elevenlab',
                'cost_normal' => 5,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

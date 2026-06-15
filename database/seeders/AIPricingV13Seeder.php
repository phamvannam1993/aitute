<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV13Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV13Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'caption',
                'model_code' => 'caption',
                'type' => 'caption',
                'cost_normal' => 26,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

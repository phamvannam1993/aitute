<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV7Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV7Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'consistent-character',
                'model_code' => 'consistent-character',
                'type' => 'image',
                'cost_normal' => 38,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

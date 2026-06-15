<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV15Seeder extends Seeder
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
                'name' => 'soida',
                'model_code' => 'soida',
                'type' => 'soida',
                'cost_normal' => 600,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'applamdep.soida'
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV4Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV4Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'aesthetic',
                'model_code' => 'aesthetic',
                'type' => 'aesthetic',
                'cost_normal' => 290,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

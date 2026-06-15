<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV3Seeder extends Seeder
{
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'Runway: Gen3 Alphaturbo',
                'model_code' => 'gen3-alphaturbo',
                'type' => 'video',
                'cost_normal' => 2600,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'giá cho 1s của video (vnd/s)'
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

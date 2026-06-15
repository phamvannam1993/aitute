<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingv2Seeder extends Seeder
{
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'Lip Sync',
                'model_code' => 'lipsync',
                'type' => 'lipsync',
                'cost_normal' => 470,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'giá cho 1s của video (vnd/s)'
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}
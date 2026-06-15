<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV11Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV11Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'pictory_create_content',
                'model_code' => 'pictory_create_content',
                'type' => 'pictory_create_content',
                'cost_normal' => 100,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'giá cho 1s của video (vnd/s)'
            ],
            [
                'id' => $maxId + 2,
                'name' => 'pictory_create_video',
                'model_code' => 'pictory_create_video',
                'type' => 'pictory_create_video',
                'cost_normal' => 100,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'giá cho 1s của video (vnd/s)'
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

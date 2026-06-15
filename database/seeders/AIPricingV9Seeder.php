<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV9Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV9Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'FLUX Pro',
                'model_code' => 'flux-1.1-pro',
                'type' => 'image',
                'cost_normal' => 76,
                'cost_input' => null,
                'cost_output' => null,
                'note' => 'giá cho 1 image (vnd/image)'
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

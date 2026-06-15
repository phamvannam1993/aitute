<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV12Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV12Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'photo-beauty',
                'model_code' => 'photo-beauty',
                'type' => 'photo-beauty',
                'cost_normal' => 94,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

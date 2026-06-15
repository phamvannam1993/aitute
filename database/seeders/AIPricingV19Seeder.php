<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV19Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV19Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'generate-image-background',
                'model_code' => 'generate-image-background',
                'type' => 'generate-image-background',
                'cost_normal' => 1500,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

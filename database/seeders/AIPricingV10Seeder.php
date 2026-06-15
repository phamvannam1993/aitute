<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPricingV10Seeder extends Seeder
{
    /**
     * php artisan db:seed --class=AIPricingV10Seeder
     */
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'heygen_photo_avatar',
                'model_code' => 'heygen_photo_avatar',
                'type' => 'heygen_photo_avatar',
                'cost_normal' => 128,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
            [
                'id' => $maxId + 2,
                'name' => 'heygen_video_avatar',
                'model_code' => 'heygen_video_avatar',
                'type' => 'heygen_video_avatar',
                'cost_normal' => 128,
                'cost_input' => null,
                'cost_output' => null,
                'note' => ''
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}

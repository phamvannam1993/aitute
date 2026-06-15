<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiPricingSeeder extends Seeder
{
    public function run()
    {
        $maxId = DB::table('ai_pricings')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'text_to_video',
                'model_code' => 'text_to_video',
                'type' => 'text_to_video',
                'cost_normal' => 1200,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 2,
                'name' => 'text_to_slide',
                'model_code' => 'text_to_slide',
                'type' => 'text_to_slide',
                'cost_normal' => 600,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 3,
                'name' => 'image_to_image',
                'model_code' => 'image_to_image',
                'type' => 'image_to_image',
                'cost_normal' => 40,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 4,
                'name' => 'image_to_video',
                'model_code' => 'image_to_video',
                'type' => 'image_to_video',
                'cost_normal' => 6500,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 5,
                'name' => 'text_to_music',
                'model_code' => 'text_to_music',
                'type' => 'text_to_music',
                'cost_normal' => 625,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 6,
                'name' => 'music_to_text',
                'model_code' => 'music_to_text',
                'type' => 'music_to_text',
                'cost_normal' => 600,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 7,
                'name' => 'ai-audio',
                'model_code' => 'ai-audio',
                'type' => 'ai-audio',
                'cost_normal' => 600,
                'cost_input' => null,
                'cost_output' => null
            ],
            [
                'id' => $maxId + 8,
                'name' => 'Lip Sync',
                'model_code' => 'lipsync',
                'type' => 'lipsync',
                'cost_normal' => 470,
                'cost_input' => null,
                'cost_output' => null
            ],
        ];

        DB::table('ai_pricings')->insert($data);
    }
}
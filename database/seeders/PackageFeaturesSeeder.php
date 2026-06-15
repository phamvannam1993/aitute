<?php

namespace Database\Seeders;

use App\Constants\FeatureConstants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'ai_text',
            'ai_image',
            'ai_background',
            'ai_swap_face',
            'ai_image_to_video',
            'ai_image_root',
            'ai_movie',
            'ai_video',
            'ai_virtual',
            'ai_video_voice_overlay',
            'ai_chat_bot',
            'ai_audio',
            'ai_text_to_audio_music',
            'ai_lyric_to_music',
            'ai_slide'
        ];

        $data = [];

        // Gói 1
        foreach ($features as $feature) {
            $data[] = [
                'config_aff_id' => 1,
                'feature_code' => $feature,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Gói 2
        foreach ($features as $feature) {
            $data[] = [
                'config_aff_id' => 2,
                'feature_code' => $feature,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $features3 = [
            'ai_text',
            'ai_image',
            'ai_background',
            'ai_swap_face',
            'ai_image_to_video',
            'ai_image_root',
            'ai_chat_bot',
            'ai_audio',
        ];

        // Gói 3
        foreach ($features3 as $feature) {
            $data[] = [
                'config_aff_id' => 3,
                'feature_code' => $feature,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $features4 = [
            'ai_text',
            'ai_image',
            'ai_background',
            'ai_swap_face',
            'ai_image_to_video',
            'ai_image_root',
            'ai_movie',
            'ai_video',
            'ai_virtual',
            'ai_chat_bot',
            'ai_audio',
            'ai_text_to_audio_music',
            'ai_lyric_to_music',
            'ai_slide'
        ];

        // Gói 4
        foreach ($features4 as $feature) {
            $data[] = [
                'config_aff_id' => 4,
                'feature_code' => $feature,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('package_features')->insert($data);
    }
}

<?php

namespace Database\Seeders;

use App\Models\VoiceType;
use Illuminate\Database\Seeder;

class VoiceTypesSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=VoiceTypesSeeder
     */
    public function run(): void
    {
        // seed voice_types
        $voiceTypes = [
            ['name' => 'Ngọc Lan', 'type' => 'hn_female_hermer_stor_48k-fhg'],
            ['name' => 'Hương Giang', 'type' => 'hue_female_huonggiang_full_48k-fhg'],
            ['name' => 'Thảo Trinh', 'type' => 'sg_female_thaotrinh_full_48k-fhg'],
            ['name' => 'Mạnh Dũng', 'type' => 'hn_male_manhdung_news_48k-fhg'],
            ['name' => 'Trung Kiên', 'type' => 'sg_male_trungkien_vdts_48k-fhg'],
            ['name' => 'Duy Phương', 'type' => 'hue_male_duyphuong_full_48k-fhg'],
        ];

        foreach ($voiceTypes as $voiceType) {
            VoiceType::create($voiceType);
        }
    }
}

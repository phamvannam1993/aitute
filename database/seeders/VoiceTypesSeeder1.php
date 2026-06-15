<?php

namespace Database\Seeders;

use App\Models\VoiceType;
use Illuminate\Database\Seeder;

class VoiceTypesSeeder1 extends Seeder
{
    /**
     * php artisan db:seed --class=VoiceTypesSeeder1
     */
    public function run(): void
    {
        // seed voice_types
        $voiceTypes = [
            ['name' => 'Giọng bé trai', 'type' => '0pGILgIBzOI35mQAwCrn','model' => 'cloned','demo'=>'/assets/audio/betrai.mp3'],
            ['name' => 'Giọng bé gái', 'type' => '5hAmWqD9990aqLm1t6nj','model' => 'cloned','demo'=>'/assets/audio/begai.mp3'],
        ];

        foreach ($voiceTypes as $voiceType) {
            VoiceType::create($voiceType);
        }
    }
}

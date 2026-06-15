<?php

namespace Database\Seeders;

use App\Models\VoiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoiceDemoSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=VoiceDemoSeeder
     */
    public function run(): void
    {
        $voiceDemos = [
            'Ngọc Lan' => '/assets/audio/ngoclan.mp3',
            'Hương Giang' => '/assets/audio/huonggiang.mp3',
            'Thảo Trinh' => '/assets/audio/thaotrinh.mp3',
            'Mạnh Dũng' => '/assets/audio/manhdung.mp3',
            'Trung Kiên' => '/assets/audio/trungkien.mp3',
            'Duy Phương' => '/assets/audio/duyphuong.mp3',
        ];

        foreach ($voiceDemos as $name => $demo) {
            DB::table('voice_types')
                ->where('name', $name)
                ->update(['demo' => $demo]);
        }
    }
}

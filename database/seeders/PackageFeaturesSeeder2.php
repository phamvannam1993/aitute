<?php

namespace Database\Seeders;

use App\Constants\FeatureConstants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageFeaturesSeeder2 extends Seeder
{
    public function run(): void
    {
        $features = [
            'ai_banner_poster',
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


        DB::table('package_features')->insert($data);
    }
}

<?php

namespace Database\Seeders;

use App\Constants\FeatureConstants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageFeaturesSeeder3 extends Seeder
{
    /**
     * php artisan db:seed --class=PackageFeaturesSeeder3
     */
    public function run(): void
    {
        $features = [
            'my_ai_image',
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

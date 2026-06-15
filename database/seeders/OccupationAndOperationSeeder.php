<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OccupationAndOperationSeeder extends Seeder
{
    public function run(): void
    {
        // php artisan db:seed --class=OccupationAndOperationSeeder
        $faker = Faker::create();
        
        // Tạo 40 occupations
        for ($i = 1; $i <= 40; $i++) {
            DB::table('occupations')->insert([
                'user_id' => 1, // Giả định user_id = 1
                'code' => strtoupper($faker->unique()->lexify('CODE???')), 
                'name' => $faker->jobTitle, // Tên ngành nghề
                'description' => $faker->sentence(10), // Mô tả ngẫu nhiên
                'image' => 'https://picsum.photos/200', // Ảnh image ngẫu nhiên
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);

            // Tạo khoảng 20 đến 40 operations cho mỗi occupation
            $operationCount = rand(20, 40);
            for ($j = 1; $j <= $operationCount; $j++) {
                DB::table('operations')->insert([
                    'user_id' => 1, // Giả định user_id = 1
                    'occupation_id' => $i, // Liên kết với occupation
                    'code' => strtoupper($faker->unique()->lexify('CODE???')), // Mã ngành nghề ngẫu nhiên
                    'name' => $faker->bs, // Tên nghiệp vụ ngẫu nhiên
                    'description' => $faker->sentence(15), // Mô tả ngẫu nhiên
                    'image' => 'https://picsum.photos/200', // Ảnh ngẫu nhiên
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        }
    }
}

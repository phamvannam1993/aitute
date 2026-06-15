<?php

namespace Database\Seeders;

use App\Models\BackgroundImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BackgroundImagesTableSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=BackgroundImagesTableSeeder
     */
    public function run(): void
    {
        $backgrounds = [
            [
                'id' => 1,
                'sample_url' => 'background-images/6777a5dd82736.png',
                'category' => 'new_year',
            ],
            [
                'id' => 3,
                'sample_url' => 'background-images/6777a6304fc7f.png',
                'category' => 'new_year',
            ],
            [
                'id' => 4,
                'sample_url' => 'background-images/6777a63d90b0c.png',
                'category' => 'new_year',
            ],
            [
                'id' => 5,
                'sample_url' => 'background-images/6777a65950b22.png',
                'category' => 'new_year',
            ],
            [
                'id' => 6,
                'sample_url' => 'background-images/6777a6671ae76.png',
                'category' => 'new_year',
            ],
            [
                'id' => 7,
                'sample_url' => 'background-images/6777a67ae651a.png',
                'category' => 'new_year',
            ],
            [
                'id' => 8,
                'sample_url' => 'background-images/6777a68f0a49b.png',
                'category' => 'new_year',
            ],
            [
                'id' => 9,
                'sample_url' => 'background-images/6777a6c971251.jpg',
                'category' => 'birthday',
            ],
            [
                'id' => 10,
                'sample_url' => 'background-images/6777a6dac8a6a.jpg',
                'category' => 'birthday',
            ],
            [
                'id' => 11,
                'sample_url' => 'background-images/6777a6ea856fa.jpg',
                'category' => 'birthday',
            ],
            [
                'id' => 12,
                'sample_url' => 'background-images/6777a6f626f31.jpg',
                'category' => 'birthday',
            ],
            [
                'id' => 13,
                'sample_url' => 'background-images/6777a745287a3.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 14,
                'sample_url' => 'background-images/6777a75b5ad2a.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 15,
                'sample_url' => 'background-images/6777a774d1355.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 16,
                'sample_url' => 'background-images/6777a78274709.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 17,
                'sample_url' => 'background-images/6777a79063175.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 18,
                'sample_url' => 'background-images/6777a7a3e7af1.png',
                'category' => 'noel',
            ],
            [
                'id' => 19,
                'sample_url' => 'background-images/6777a7c4b7e41.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 20,
                'sample_url' => 'background-images/6777a7cea558b.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 21,
                'sample_url' => 'background-images/6777a7d981fc0.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 22,
                'sample_url' => 'background-images/6777a7e3e67c8.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 23,
                'sample_url' => 'background-images/6777a7ef20134.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 24,
                'sample_url' => 'background-images/6777a7f8ebe3d.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 25,
                'sample_url' => 'background-images/6777a80a30aef.png',
                'category' => 'noel',
            ],
            [
                'id' => 26,
                'sample_url' => 'background-images/6777a81a64df8.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 27,
                'sample_url' => 'background-images/6777a825154e5.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 28,
                'sample_url' => 'background-images/6777a83aadb4f.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 29,
                'sample_url' => 'background-images/6777a846a2fd0.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 30,
                'sample_url' => 'background-images/6777a85b46bfc.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 31,
                'sample_url' => 'background-images/6777a86615893.png',
                'category' => 'noel',
            ],
            [
                'id' => 32,
                'sample_url' => 'background-images/6777a87749ce4.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 33,
                'sample_url' => 'background-images/6777a87fa9e94.png',
                'category' => 'noel',
            ],
            [
                'id' => 34,
                'sample_url' => 'background-images/6777a891ee9a8.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 35,
                'sample_url' => 'background-images/6777a8af768d9.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 36,
                'sample_url' => 'background-images/6777a8b8ee230.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 37,
                'sample_url' => 'background-images/6777a8c1c016f.png',
                'category' => 'noel',
            ],
            [
                'id' => 38,
                'sample_url' => 'background-images/6777a8d9334e8.jpg',
                'category' => 'noel',
            ],
            [
                'id' => 39,
                'sample_url' => 'background-images/6777a8f761756.png',
                'category' => 'noel',
            ],
            [
                'id' => 40,
                'sample_url' => 'background-images/6777ae4d453a9.png',
                'category' => 'noel',
            ],
            [
                'id' => 41,
                'sample_url' => 'background-images/6777ae5b3264e.jpg',
                'category' => 'noel',
            ]
        ];

        foreach ($backgrounds as $background) {
            // Kiểm tra xem bản ghi đã tồn tại chưa dựa trên id
            $exists = BackgroundImage::where('id', $background['id'])->exists();

            if (!$exists) {
                BackgroundImage::create($background);
            }
        }
    }
}

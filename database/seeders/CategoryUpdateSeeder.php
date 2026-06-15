<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->truncate();

        $fields = [
            ['name' => 'Y tế', 'image' => '/assets/img/EnterpriseField/medical.png'],
            ['name' => 'Giáo dục', 'image' => '/assets/img/EnterpriseField/education.png'],
            ['name' => 'Thể thao', 'image' => '/assets/img/EnterpriseField/sport.png'],
            ['name' => 'Ẩm thực', 'image' => '/assets/img/EnterpriseField/food.png'],
            ['name' => 'Hội họa', 'image' => '/assets/img/EnterpriseField/art.png'],
            ['name' => 'Làm đẹp', 'image' => '/assets/img/EnterpriseField/beauty.png'],
            ['name' => 'Kinh tế', 'image' => '/assets/img/EnterpriseField/economic.png'],
            ['name' => 'Âm nhạc', 'image' => '/assets/img/EnterpriseField/music.png'],
            ['name' => 'Lĩnh vực khác', 'image' => '/assets/img/EnterpriseField/other-field.png'],
        ];

        foreach ($fields as $field) {
            Category::create($field);
        }
    }
}

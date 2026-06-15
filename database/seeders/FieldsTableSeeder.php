<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;

class FieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            Field::create($field);
        }
    }
}

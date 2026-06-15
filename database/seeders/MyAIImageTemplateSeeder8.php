<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder8 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder8
     */
    public function run(): void
    {
        try {
            // Thể thao
            $category = [
                'path'=> 'storage/app/private/public/template/the_thao',
                'slug' => 'the_thao',
                'title' => 'Thể thao',
                'my_ai_image_collection_id' => 3,
                'total' => 17,
            ];

            $record = MyAIImageTemplateCategory::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'title' => $category['title'],
                    'my_ai_image_collection_id' => $category['my_ai_image_collection_id'],
                    'slug' => $category['slug']
                ]
            );

            for ($index = 1; $index <= $category['total']; $index++) {
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . "_v2.jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}

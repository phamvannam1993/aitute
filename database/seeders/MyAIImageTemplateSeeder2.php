<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplateCategory;  // Import model category nếu có
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder2
     */
    public function run(): void
    {
        $categories = [
            'tet-nu' => 'Tết Nữ',
            'noel-nu' => 'Giáng Sinh Nữ',
            'tet-be-gai' => 'Tết Cho Bé Gái'
        ];

        DB::beginTransaction();

        try {
            foreach ($categories as $slug => $title) {
                $category = MyAIImageTemplateCategory::firstOrCreate(
                    ['slug' => $slug], 
                    ['title' => $title, 'my_ai_image_collection_id' => 1]
                );
            }

            foreach ($categories as $slug => $title) {
                $category = MyAIImageTemplateCategory::where('slug', $slug)->first();

                if ($category) {
                    DB::table('my_ai_image_templates')
                        ->where('s3_url', 'like', '%' . $slug . '%')
                        ->update(['my_ai_image_template_category_id' => $category->id]);
                }
            }

            DB::commit();
            echo "Categories created and my_ai_image_template_category_id updated successfully.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}

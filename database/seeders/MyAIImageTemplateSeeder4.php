<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder4 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder4
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Find records matching the criteria
            $records = MyAIImageTemplate::whereIn('s3_url', [
                    'image_generate/tet-nu_1.jpeg',
                    'image_generate/tet-nu_2.jpeg'
                ])
                ->where('my_ai_image_template_category_id', 1)
                ->get();

            // Delete the records
            foreach ($records as $record) {
                $record->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception
            dd($e->getMessage());
        }
    }
}

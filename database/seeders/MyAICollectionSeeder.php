<?php

namespace Database\Seeders;

use App\Models\MyAIImageCollection;
use App\Models\MyAIImageTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MyAICollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAICollectionSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $fileName = "my_ai_image_collection/festival.jpg";

            MyAIImageCollection::create([
                'title' => "BỘ SƯU TẬP ẢNH NGÀY LỄ",
                's3_url' => $fileName,
            ]);

            $fileName = "my_ai_image_collection/memory.jpg";

            MyAIImageCollection::create([
                'title' => "BỘ SƯU TẬP ẢNH KỶ NIỆM",
                's3_url' => $fileName,
            ]);

            $fileName = "my_ai_image_collection/avatar.jpg";

            MyAIImageCollection::create([
                'title' => "BỘ SƯU TẬP ẢNH CHÂN DUNG",
                's3_url' => $fileName,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}

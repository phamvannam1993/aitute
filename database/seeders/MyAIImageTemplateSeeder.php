<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MyAIImageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder
     */
    public function run(): void
    {
        $categories = [
            'noel-nu' => 'storage/app/private/public/template/noel-nu',
            'tet-nu' => 'storage/app/private/public/template/tet-nu',
            'tet-be-gai' => 'storage/app/private/public/template/tet-be-gai',
        ];

        DB::beginTransaction();

        try {
            foreach ($categories as $category => $localFolder) {
                $files = scandir($localFolder);

                $imageFiles = array_filter($files, function ($file) use ($localFolder) {
                    return is_file($localFolder . '/' . $file) && str_ends_with(strtolower($file), '.jpeg');
                });

                foreach (array_values($imageFiles) as $index => $imageFile) {
                    $imagePath = $localFolder . '/' . $imageFile;

                    $fileContent = file_get_contents($imagePath);

                    $fileName = "image_generate/{$category}_" . ($index + 1) . ".jpeg";

                    Storage::disk('s3')->put($fileName, $fileContent);

                    MyAIImageTemplate::create([
                        'category' => $category,
                        's3_url' => $fileName,
                    ]);

                    echo "Uploaded and saved: {$imageFile} -> {$fileName}\n";
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}

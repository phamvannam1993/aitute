<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder7 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder7
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MyAIImageTemplate::truncate();
        MyAIImageTemplateCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::beginTransaction();

        try {

             // Ảnh tết
             $category  = [
                'slug' => 'tet-nu',
                'title' => 'Mẫu tết dành cho nữ',
                'my_ai_image_collection_id' => 1,
                'total' => 58,
            ];

            $record = MyAIImageTemplateCategory::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'title' => $category['title'],
                    'my_ai_image_collection_id' => $category['my_ai_image_collection_id'],
                    'slug' => $category['slug']
                ]
            );

            for ($index = 3; $index <= 54; $index++) {
                $fileName = "image_generate/tet-nu_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            for ($index = 1; $index <= 57; $index++) {
                $fileName = "image_generate/tet_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Giáng sinh
            $category  = [
                'slug' => 'noel-nu',
                'title' => 'Mẫu giáng sinh',
                'my_ai_image_collection_id' => 1,
                'total' => 58,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            for ($index = 1; $index <= 14; $index++) {
                $fileName = "image_generate/noel_nu_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Đám cưới
            $category = [
                'path'=> 'storage/app/private/public/template/dam-cuoi',
                'slug' => 'dam-cuoi',
                'title' => 'Đám Cưới',
                'my_ai_image_collection_id' => 2,
                'total' => 50,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            for ($index = 1; $index <= 22; $index++) {
                $fileName = "image_generate/ngay_cuoi_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Doanh nhân
            $category = [
                'path'=> 'storage/app/private/public/template/doanh-nhan',
                'slug' => 'doanh-nhan',
                'title' => 'Doanh Nhân',
                'my_ai_image_collection_id' => 3,
                'total' => 50,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            for ($index = 1; $index <= 22; $index++) {
                $fileName = "image_generate/doanh_nhan_thanh_dat_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Học sinh

            $category = [
                'path'=> 'storage/app/private/public/template/hoc-sinh',
                'slug' => 'hoc-sinh',
                'title' => 'Học Sinh',
                'my_ai_image_collection_id' => 2,
                'total' => 50,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Quốc tế phụ nữ

            $category = [
                'path'=> 'storage/app/private/public/template/quoc-te-phu-nu',
                'slug' => 'quoc-te-phu-nu',
                'title' => 'Quốc Tế Phụ Nữ',
                'my_ai_image_collection_id' => 1,
                'total' => 50,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            }

            // Quốc tế thiếu nhi

            $category = [
                'path'=> 'storage/app/private/public/template/quoc-te-thieu-nhi',
                'slug' => 'quoc-te-thieu-nhi',
                'title' => 'Quốc Tế Thiếu Nhi',
                'my_ai_image_collection_id' => 1,
                'total' => 50,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Tết trung thu

            $category = [
                'path'=> 'storage/app/private/public/template/trung-thu',
                'slug' => 'trung-thu',
                'title' => 'Tết Trung Thu',
                'my_ai_image_collection_id' => 1,
                'total' => 30,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Tuổi thơ

            $category = [
                'path'=> 'storage/app/private/public/template/tuoi-tho',
                'slug' => 'tuoi-tho',
                'title' => 'Tuổi Thơ',
                'my_ai_image_collection_id' => 2,
                'total' => 22,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Em bé chào đời

            $category = [
                'path'=> 'storage/app/private/public/template/em_be_chao_doi',
                'slug' => 'em_be_chao_doi',
                'title' => 'Em bé chào đời',
                'my_ai_image_collection_id' => 2,
                'total' => 6,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };


            // Mẫu ảnh tự do

            $category = [
                'path'=> 'storage/app/private/public/template/mau_anh_tu_do',
                'slug' => 'mau_anh_tu_do',
                'title' => 'Mẫu ảnh tự do',
                'my_ai_image_collection_id' => 3,
                'total' => 29,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Ngày lễ tốt nghiệp

            $category = [
                'path'=> 'storage/app/private/public/template/ngay_le_tot_nghiep',
                'slug' => 'ngay_le_tot_nghiep',
                'title' => 'Ngày lễ tốt nghiệp',
                'my_ai_image_collection_id' => 1,
                'total' => 25,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Sinh nhật

            $category = [
                'path'=> 'storage/app/private/public/template/sinh_nhat',
                'slug' => 'sinh_nhat',
                'title' => 'Sinh Nhật',
                'my_ai_image_collection_id' => 2,
                'total' => 13,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Thể thao

            $category = [
                'path'=> 'storage/app/private/public/template/the_thao',
                'slug' => 'the_thao',
                'title' => 'Thể thao',
                'my_ai_image_collection_id' => 3,
                'total' => 1,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

                MyAIImageTemplate::create([
                    'my_ai_image_template_category_id' => $record->id,
                    's3_url' => $fileName,
                ]);

                echo "Saved: {$fileName}\n";
            };

            // Thời trang

            $category = [
                'path'=> 'storage/app/private/public/template/thoi_trang_luxury',
                'slug' => 'thoi_trang_luxury',
                'title' => 'Thời trang xa xỉ',
                'my_ai_image_collection_id' => 3,
                'total' => 5,
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
                $fileName = "image_generate/" . $category['slug'] . "_" . $index . ".jpeg";

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

<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder3
     */
    public function run(): void
    {
        $categories = [
            'doanh-nhan-thanh-dat' => [
                'path'=> 'storage/app/private/public/template/doanh_nhan_thanh_dat',
                'slug' => 'doanh-nhan-thanh-dat',
                'title' => 'Doanh nhân thành đạt',
                'my_ai_image_collection_id' => 3,
                'total' => 50,
            ],
            'em-be-chao-doi' => [
                'path'=> 'storage/app/private/public/template/em_be_chao_doi',
                'slug' => 'em-be-chao-doi',
                'title' => 'Em bé chào đời',
                'my_ai_image_collection_id' => 2,
                'total' => 11,
            ],
            'mau-anh-tu-do' => [
                'path'=> 'storage/app/private/public/template/mau_anh_tu_do',
                'slug' => 'mau-anh-tu-do',
                'title' => 'Mẫu ảnh tự do',
                'my_ai_image_collection_id' => 3,
                'total' => 57,
            ],
            'ngay-cuoi' => [
                'path'=> 'storage/app/private/public/template/ngay_cuoi',
                'slug' => 'ngay-cuoi',
                'title' => 'Ngày cưới',
                'my_ai_image_collection_id' => 2,
                'total' => 42,
            ],
            'ngay-le-tot-nghiep' => [
                'path'=> 'storage/app/private/public/template/ngay_le_tot_nghiep',
                'slug' => 'ngay-le-tot-nghiep',
                'title' => 'Ngày lễ tốt nghiệp',
                'my_ai_image_collection_id' => 1,
                'total' => 42,
            ],
            'noel-nu' => [
                'path'=> 'storage/app/private/public/template/noel_nu',
                'slug' => 'noel-nu',
                'title' => 'Lễ Noel',
                'my_ai_image_collection_id' => 1,
                'total' => 29,
            ],
            'sinh-nhat' => [
                'path'=> 'storage/app/private/public/template/sinh_nhat',
                'slug' => 'sinh-nhat',
                'title' => 'Sinh Nhật',
                'my_ai_image_collection_id' => 2,
                'total' => 22,
            ],
            'tet' => [
                'path'=> 'storage/app/private/public/template/tet',
                'slug' => 'tet',
                'title' => 'Tết',
                'my_ai_image_collection_id' => 1,
                'total' => 96,
            ],
            'the-thao' => [
                'path'=> 'storage/app/private/public/template/the_thao',
                'slug' => 'the-thao',
                'title' => 'Thể thao',
                'my_ai_image_collection_id' => 3,
                'total' => 2,
            ],
            'thoi-trang-luxury' => [
                'path'=> 'storage/app/private/public/template/thoi_trang_luxury',
                'slug' => 'thoi-trang-luxury',
                'title' => 'Thời trang xa xỉ',
                'my_ai_image_collection_id' => 3,
                'total' => 6,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($categories as $category) {
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
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}

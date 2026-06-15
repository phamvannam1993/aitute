<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplate;
use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateSeeder6 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageTemplateSeeder6
     */
    public function run(): void
    {
        $categories = [
            'doanh_nhan_thanh_dat' => [
                'path'=> 'storage/app/private/public/template/doanh_nhan_thanh_dat',
                'slug' => 'doanh_nhan_thanh_dat',
                'title' => 'Doanh nhân thành đạt',
                'my_ai_image_collection_id' => 3,
                'total' => 25,
            ],
            'em_be_chao_doi' => [
                'path'=> 'storage/app/private/public/template/em_be_chao_doi',
                'slug' => 'em_be_chao_doi',
                'title' => 'Em bé chào đời',
                'my_ai_image_collection_id' => 2,
                'total' => 6,
            ],
            'mau_anh_tu_do' => [
                'path'=> 'storage/app/private/public/template/mau_anh_tu_do',
                'slug' => 'mau_anh_tu_do',
                'title' => 'Mẫu ảnh tự do',
                'my_ai_image_collection_id' => 3,
                'total' => 29,
            ],
            'ngay_cuoi' => [
                'path'=> 'storage/app/private/public/template/ngay_cuoi',
                'slug' => 'ngay_cuoi',
                'title' => 'Ngày cưới',
                'my_ai_image_collection_id' => 2,
                'total' => 22,
            ],
            'ngay_le_tot_nghiep' => [
                'path'=> 'storage/app/private/public/template/ngay_le_tot_nghiep',
                'slug' => 'ngay_le_tot_nghiep',
                'title' => 'Ngày lễ tốt nghiệp',
                'my_ai_image_collection_id' => 1,
                'total' => 25,
            ],
            'noel_nu' => [
                'path'=> 'storage/app/private/public/template/noel_nu',
                'slug' => 'noel_nu',
                'title' => 'Lễ Noel',
                'my_ai_image_collection_id' => 1,
                'total' => 14,
            ],
            'sinh_nhat' => [
                'path'=> 'storage/app/private/public/template/sinh_nhat',
                'slug' => 'sinh_nhat',
                'title' => 'Sinh Nhật',
                'my_ai_image_collection_id' => 2,
                'total' => 13,
            ],
            'tet' => [
                'path'=> 'storage/app/private/public/template/tet',
                'slug' => 'tet',
                'title' => 'Tết',
                'my_ai_image_collection_id' => 1,
                'total' => 57,
            ],
            'the_thao' => [
                'path'=> 'storage/app/private/public/template/the_thao',
                'slug' => 'the_thao',
                'title' => 'Thể thao',
                'my_ai_image_collection_id' => 3,
                'total' => 1,
            ],
            'thoi_trang_luxury' => [
                'path'=> 'storage/app/private/public/template/thoi_trang_luxury',
                'slug' => 'thoi_trang_luxury',
                'title' => 'Thời trang xa xỉ',
                'my_ai_image_collection_id' => 3,
                'total' => 5,
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

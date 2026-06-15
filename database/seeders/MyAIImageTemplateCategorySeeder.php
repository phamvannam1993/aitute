<?php

namespace Database\Seeders;

use App\Models\MyAIImageTemplateCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MyAIImageTemplateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=MyAIImageTemplateCategorySeeder
     */
    public function run()
    {
        // Define the manual order mapping for slugs
        $orders = [
            'tet-nu' => 1,
            'mau-nu-toc-ngan' => 2,
            'doanh-nhan' => 3,
            'mau_anh_tu_do' => 4,
            'thoi_trang_luxury' => 5,
            'sinh_nhat' => 6,
            'quoc-te-phu-nu' => 7,
            'dam-cuoi' => 8,
            'hoc-sinh' => 9,
            'ngay_le_tot_nghiep' => 10,
            'the_thao' => 11,
            'tuoi-tho' => 12,
            'em_be_chao_doi' => 13,
            'trung-thu' => 14,
            'noel-nu' => 15,
            'quoc-te-thieu-nhi' => 16,
        ];

        foreach ($orders as $slug => $order) {
            MyAIImageTemplateCategory::where('slug', $slug)->update(['order' => $order]);
        }
    }

}
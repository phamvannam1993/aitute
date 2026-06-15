<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder03 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder03
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "b1e5a107-b865-4517-bc31-a43a820c3be1";
            $template->title = "Sản phẩm khuyến mãi";
            $template->category = 'COMMERCE';
            $template->template_thumbnail = "/assets/img/creatomate/template3.png";
            $template->structure = '{
                "composition": {
                    "Time": {
                        "value": "",
                        "type": "text",
                        "label": "Thời gian"
                    },
                    "Brandname1": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 1"
                    },
                    "Itemtitle1": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề sản phẩm 1"
                    },
                    "Brandname2": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 2"
                    },
                    "Itemtitle2": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề sản phẩm 2"
                    },
                    "Promotext": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung khuyến mãi"
                    },
                    "Itemimage1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 1"
                    },
                    "Itemimage2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 2"
                    },
                    "Productoffer1": {
                        "value": "",
                        "type": "text",
                        "label": "Ưu đãi sản phẩm 1"
                    },
                    "Productimage1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh ưu đãi 1"
                    },
                    "Productoffer2": {
                        "value": "",
                        "type": "text",
                        "label": "Ưu đãi sản phẩm 2"
                    },
                    "Productimage2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh ưu đãi 2"
                    },
                    "Productoffer3": {
                        "value": "",
                        "type": "text",
                        "label": "Ưu đãi sản phẩm 3"
                    },
                    "Productimage3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh ưu đãi 3"
                    },
                    "Website": {
                        "value": "",
                        "type": "text",
                        "label": "Trang web"
                    },
                    "Brandname3": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 3"
                    },
                    "Audio": {
                        "value": "",
                        "type": "audio",
                        "label": "Âm thanh"
                    }
                }
            }';
            $template->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}

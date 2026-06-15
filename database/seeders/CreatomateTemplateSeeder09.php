<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder09 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder09
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "ddabbf5f-6f81-4a4c-846e-05337b5a3258";
            $template->title = "Quảng cáo sản phẩm";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::COMMERCE;
            $template->template_thumbnail = "/assets/img/creatomate/template9.png";
            $template->structure = '{
                "composition": {
                    "Productname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm"
                    },
                    "Brandname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu"
                    },
                    "Details": {
                        "value": "",
                        "type": "text",
                        "label": "Chi tiết sản phẩm"
                    },
                    "Price": {
                        "value": "",
                        "type": "text",
                        "label": "Giá sản phẩm"
                    },
                    "Buynow": {
                        "value": "",
                        "type": "text",
                        "label": "Nút mua ngay"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 1"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 2"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 3"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 4"
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

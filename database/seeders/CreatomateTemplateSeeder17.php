<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder17 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder17
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "8eacd668-d332-4985-849a-a38d70db45fd";
            $template->title = "Quảng cáo đa sản phẩm";
            $template->resolution = "1:1";
            $template->category = CreatomateCategory::COMMERCE;
            $template->template_thumbnail = "/assets/img/creatomate/template17.png";
            $template->structure = '{
                "composition": {
                    "Title": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề"
                    },
                    "Brandname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu"
                    },
                    "Productescription": {
                        "value": "",
                        "type": "text",
                        "label": "Mô tả sản phẩm"
                    },
                    "Productname1": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm 1"
                    },
                    "Productprice1": {
                        "value": "",
                        "type": "text",
                        "label": "Giá sản phẩm 1"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Productname2": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm 2"
                    },
                    "Productprice2": {
                        "value": "",
                        "type": "text",
                        "label": "Giá sản phẩm 2"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Productname3": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm 3"
                    },
                    "Productprice3": {
                        "value": "",
                        "type": "text",
                        "label": "Giá sản phẩm 3"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
                    },
                    "Productname4": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm 4"
                    },
                    "Productprice4": {
                        "value": "",
                        "type": "text",
                        "label": "Giá sản phẩm 4"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 4"
                    },
                    "Logo": {
                        "value": "",
                        "type": "image",
                        "label": "Logo"
                    },
                    "Audio": {
                        "value": "",
                        "type": "audio",
                        "label": "Nhạc nền"
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

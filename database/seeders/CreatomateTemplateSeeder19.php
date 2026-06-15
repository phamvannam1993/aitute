<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder19 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder19
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "d29e06ac-3131-48ab-a75d-88453f78a3e8";
            $template->title = "Câu chuyện bằng ảnh";
            $template->resolution = "1:1";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template19.png";
            $template->structure = '{
                "composition": {
                    "Slogan": {
                        "value": "",
                        "type": "text",
                        "label": "Khẩu hiệu"
                    },
                    "Logo": {
                        "value": "",
                        "type": "image",
                        "label": "Logo"
                    },
                    "Brandname1": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 1"
                    },
                    "Brandname2": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 2"
                    },
                    "Productdescription1": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin sản phẩm 1"
                    },
                    "Productdescription2": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin sản phẩm 2"
                    },
                    "Productdescription3": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin sản phẩm 3"
                    },
                    "Productdescription4": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin sản phẩm 4"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 4"
                    },
                    "Image5": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 5"
                    },
                    "Image6": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 6"
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

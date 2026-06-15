<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder08 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder08
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "22d6f0eb-48f1-4aef-af6d-c0ad81e377b0";
            $template->title = "Giới thiệu sản phẩm";
            $template->category = CreatomateCategory::COMMERCE;
            $template->template_thumbnail = "/assets/img/creatomate/template8.png";
            $template->structure = '{
                "composition": {
                    "Productname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm"
                    },
                    "Buy": {
                        "value": "",
                        "type": "text",
                        "label": "Nút mua hàng"
                    },
                    "Brandname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu"
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

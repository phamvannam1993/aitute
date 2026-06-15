<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder10 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder10
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "ef6087f8-53bd-462e-90b5-91d1669c3083";
            $template->title = "Giới thiệu sản phẩm và logo thương hiệu";
            $template->resolution = "1:1";
            $template->category = CreatomateCategory::COMMERCE;
            $template->template_thumbnail = "/assets/img/creatomate/template10.png";
            $template->structure = '{
                "composition": {
                    "Slogan1": {
                        "value": "",
                        "type": "text",
                        "label": "Câu khẩu hiệu 1"
                    },
                    "Slogan2": {
                        "value": "",
                        "type": "text",
                        "label": "Câu khẩu hiệu 2"
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
                    "Image5": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 5"
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

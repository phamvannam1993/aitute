<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder18 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder18
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "603dbc7b-ba97-4a4b-88ec-b4c15accad6f";
            $template->title = "Giới thiệu bản thân";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::INTRODUCTION;
            $template->template_thumbnail = "/assets/img/creatomate/template18.png";
            $template->structure = '{
                "composition": {
                    "Name": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Contact": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin liên hệ"
                    },
                    "Avatar": {
                        "value": "",
                        "type": "image",
                        "label": "Ảnh đại diện"
                    },
                    "Title1": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề 1"
                    },
                    "Title2": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề 2"
                    },
                    "Infomation1": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin 1"
                    },
                    "Infomation2": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin 2"
                    },
                    "Infomation3": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin 3"
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

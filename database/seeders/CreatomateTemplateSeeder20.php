<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder20 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder20
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "ef5fb187-7a33-4737-9b1b-d97257c4e9d5";
            $template->title = "Trình chiếu hình ảnh kèm phần mở đầu và kết thúc";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::INTRODUCTION;
            $template->template_thumbnail = "/assets/img/creatomate/template20.png";
            $template->structure = '{
                "composition": {
                    "Brandname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên Thương hiệu"
                    },
                    "Image": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh"
                    },
                    "Title1": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề 1"
                    },
                    "Text1": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản 1"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Title2": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề 2"
                    },
                    "Text2": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản 2"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Title3": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề 3"
                    },
                    "Text3": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản 3"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
                    },
                    "Text4": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản 4"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 4"
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

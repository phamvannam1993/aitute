<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder04 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder04
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "8a55463b-4d35-4d6e-ad6a-f56636f37d42";
            $template->title = "Khuyến mãi nhanh";
            $template->category = 'LANDSCAPE';
            $template->template_thumbnail = "/assets/img/creatomate/template4.png";
            $template->structure = '{
                "composition": {
                    "Subjectmessage": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề thông điệp"
                    },
                    "Introductorymessage": {
                        "value": "",
                        "type": "text",
                        "label": "Thông điệp giới thiệu"
                    },
                    "Text": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung văn bản"
                    },
                    "Image": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh"
                    },
                    "Video": {
                        "value": "",
                        "type": "video",
                        "label": "Video"
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

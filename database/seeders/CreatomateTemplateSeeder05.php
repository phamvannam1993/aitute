<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder05 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder05
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "9c4dc50a-c891-49b3-8cd9-7d280a292fee";
            $template->title = "Ví dụ khởi đầu nhanh";
            $template->category = 'LANDSCAPE';
            $template->template_thumbnail = "/assets/img/creatomate/template5.png";
            $template->structure = '{
                "composition": {
                    "Link": {
                        "value": "",
                        "type": "text",
                        "label": "Liên kết"
                    },
                    "Contentmessage1": {
                        "value": "",
                        "type": "text",
                        "label": "Thông điệp nội dung 1"
                    },
                    "Contentmessage2": {
                        "value": "",
                        "type": "text",
                        "label": "Thông điệp nội dung 2"
                    },
                    "Openingmessage1": {
                        "value": "",
                        "type": "text",
                        "label": "Lời mở đầu 1"
                    },
                    "Background": {
                        "value": "",
                        "type": "image",
                        "label": "Hình nền"
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

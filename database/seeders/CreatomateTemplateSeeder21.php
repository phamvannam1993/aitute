<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder21 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder21
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "3155850e-d41a-4164-bd3c-45a7822be889";
            $template->title = "Trích dẫn kèm video";
            $template->resolution = "1:1";
            $template->category = CreatomateCategory::SOCIETY;
            $template->template_thumbnail = "/assets/img/creatomate/template21.png";
            $template->structure = '{
                "composition": {
                    "Message": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn"
                    },
                    "Name": {
                        "value": "",
                        "type": "text",
                        "label": "Tên hiển thị"
                    },
                    "Handle": {
                        "value": "",
                        "type": "text",
                        "label": "Tên người dùng"
                    },
                    "Picture": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh"
                    },
                    "Background": {
                        "value": "",
                        "type": "video",
                        "label": "Video nền"
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

<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder14 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder14
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "4131722f-1416-46cd-bfdc-b113cc0c93cc";
            $template->title = "Văn bản đơn giản kèm video";
            $template->resolution = "1:1";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template14.png";
            $template->structure = '{
                "composition": {
                    "Name": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Message1": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn 1"
                    },
                    "Message2": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn 2"
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

<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder25 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder25
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "d6295192-028f-4f94-8ea4-a76f7777f364";
            $template->title = "Tổng hợp video (4 video)";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template25.png";
            $template->structure = '{
                "composition": {
                    "Video1": {
                        "value": "",
                        "type": "video",
                        "label": "Video 1"
                    },
                    "Video2": {
                        "value": "",
                        "type": "video",
                        "label": "Video 2"
                    },
                    "Video3": {
                        "value": "",
                        "type": "video",
                        "label": "Video 3"
                    },
                    "Video4": {
                        "value": "",
                        "type": "video",
                        "label": "Video 4"
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

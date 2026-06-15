<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder26 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder26
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "c590052f-8b54-44f3-929c-a826c0aee6c0";
            $template->title = "Câu chuyện với 5 sự thật thú vị";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::COMPILATIONVIDEO;
            $template->template_thumbnail = "/assets/img/creatomate/template26.png";
            $template->structure = '{
                "composition": {
                    "Introductorytext": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản giới thiệu"
                    },
                    "Content1": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 1"
                    },
                    "Content2": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 2"
                    },
                    "content3": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 3"
                    },
                    "content4": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 4"
                    },
                    "content5": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 5"
                    },
                    "OutroText": {
                        "value": "",
                        "type": "text",
                        "label": "Văn bản kết thúc"
                    },
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

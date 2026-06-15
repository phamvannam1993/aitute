<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder16 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder16
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "8eacd668-d332-4985-849a-a38d70db45fd";
            $template->title = "Lồng tiếng ngắn gọn";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::FOOD;
            $template->template_thumbnail = "/assets/img/creatomate/template16.png";
            $template->structure = '{
                "composition": {
                    "Content1": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 1"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Voiceover1": {
                        "value": "",
                        "type": "audio",
                        "label": "Âm thanh 1"
                    },
                    "Content2": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 2"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Voiceover2": {
                        "value": "",
                        "type": "audio",
                        "label": "Âm thanh 2"
                    },
                    "Content3": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 3"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
                    },
                    "Voiceover3": {
                        "value": "",
                        "type": "audio",
                        "label": "Âm thanh 3"
                    },
                    "Content4": {
                        "value": "",
                        "type": "text",
                        "label": "Nội dung 4"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 4"
                    },
                    "Voiceover4": {
                        "value": "",
                        "type": "audio",
                        "label": "Âm thanh 4"
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

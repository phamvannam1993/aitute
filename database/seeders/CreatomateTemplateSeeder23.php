<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder23 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder23
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "c281501d-c38f-447a-b9fa-afd70c2c907d";
            $template->title = "Tổng hợp video (2 video)";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template23.png";
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

<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder24 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder24
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "89611e41-00a9-42e3-941b-a1ae6ffbf42f";
            $template->title = "Tổng hợp video (3 video)";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template24.png";
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

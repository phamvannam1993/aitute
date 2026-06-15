<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder15 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder15
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "c15c99ed-bedc-4fb9-802c-4a11251be359";
            $template->title = "Trình chiếu hình ảnh (3 hình ảnh)";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template15.png";
            $template->structure = '{
                "composition": {
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
                    },
                    "Music": {
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

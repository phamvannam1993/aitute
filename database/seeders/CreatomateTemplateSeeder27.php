<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder27 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder27
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "373fb78d-05c7-47d2-8f2d-28cff3ed0542";
            $template->title = "Cuộc thi ảnh";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::PHOTO;
            $template->template_thumbnail = "/assets/img/creatomate/template27.png";
            $template->structure = '{
                "composition": {
                    "Title": {
                        "value": "",
                        "type": "text",
                        "label": "Chủ đề"
                    },
                    "Nameofphoto": {
                        "value": "",
                        "type": "text",
                        "label": "Tên hình ảnh"
                    },
                    "Winnersname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Image": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh"
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

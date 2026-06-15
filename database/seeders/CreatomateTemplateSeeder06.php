<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder06 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder06
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "45f83383-5507-4643-a5d2-40e86060c429";
            $template->title = "Giới thiệu thương hiệu";
            $template->category = 'LANDSCAPE';
            $template->template_thumbnail = "/assets/img/creatomate/template6.png";
            $template->structure = '{
                "composition": {
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Slogan1": {
                        "value": "",
                        "type": "text",
                        "label": "Slogan 1"
                    },
                    "BrandName1": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 1"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
                    },
                    "Slogan2": {
                        "value": "",
                        "type": "text",
                        "label": "Slogan 2"
                    },
                    "BrandName2": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 2"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 3"
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

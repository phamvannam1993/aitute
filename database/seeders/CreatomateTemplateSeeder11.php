<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder11 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder11
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "2204ec08-df2f-4625-b4f0-cfc3ec9ab93a";
            $template->title = "Video kể chuyện";
            $template->resolution = "4:5";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template11.png";
            $template->structure = '{
                "composition": {
                    "Slogan1": {
                        "value": "",
                        "type": "text",
                        "label": "Câu khẩu hiệu 1"
                    },
                    "Slogan2": {
                        "value": "",
                        "type": "text",
                        "label": "Câu khẩu hiệu 2"
                    },
                    "Brandname1": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 1"
                    },
                    "Brandname2": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu 2"
                    },
                    "Image1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 1"
                    },
                    "Image2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 2"
                    },
                    "Image3": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 3"
                    },
                    "Image4": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 4"
                    },
                    "Image5": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 5"
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

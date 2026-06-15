<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder13 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder13
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "8edd268b-5fea-49ff-bb7f-e7c9343c0e64";
            $template->title = "Bộ ba ảnh động";
            $template->resolution = "16:9";
            $template->category = CreatomateCategory::LANDSCAPE;
            $template->template_thumbnail = "/assets/img/creatomate/template13.png";
            $template->structure = '{
                "composition": {
                    "Nameofirstimage": {
                        "value": "",
                        "type": "text",
                        "label": "Tên hình ảnh 1"
                    },
                    "Nameofthesecondimage": {
                        "value": "",
                        "type": "text",
                        "label": "Tên hình ảnh 2"
                    },
                    "Thirdimagename": {
                        "value": "",
                        "type": "text",
                        "label": "Tên hình ảnh 3"
                    },
                    "Firstintroductorymessage": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn giới thiệu 1"
                    },
                    "Secondintroductorymessage": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn giới thiệu 2"
                    },
                    "Thirdintroductorymessage": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn giới thiệu 3"
                    },
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

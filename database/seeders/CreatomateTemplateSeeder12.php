<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder12 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder12
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "05b908a5-51ac-4e06-a3ed-f30e9830ca89";
            $template->title = "Đánh giá hoạt hình";
            $template->resolution = "4:5";
            $template->category = CreatomateCategory::SOCIETY;
            $template->template_thumbnail = "/assets/img/creatomate/template12.png";
            $template->structure = '{
                "composition": {
                    "Comment": {
                        "value": "",
                        "type": "text",
                        "label": "Bình luận"
                    },
                    "Date": {
                        "value": "",
                        "type": "text",
                        "label": "Ngày"
                    },
                    "Name": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Profilepicture": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh đại diện"
                    },
                    "Photo1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 1"
                    },
                    "Photo2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh 2"
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

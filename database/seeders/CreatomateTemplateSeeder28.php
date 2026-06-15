<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder28 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder28
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "68f2abf2-f72d-4998-b307-a760cee9e9a9";
            $template->title = "Thông báo Twitter";
            $template->resolution = "9:16";
            $template->category = CreatomateCategory::SOCIETY;
            $template->template_thumbnail = "/assets/img/creatomate/template28.png";
            $template->structure = '{
                "composition": {
                    "comment": {
                        "value": "",
                        "type": "text",
                        "label": "Bình luận"
                    },
                    "Now": {
                        "value": "",
                        "type": "text",
                        "label": "Thời gian"
                    },
                    "Companyname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Logo": {
                        "value": "",
                        "type": "image",
                        "label": "Logo"
                    },
                    "Image": {
                        "value": "",
                        "type": "image",
                        "label": "Image"
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

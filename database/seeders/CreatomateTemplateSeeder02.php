<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder02 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder02
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "12c40f00-5094-41e4-ade4-b1ddc7df2a02";
            $template->title = "Giới thiệu sản phẩm mới";
            $template->category = 'COMMERCE';
            $template->template_thumbnail = "/assets/img/creatomate/template2.png";
            $template->structure = '{
                "composition": {
                    "Itemdescription": {
                        "value": "",
                        "type": "text",
                        "label": "Mô tả sản phẩm"
                    },
                    "Productname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm"
                    },
                    "Itemtitle1": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề sản phẩm 1"
                    },
                    "Itemtitle2": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề sản phẩm 2"
                    },
                    "Slogan1": {
                        "value": "",
                        "type": "text",
                        "label": "Khẩu hiệu 1"
                    },
                    "Itemimage1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 1"
                    },
                    "Itemimage2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm 2"
                    },
                    "Slogan2": {
                        "value": "",
                        "type": "text",
                        "label": "Khẩu hiệu 2"
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

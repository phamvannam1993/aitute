<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder01 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder01
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "bdc52cd3-043d-4416-a3df-772866268c02";
            $template->title = "Sản phẩm nổi bật giảm giá";
            $template->category = 'COMMERCE';
            $template->template_thumbnail = "/assets/img/creatomate/template1.jpeg";
            $template->structure = '{
                "composition": {
                    "Alertmessage": {
                        "value": "",
                        "type": "text",
                        "label": "Thông báo"
                    },
                    "Currentprice": {
                        "value": "",
                        "type": "text",
                        "label": "Giá bán hiện tại"
                    },
                    "Originalprice": {
                        "value": "",
                        "type": "text",
                        "label": "Giá gốc"
                    },
                    "Itemdescription": {
                        "value": "",
                        "type": "text",
                        "label": "Mô tả sản phẩm"
                    },
                    "Itemtitle": {
                        "value": "",
                        "type": "text",
                        "label": "Tên sản phẩm"
                    },
                    "Promotext": {
                        "value": "",
                        "type": "text",
                        "label": "Thông điệp khuyến mãi"
                    },
                    "ItemImage": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh sản phẩm"
                    },
                    "Companywebsite": {
                        "value": "",
                        "type": "text",
                        "label": "Website công ty"
                    },
                    "Actionmessage": {
                        "value": "",
                        "type": "text",
                        "label": "Lời kêu gọi hành động"
                    },
                    "Background": {
                        "value": "",
                        "type": "image",
                        "label": "Hình nền"
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

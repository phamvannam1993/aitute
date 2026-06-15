<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder07 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder07
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "fa278d01-443e-4522-86f6-7d5aab9987ad";
            $template->title = "Khuyến mãi sản phẩm";
            $template->category = 'COMMERCE';
            $template->template_thumbnail = "/assets/img/creatomate/template7.png";
            $template->structure = '{
                "composition": {
                    "Time": {
                        "value": "",
                        "type": "text",
                        "label": "Thời gian khuyến mãi"
                    },
                    "Title": {
                        "value": "",
                        "type": "text",
                        "label": "Tiêu đề"
                    },
                    "Sale": {
                        "value": "",
                        "type": "text",
                        "label": "Thông tin giảm giá"
                    },
                    "Brandname": {
                        "value": "",
                        "type": "text",
                        "label": "Tên thương hiệu"
                    },
                    "Brandimage1": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh thương hiệu 1"
                    },
                    "Brandimage2": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh thương hiệu 2"
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

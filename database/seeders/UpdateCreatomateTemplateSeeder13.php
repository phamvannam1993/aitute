<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder13 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder13
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', '8edd268b-5fea-49ff-bb7f-e7c9343c0e64')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->structure = '{
                    "composition": {
                        "Nameoffirstimage": {
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
            } else {
                dd('Không tìm thấy template với ID được cung cấp.');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}

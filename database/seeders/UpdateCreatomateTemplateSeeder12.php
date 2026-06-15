<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder12 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder12
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', '05b908a5-51ac-4e06-a3ed-f30e9830ca89')->first();

            if ($template) {
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
                            "label": "Hình ảnh sản phẩm"
                        },
                        "Photo2": {
                            "value": "",
                            "type": "image",
                            "label": "Ảnh nền video"
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

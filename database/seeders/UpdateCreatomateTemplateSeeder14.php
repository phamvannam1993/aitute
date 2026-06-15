<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder14 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder14
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', '4131722f-1416-46cd-bfdc-b113cc0c93cc')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->structure = '{
                "composition": {
                    "Name": {
                        "value": "",
                        "type": "text",
                        "label": "Tên"
                    },
                    "Message1": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn 1"
                    },
                    "Message2": {
                        "value": "",
                        "type": "text",
                        "label": "Tin nhắn 2"
                    },
                    "Image": {
                        "value": "",
                        "type": "image",
                        "label": "Hình ảnh"
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

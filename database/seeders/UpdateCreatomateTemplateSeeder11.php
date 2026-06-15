<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder11 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder11
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', '2204ec08-df2f-4625-b4f0-cfc3ec9ab93a')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->structure = '{
                    "composition": {
                        "Link": {
                            "value": "",
                            "type": "text",
                            "label": "Liên kết"
                        },
                        "Content1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 1"
                        },
                        "Content2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 2"
                        },
                        "Background1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền 1"
                        },
                        "Background2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền 2"
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

<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder15 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder15
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', 'c15c99ed-bedc-4fb9-802c-4a11251be359')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->structure = '{
                    "composition": {
                        "Name1": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 1"
                        },
                        "Name2": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 2"
                        },
                        "Name3": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 3"
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

<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder08 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder08
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', '22d6f0eb-48f1-4aef-af6d-c0ad81e377b0')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->resolution = "9:16";
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

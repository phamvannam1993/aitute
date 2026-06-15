<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder01 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder01
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tìm template theo template_id
            $template = CreatomateTemplate::where('template_id', 'bdc52cd3-043d-4416-a3df-772866268c02')->first();

            if ($template) {
                // Thêm giá trị resolution
                $template->resolution = "16:9";
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

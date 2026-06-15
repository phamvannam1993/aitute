<?php

namespace Database\Seeders;

use App\Constants\CreatomateCategory;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateTemplateSeeder17 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateTemplateSeeder17
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = CreatomateTemplate::where('title', 'Quảng cáo đa sản phẩm')->first();

            if ($template) {
                $template->template_id = "79d44253-a167-402d-8fc9-889c994f1adc";
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

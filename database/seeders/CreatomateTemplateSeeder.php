<?php

namespace Database\Seeders;

use App\Models\AIPricing;
use App\Models\ConfigAff;
use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatomateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateTemplateSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new CreatomateTemplate();
            $template->template_id = "df3395b5-bf7e-411c-afd1-84079348f7b6";
            $template->title = "Mẫu Quảng Cáo";
            $template->template_thumbnail = "/assets/img/creatomate/template1.jpeg";
            $template->structure = '{
                "composition": {
                    "alertMessage": {
                        "value": "",
                        "type": "text"
                    },
                    "currentPrice": {
                        "value": "",
                        "type": "text"
                    },
                    "originalPrice": {
                        "value": "",
                        "type": "text"
                    },
                    "itemDescription": {
                        "value": "",
                        "type": "text"
                    },
                    "itemTitle": {
                        "value": "",
                        "type": "text"
                    },
                    "promoText": {
                        "value": "",
                        "type": "text"
                    },
                    "itemImage": {
                        "value": "",
                        "type": "image"
                    },
                    "logoImage": {
                        "value": "",
                        "type": "image"
                    },
                    "companyWebsite": {
                        "value": "",
                        "type": "text"
                    },
                    "actionMessage": {
                        "value": "",
                        "type": "text"
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

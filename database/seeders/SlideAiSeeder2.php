<?php

namespace Database\Seeders;

use App\Models\SlideAiTemplate;
use App\Models\SlideAiComponent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideAiSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=SlideAiSeeder2
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $template = new SlideAiTemplate();
            $template->template_name = 'Template Gamma';
            $template->save();

            $templateId = $template->id;

            $components = [
                [
                    'component_name' => 'Template1',
                    'type' => 'start',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template2',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template3',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template4',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template5',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template6',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template7',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template8',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template9',
                    'type' => 'body',
                    'template_id' => $templateId,
                ],
                [
                    'component_name' => 'Template10',
                    'type' => 'end',
                    'template_id' => $templateId,
                ],
            ];

            foreach ($components as $componentData) {
                $component = new SlideAiComponent();
                $component->component_name = $componentData['component_name'];
                $component->type = $componentData['type'];
                $component->template_id = $componentData['template_id'];
                $component->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreatomateDemoUrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CreatomateDemoUrlSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Mảng chứa các template_id và file_path
            $templates = [
                ['template_id' => 'bdc52cd3-043d-4416-a3df-772866268c02', 'file_name' => 'bdc52cd3-043d-4416-a3df-772866268c02.mp4'],
                ['template_id' => '12c40f00-5094-41e4-ade4-b1ddc7df2a02', 'file_name' => '12c40f00-5094-41e4-ade4-b1ddc7df2a02.mp4'],
                ['template_id' => 'b1e5a107-b865-4517-bc31-a43a820c3be1', 'file_name' => 'b1e5a107-b865-4517-bc31-a43a820c3be1.mp4'],
                ['template_id' => '8a55463b-4d35-4d6e-ad6a-f56636f37d42', 'file_name' => '8a55463b-4d35-4d6e-ad6a-f56636f37d42.mp4'],
                ['template_id' => '9c4dc50a-c891-49b3-8cd9-7d280a292fee', 'file_name' => '9c4dc50a-c891-49b3-8cd9-7d280a292fee.mp4'],
                ['template_id' => '45f83383-5507-4643-a5d2-40e86060c429', 'file_name' => '45f83383-5507-4643-a5d2-40e86060c429.mp4'],
                ['template_id' => 'fa278d01-443e-4522-86f6-7d5aab9987ad', 'file_name' => 'fa278d01-443e-4522-86f6-7d5aab9987ad.mp4'],
                ['template_id' => '22d6f0eb-48f1-4aef-af6d-c0ad81e377b0', 'file_name' => '22d6f0eb-48f1-4aef-af6d-c0ad81e377b0.mp4'],
                ['template_id' => 'ddabbf5f-6f81-4a4c-846e-05337b5a3258', 'file_name' => 'ddabbf5f-6f81-4a4c-846e-05337b5a3258.mp4'],
                ['template_id' => 'ef6087f8-53bd-462e-90b5-91d1669c3083', 'file_name' => 'ef6087f8-53bd-462e-90b5-91d1669c3083.mp4'],
                ['template_id' => '2204ec08-df2f-4625-b4f0-cfc3ec9ab93a', 'file_name' => '2204ec08-df2f-4625-b4f0-cfc3ec9ab93a.mp4'],
                ['template_id' => '05b908a5-51ac-4e06-a3ed-f30e9830ca89', 'file_name' => '05b908a5-51ac-4e06-a3ed-f30e9830ca89.mp4'],
                ['template_id' => '8edd268b-5fea-49ff-bb7f-e7c9343c0e64', 'file_name' => '8edd268b-5fea-49ff-bb7f-e7c9343c0e64.mp4'],
                ['template_id' => '4131722f-1416-46cd-bfdc-b113cc0c93cc', 'file_name' => '4131722f-1416-46cd-bfdc-b113cc0c93cc.mp4'],
                ['template_id' => 'c15c99ed-bedc-4fb9-802c-4a11251be359', 'file_name' => 'c15c99ed-bedc-4fb9-802c-4a11251be359.mp4'],
                ['template_id' => '79d44253-a167-402d-8fc9-889c994f1adc', 'file_name' => '79d44253-a167-402d-8fc9-889c994f1adc.mp4'],
                ['template_id' => '603dbc7b-ba97-4a4b-88ec-b4c15accad6f', 'file_name' => '603dbc7b-ba97-4a4b-88ec-b4c15accad6f.mp4'],
                ['template_id' => 'd29e06ac-3131-48ab-a75d-88453f78a3e8', 'file_name' => 'd29e06ac-3131-48ab-a75d-88453f78a3e8.mp4'],
                ['template_id' => 'ec0b82a5-3adb-448d-bf7a-ece855feb587', 'file_name' => 'ec0b82a5-3adb-448d-bf7a-ece855feb587.mp4'],
                
            ];

            foreach ($templates as $templateData) {
                // $localFilePath = storage_path('app/public/video/' . $templateData['file_name']);
                // if (!file_exists($localFilePath)) {
                //     dd('File không tồn tại: ' . $localFilePath);
                // }

                $s3FilePath = 'creatomate_video/' . $templateData['file_name'];
                // Storage::disk('s3')->put($s3FilePath, file_get_contents($localFilePath));

                $template = CreatomateTemplate::where('template_id', $templateData['template_id'])->first();

                if ($template) {
                    $template->demo_url = $s3FilePath;
                    $template->save();
                    // unlink($localFilePath);
                } else {
                    dd('Không tìm thấy template với ID: ' . $templateData['template_id']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}

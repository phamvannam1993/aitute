<?php

namespace App\Services\DifyBackup;

use App\Models\AIBusinessProject;
use App\Services\DifyBackup\Mode\ExpertMode;
use App\Services\DifyBackup\StepHandler;

class DifyBackupService
{

  public function handle(array $data, AIBusinessProject $aiBusinessProject)
  {
    $stepHandler = new StepHandler($data, $aiBusinessProject);

    return $stepHandler->handle();
  }


  /**
   * @throws JsonException
   */
  public function updateMetadata(AIBusinessProject $aiBusinessProject, array $query, string $answer = ''): void
  {
    if (!$aiBusinessProject->meta_data) {
      $aiBusinessProject = $this->generateMetadata($aiBusinessProject);
    }

    $metaData = json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR);
    $currentStep = $query['currentStep'] ?? '';

    if ($currentStep === 'buoc3') {
      $metaData['muc_tieu'] = $query['muc_tieu'] ?? '';
      $metaData['the_loai'] = $query['the_loai'] ?? '';
      $metaData['current_sub_step'] = $query['currentSubStep'] ?? '';
      $metaData['mode'] = $query['mode'] ?? '';

      if ($metaData['the_loai'] === 'Lời nhạc Quảng cáo sản phẩm') {
        $metaData['the_loai'] = 'Nhạc Doanh nghiệp';
      }

      if ($metaData['the_loai'] === 'Nhạc Doanh nghiệp') {
        if ($metaData['current_sub_step'] === 'buoc3_1') {
          $metaData['goal'] = $query['goal'] ?? '';
          $metaData['so_luong'] = $query['so_luong'] ?? '';
        } elseif ($metaData['current_sub_step']  === 'buoc3_2') {
          $metaData['y_tuong'] = $query['y_tuong'] ?? '';
        }
      }
    }

    if ($currentStep === 'buoc4' && !isset($metaData['muc_tieu'])) {
      $metaData['muc_tieu'] = $query['muc_tieu'] ?? '';
    }
    $aiBusinessProject->meta_data = json_encode($metaData, JSON_THROW_ON_ERROR);
    $aiBusinessProject->save();
  }

  /**
   * @throws JsonException
   */
  public function generateMetadata(AIBusinessProject $aiBusinessProject): AIBusinessProject
  {
    $metaData = $aiBusinessProject->meta_data ? json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR) : [];
    $tenDuAn = trim("$aiBusinessProject->title, $aiBusinessProject->description", ", ");
    $metaData['ten_du_an'] = $tenDuAn;

    $aiBusinessProject->meta_data = json_encode($metaData, JSON_THROW_ON_ERROR);
    $aiBusinessProject->save();

    return $aiBusinessProject;
  }
}

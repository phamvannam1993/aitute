<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AIGeneratedMedia;
use App\Services\AIVideoService;
use Illuminate\Support\Str;
use App\Models\User;
use App\Helper\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Helper\FFmpegHelper;
use Exception;
use Auth; 
use function PHPUnit\Framework\isEmpty;

class KlingService
{
  protected $apiKey;
  protected $baseUrl;
  protected $videoService;
  protected $textToSpeechService;
  protected $ffmpegHelper;

  public function __construct()
  {
    $this->baseUrl = config('services.kling.endpoint');
    $this->apiKey = config('services.kling.api_key');
    $this->videoService = new AIVideoService();
    $this->textToSpeechService = new TextToSpeechService();
    $this->ffmpegHelper = new FFmpegHelper();
  }

  private function cleanupFiles(array $paths)
  {
    foreach ($paths as $path) {
      $fullPath = storage_path('app/public/' . $path);
      if (file_exists($fullPath)) {
        unlink($fullPath);
      }
    }
  }

  public function storeMedia($data)
  {
   
    try {
      $message = AIGeneratedMedia::create($data);
     
      if (!$message) {
        Log::error('Lưu trữ file thất bại.');
        return null;
      }
      return $message;
    } catch (Exception $e) {
      Log::error(message: 'Lỗi khi lưu trữ file: ' . $e->getMessage());
      return null;
    }
  }

  public function updateMedia($task_id, $data)
  {
    try {
      AIGeneratedMedia::query()->where('task_id', "=", $task_id)->update($data);
    } catch (Exception $e) {
      Log::error(message: 'Lỗi khi cập nhật file: ' . $e->getMessage());
      return null;
    }
  }

  public function createTask($data, $user_id) {
    try {
      $isTaskId = env("IS_TASK_ID");
      $taskId = "";
      if(!$isTaskId) {
        $responseData = $this->callApiKling($data);
        $taskId = $responseData['data']['task_id'] ?? "";
      } else {
        $taskId = "3c1095f0-399a-4433-b439-e69f4b48c6f6";
      }
      $this->storeMedia([
        'model' => 'Kling',
        'description' => $data['video_description'] . "|" . ($data['audio_description'] ?? '') . "|" . ($data['language'] ?? 'vi-VN'),
        'duration' => $data['duration'],
        'task_id' => $taskId,
        'user_id' => $user_id,
        'type' => 'video',
        's3_url' => ''
      ]);
      $audio_path = "";
      $dataFiles = [];
      if($data["audio_url"]) {
          $audioContent = file_get_contents($data["audio_url"]);
          $audioPath = 'videos/' . Str::uuid() . '.mp3';
          $audio_path = 'videos/' . Str::uuid() . '.mp3';
          file_put_contents(storage_path('app/public/' . $audioPath), $audioContent);
          FFmpegHelper::cutAudioV2(
              storage_path('app/public/' . $audioPath),
              storage_path('app/public/' . $audio_path),
              0,
              $data['duration']
          );
          $dataFiles[] = $audioPath;
      }
      $this->cleanupFiles($dataFiles);
     
      if (isset($data["audio_path"])) {
        FFmpegHelper::cutAudioV2(
            storage_path('app/public/' . $data["audio_path"]),
            storage_path('app/public/' . $audio_path),
            0,
            $data['duration']
        );
      }
  
      $media = AIGeneratedMedia::where('task_id', $taskId)->first();
      if(!$media) {
        return response()->json(['success' => false, 'message' => 'Không tồn tại task'], 400);
      }
      return response()->json(['success' => true, 'task_id' => $taskId, 'audio_path_cut' => $audio_path]);
      } catch(\Exception $ex) {
        return response()->json(['success' => false, 'message' => 'Task created error'], 500);
      }
  }

  public function getVideoUrl($taskId, $audio_path) {
    $media = AIGeneratedMedia::where('task_id', $taskId)->first();
    $user = User::find($media->user_id);
    $statusResponse = Http::withHeaders([
      'x-api-key' => $this->apiKey,
      'Content-Type' => 'application/json',
    ])->get("{$this->baseUrl}/{$media->task_id}");
    $statusData = $statusResponse->json();
    $videoUrl = $statusData['data']['output']['video_url'] ?? null;
    if ($videoUrl) {
      $videoContent = file_get_contents($videoUrl);
      $tempFile = tempnam(sys_get_temp_dir(), 'video_');
      file_put_contents($tempFile, $videoContent);
      $uploadedFile = new \Illuminate\Http\UploadedFile(
        $tempFile,
        'video_template.mp4',
        mime_content_type($tempFile),
        null,
        true
      );

      $video_path = $uploadedFile->store('videos', 'public');
      $audio_path_cut = 'audio/' . Str::uuid() . '.mp3';
      $outputPath = 'videos/' . Str::uuid() . '.mp4';
      $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
      $dataFiles = [];
      if ($audio_path) {
        $audio_path_cut = 'audio/' . Str::uuid() . '.mp3';
        $dataFiles[] = $audio_path_cut;
        $dataFiles[] = $audio_path;
        $dataFiles[] = $thumbnailPath;
        $dataFiles[] = $video_path;
        $this->ffmpegHelper->cutAudio(
          storage_path('app/public/' . $audio_path),
          storage_path('app/public/' . $audio_path_cut),
          0,
          $media->duration
        );
        $this->ffmpegHelper->mergeVideoAndAudioWithFFmpeg(
          storage_path('app/public/' . $video_path),
          storage_path('app/public/' . $audio_path_cut),
          storage_path('app/public/' . $outputPath),
          storage_path('app/public/' . $thumbnailPath)
        );
        if ($outputPath && Storage::disk('public')->exists($outputPath)) {
          Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
        }
        if (Storage::disk('public')->exists($video_path)) {
          Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
        }
        if (Storage::disk('public')->exists($thumbnailPath)) {
          Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
        }

      } else {
        $outputPath = $video_path;
        // create thumbnail
        $this->ffmpegHelper->createThumbnail(
          storage_path('app/public/' . $outputPath),
          storage_path('app/public/' . $thumbnailPath)
        );
        Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
        Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
      }
      Auth::user()->chargeCredit('gen3-alphaturbo', '', $media->duration, null, null, 3, 3);
      $this->updateMedia($media->task_id, [
        's3_url' => $outputPath,
        'thumbnail' => $thumbnailPath
      ]);
      $this->cleanupFiles($dataFiles);
      $s3Url = Helpers::preSignedS3Url($outputPath);
      return response()->json(['s3_url' => $s3Url,'id' => $media->id, 'video_path' => $outputPath, 'success' => true, 'thumbnail' => $thumbnailPath, 'message' => 'Task created successfully', 'id' => $media->id],);
    }
    return response()->json(['success' => false, 'message' => 'Không tồn tại Video']);
  }

  public function callApiKling($data) {
      $response = Http::withHeaders([
        'x-api-key' => $this->apiKey,
        'Content-Type' => 'application/json',
      ])->post($this->baseUrl, [
        'model' => 'kling',
        'task_type' => 'video_generation',
        'input' => [
          'prompt' => $data['video_description'],
          'negative_prompt' => '',
          'cfg_scale' => 0.7,
          'duration' => (int)$data['duration'],
          'aspect_ratio' => $data['resolution'],
          'camera_control' => [
            'type' => 'simple',
            'config' => [
              'horizontal' => 0,
              'vertical' => 0,
              'pan' => -10,
              'tilt' => 0,
              'roll' => 0,
              'zoom' => 0
            ]
          ],
          'mode' => 'std'
        ],
        'config' => [
          'service_mode' => 'public',
          'webhook_config' => [
            'endpoint' => $data['webhook_endpoint'] ?? '',
            'secret' => $data['webhook_secret'] ?? '',
          ],
        ]
      ]);
      $responseData = $response->json();
      return  $responseData;
  }

  public function checkTaskStatus($taskId)
  {
    // Kiểm tra trạng thái task từ session
    $taskStatus = session()->get("video_task_{$taskId}");

    if (!$taskStatus) {
      return response()->json(['error' => 'Task not found'], 404);
    }

    return response()->json([
      'task_id' => $taskId,
      'status' => $taskStatus['status'],
      'video_url' => $taskStatus['video_url'],
      'description' => $taskStatus['description']
    ]);
  }

  public function updateTaskStatus($taskId, $videoUrl)
  {
    // Lấy thông tin task từ session
    $taskStatus = session()->get("video_task_{$taskId}");

    if (!$taskStatus) {
      return response()->json(['error' => 'Task not found'], 404);
    }

    // Cập nhật trạng thái và video_url
    $taskStatus['status'] = 'completed';
    $taskStatus['video_url'] = $videoUrl;

    // Lưu lại vào session
    session()->put("video_task_{$taskId}", $taskStatus);

    return response()->json([
      'message' => 'Task updated successfully',
      'task_id' => $taskId,
      'video_url' => $videoUrl
    ]);
  }

  public function clearTaskFromSession($taskId)
  {
    // Xóa task từ session
    session()->forget("video_task_{$taskId}");

    return response()->json(['message' => 'Task cleared from session']);
  }

  public function getTask($taskId)
  {
    try {
      Log::info('Kling API bắt đầu download video: ' . $taskId);

      $media = AIGeneratedMedia::query()->where('task_id', $taskId)->first();

      $response = Http::withHeaders([
        'x-api-key' => $this->apiKey,
      ])->get($this->baseUrl . '/' . $taskId);

      if ($response->json()['code'] === 200) {
        $videoUrl = $response->json()['data']['output']['video_url'] ?? null;

        if ($videoUrl) {
          $videoContent = @file_get_contents($videoUrl);
          if ($videoContent === false) {
            Log::error('Không thể tải video từ URL: ' . $videoUrl);
            throw new Exception('Failed to download video.');
          }

          $tempFile = tempnam(sys_get_temp_dir(), 'video_');
          if ($tempFile && file_put_contents($tempFile, $videoContent) !== false) {
            $uploadedFile = new \Illuminate\Http\UploadedFile(
              $tempFile,
              'video_template.mp4',
              mime_content_type($tempFile),
              null,
              true
            );
            $video_path = $uploadedFile->store('videos', 'public');
          } else {
            Log::error('Không thể tạo tệp tạm hoặc ghi nội dung video.');
            throw new Exception('Failed to create temp file or write video content.');
          }

          if (!file_exists(storage_path('app/public/kling/' . $taskId . '.mp3'))) {
            $user = User::find($media->user_id);
            $content = $this->videoService->generateContentAudio(
              explode('|', $media->description)[0] . " Thời lượng là " . $media->duration . " giây",
              $media->duration,
              null,
              $user
            );

            if (!$content || empty($content['ssml'])) {
              Log::error('Không thể tạo nội dung audio.');
              throw new Exception('Failed to generate audio content.');
            }

            $audio_path = $this->textToSpeechService->ssml2Audio($content['ssml']);
          } else {
            $audio_path = 'kling/' . $taskId . '.mp3';
          }

          $audio_path_cut = 'audio/' . Str::uuid() . '.mp3';
          $this->ffmpegHelper->cutAudio(
            storage_path('app/public/' . $audio_path),
            storage_path('app/public/' . $audio_path_cut),
            0,
            $media->duration
          );

          $outputPath = 'videos/' . Str::uuid() . '.mp4';
          $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
          Log::info('Kling API bắt đầu merge video và audio: ' . $outputPath);
          $this->ffmpegHelper->mergeVideoAndAudioWithFFmpeg(
            storage_path('app/public/' . $video_path),
            storage_path('app/public/' . $audio_path_cut),
            storage_path('app/public/' . $outputPath),
            storage_path('app/public/' . $thumbnailPath)
          );

          if (Storage::disk('public')->exists($outputPath)) {
            Log::info('Kling API bắt đầu upload video và audio lên S3: ' . $outputPath);
            Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
          } else {
            Log::error('File output không tồn tại: ' . $outputPath);
            throw new Exception('Output file does not exist.');
          }

          if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
          }

          Log::info('Kling API bắt đầu xóa file tạm: ' . $outputPath);
          $this->cleanupFiles([
            $video_path,
            $audio_path,
            $outputPath,
            $thumbnailPath
          ]);

          Log::info('Kling API bắt đầu update s3_url: ' . $outputPath);

          $this->updateMedia($taskId, [
            's3_url' => $outputPath,
            'thumbnail' => $thumbnailPath
          ]);
        } else {
          Log::error('Không có video URL trong phản hồi.');
          throw new Exception('No video URL found in response.');
        }
      } else {
        Log::error('Kling API get task error: ' . $response->body());
        throw new Exception('Failed to get task: ' . $response->status());
      }
    } catch (Exception $e) {
      Log::error('Kling API get task exception: ' . $e->getMessage());
      throw $e;
    }
  }
}
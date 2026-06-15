<?php

namespace App\Services;

use App\Repositories\AiVideoRepository;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Helper\FFmpegHelper;
use App\Services\ChatGPTService;
use App\Services\TextToSpeechService;
use App\Helper\Helpers;
use App\Models\AIGeneratedMedia;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class AIVideoServiceV2
{
    private $apiToken;
    private $apiKeySegmind;
    private $segmindEnpoint;
    private $chatGPTService;
    private $textToSpeechService;
    private $aiVideoRepository;
    public function __construct()
    {
        $this->apiToken = env('REPLICATE_API_TOKEN');
        $this->apiKeySegmind = env('SEGMIND_API_KEY');
        $this->chatGPTService = new ChatGPTService();
        $this->textToSpeechService = new TextToSpeechService();
        $this->aiVideoRepository = new AiVideoRepository();
    }

    public function generateVideo($data, $user_id) {
        $dataSave = [
            'user_id' => $user_id,
            'description' => $data['video_description'],
            'model' => $data['model'],
            'duration' => $data['duration'],
            'task_id' => "",
            's3_url' => "",
            'thumbnail' => "",
            'type' => 'video',
        ];
        $result = $this->storeMedia($dataSave);
        $urls = [];
        $videoUrl = "";
        $thumbnail = "";
        if($result) {
            $resData = "";
            if(isset($data["promptImage"])) {
                $resData = $this->createVideRunway($data);
            } else {
                $resData = $this->createVideoPrediction($data);
            }
            if($resData['success']) {
                $videoUrl = $resData['video_url'];
                $thumbnail = $resData['thumbnail'];
                $urls[] = $videoUrl;
                $urls[] = $thumbnail;
            } else {
                return $resData;
            }
            AIGeneratedMedia::where("id", $result->id)->update(['s3_url' => $videoUrl, 'thumbnail' => $thumbnail]);
            if(!empty($data['audio_url'])) {
                $videoUrl = $this->mergeVideoAudio($videoUrl, $data['audio_url']);
                $urls[] = $videoUrl;
                AIGeneratedMedia::where("id", $result->id)->update(['s3_url' => $videoUrl]);
            } 
            $this->cleanupFiles($urls);
            $aiMedia = AIGeneratedMedia::where("id", $result->id)->first();
            return [
                'success' => true,
                's3_url' => Helpers::preSignedS3Url($videoUrl),
                'generate_file' => $aiMedia,
                'id' => isset($aiMedia->id) ? $aiMedia->id : 0
            ];
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
            Log::error('Lỗi khi lưu trữ file: ' . $e->getMessage());
            return null;
        }
    }

    function createVideoPrediction($data)
    {
        try {
            $promt = $data['video_description'];
            $duration = $data['duration'];
            $ratio = $data['ratio'];
            $responseData = $this->callApi($promt, $duration, $ratio);
            $responseData = json_decode($responseData, true);
            $get_url = $responseData['urls']['get'];
            $apiToken = env('REPLICATE_API_KLING');
            do {
                try {
                    $responseImg = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$apiToken}",
                    ])->get($get_url);
                    $responseImgData = $responseImg->json();
                    $output = isset($responseImgData['output']) ? $responseImgData['output'] : null;
                } catch (Exception $e) {
                    return ["success" => false, "message" => "Nội dung video không hợp lệ. Vui lòng nhập lại.", "code" => 500];
                }
            } while (empty($output) && $responseImgData['status'] !== 'failed');

            if (is_null($output)) {
                return response()->json(["success" => false, "message" => "Nội dung video không hợp lệ. Vui lòng nhập lại", "code" => 500]);
            }
            $videoContent = null;
            $videoContent = file_get_contents($output);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
            $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
            FFmpegHelper::createThumbnail(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $thumbnailPath)
            );
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
            }
            Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
            Auth::user()->chargeCredit('gen3-alphaturbo', '', $duration, null, null, 37, 41);
            return [
                'video_url' => $video_path,
                'thumbnail' => $thumbnailPath,
                'success' => true,
                'output' => $output,
                'code' => 200
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Nội dung video không hợp lệ", "code" => 500];
        }
    }

    function mergeVideoAudio($video_path, $audioUrlS3) {
        $audioUrl = 'videos/' . Str::uuid() . '.mp3';
        $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
        $urls = [];
        if(!empty($audioUrlS3)) {
            file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
            $urls[] = $video_path;
            $urls[] = $audioUrl;
            $audioPath2 = 'audio/' . Str::uuid() . '.mp3';
            $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
            FFmpegHelper::cutAudioV2(
                storage_path('app/public/' . $audioUrl),
                storage_path('app/public/' . $audioPath2),
                0,
                (int)$videoDuration
            );
            $urls[] = $audioPath2;
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null
            );
            Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        } 
        $this->cleanupFiles($urls);
        return $videoPath2;
    }

    private function cleanupFiles(array $paths)
    {
        try {
            foreach ($paths as $path) {
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        } catch (Exception $e) {
            Log::error("cleanupFiles: " . $e->getMessage());
        }
    }

    function callApi($promt, $duration, $ratio) {
        $apiToken = env('REPLICATE_API_KLING');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.replicate.com/v1/models/kwaivgi/kling-v1.6-standard/predictions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "input": {
              "prompt": "'.$promt.'",
              "duration": '.$duration.',
              "cfg_scale": 0.5,
              "aspect_ratio": "'.$ratio.'",
              "negative_prompt": ""
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$apiToken,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        return $response;
    }

    public function createVideRunway($data)
    {
        try {
            if($data['ratio']  == '9:16') {
                $ratio = "768:1280";
            } else {
                $ratio = "1280:768";
            }
            $promptText = $data['video_description'];
            if(empty($promptText)) {
                $promptText = "Mô tả video";
            }
            $promptImage = $this->resizeImage($data['promptImage']);
            $requestData = [
                'promptText' => $promptText,
                "promptImage" => $promptImage,
                "model" => "gen3a_turbo",
                "watermark" => false,
                'seed' => rand(1, 99999),
                'duration' => (int)$data['duration'],
                'ratio' => $ratio
            ];
            $isTaskId = env("IS_TASK_ID");
            $taskId = "";
            if(!$isTaskId) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('runway.api_key'),
                    'Content-Type' => 'application/json',
                    'X-Runway-Version' => '2024-11-06'
                ])->timeout(1200)->post('https://api.dev.runwayml.com/v1/image_to_video', $requestData);
                if (!$response->successful()) {
                    return ["success" => false, 'details' => $response->json(), "code" => 500];
                }
                $taskId = $response->json()['id'];
            } else {
                $taskIds = ["fb49fae2-98da-42fb-9ef3-4f804d237291", "806b3777-a365-4599-97ca-f39722e599d5", "afe0cda2-68e3-4f81-9ab3-2c6f07c7e04e",
                "806b3777-a365-4599-97ca-f39722e599d5", "f59e128d-5e23-4ad2-98e6-06ceaf7ed2b0", "edfb411d-3ee2-4154-8214-271b92fe1d7e", "4147dbc0-ed6f-45fc-82e3-28af217c6310"];
                $randId = rand(0,5);
                $taskId = $taskIds[$randId];
            }

            // $taskId = "f59e128d-5e23-4ad2-98e6-06ceaf7ed2b0";
            $maxAttempts = 60;
            $pollInterval = 5;
            $attempt = 0;

            while ($attempt < $maxAttempts) {
                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('runway.api_key'),
                    'Content-Type' => 'application/json',
                    'X-Runway-Version' => '2024-09-13'
                ])->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");

                if (!$statusResponse->successful()) {
                    return ["success" => false, 'code' => "500"];
                }
                $status = $statusResponse->json()['status'];
                if ($status === 'SUCCEEDED') {
                    $output = $statusResponse->json()['output'][0];
                    $videoContent = file_get_contents($output);
                    $video_path = 'videos/' . Str::uuid() . '.mp4';
                    file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
                    $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
                    FFmpegHelper::createThumbnail(
                        storage_path('app/public/' . $video_path),
                        storage_path('app/public/' . $thumbnailPath)
                    );
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                    }
                    Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
                    Auth::user()->chargeCredit('gen3-alphaturbo', '', $data['duration'], null, null, 37, 41);
                    return [
                        'video_url' => $video_path,
                        'thumbnail' => $thumbnailPath,
                        'success' => true,
                        'code' => 200
                    ];
                }

                if ($status === 'FAILED') {
                    return ["success" => false, 'code' => "500"];
                }

                $attempt++;
                if ($attempt < $maxAttempts) {
                    sleep($pollInterval);
                }
            }
        } catch (\Exception $e) {
            return ["success" => false, 'message' => $e->getMessage(), 'code' => "500"];
        }
    }

    public function resizeImage($s3_url, $isRatio = false) {
        $urls = [];
        $imagePath = 'videos/' . Str::uuid() . '.jpg';
        $urls[] = $imagePath;
        file_put_contents( storage_path('app/public/'.$imagePath), file_get_contents($s3_url));
        $imageInfo = FFmpegHelper::getWidthHeightVideo(storage_path('app/public/'.$imagePath));
        $imageInfos = (explode(",",$imageInfo));
        $with = $imageInfos[0];
        $height = $imageInfos[1];
        $ratio = $with/ $height;
        if($isRatio) {
            $this->cleanupFiles($urls);
            return $ratio;
        }
        $imagePathConvert = 'videos/' . Str::uuid() . '.jpg';
        if($ratio < 0.5 || $ratio > 2) {
            if($ratio < 0.5) {
                FFmpegHelper::changeVideoResolution(storage_path('app/public/'.$imagePath), storage_path('app/public/'.$imagePathConvert), 1080, 1920);
            } else if($ratio > 2) {
                FFmpegHelper::changeVideoResolution(storage_path('app/public/'.$imagePath), storage_path('app/public/'.$imagePathConvert), 1920, 1080);
            }
            Storage::disk('s3')->put($imagePathConvert, Storage::disk('public')->get($imagePathConvert));
            $urls[] = $imagePathConvert;
            $s3_url = Helpers::preSignedS3Url($imagePathConvert);
        }
        $this->cleanupFiles($urls);
        return $s3_url;
    }
}

<?php

namespace App\Services;

use App\Repositories\ShortVideoRepository;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Helper\FFmpegHelper;
use App\Helper\Helpers;
use App\Models\ShortVideo;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShortVideoService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
      $this->baseUrl = config('services.kling.endpoint');
      $this->apiKey = config('services.kling.api_key');
    }

    public function generateVideoAudioRunway($data, $user_id)
    {
        try {
            $user = User::find($user_id);
            try {
                $ratio = "768:1280";
                if($data['resolution'] == "16:9") {
                    $ratio = "1280:768";
                }
                $promptText = $data['image_description'] != "undefined" && $data['image_description'] != "null" ? $data['image_description'] : "";
                if(!empty($data['position_camera'])) {
                    $positionCamera = ShortVideo::positionCamera();
                    $des = ShortVideo::positionCameraByKey($data['position_camera'], $positionCamera);
                    $promptText = $promptText.".".$des;
                }
                if(empty($promptText)) {
                    $promptText = "Mô tả video";
                }
                $requestData = [
                    'promptText' => $promptText,
                    'promptImage' => $data["promptImage"],
                    "model" => "gen3a_turbo",
                    "watermark" => false,
                    'seed' => rand(1, 99999),
                    'duration' => (int)$data['duration'],
                    'ratio' => $ratio
                ];
                $isTaskId = env("IS_TASK_ID");
                $isExist = false;
                $taskId = "";
                if(isset($data["short_video_id"])) {
                    $shortVideo = ShortVideo::where("id", $data["short_video_id"])->where("user_id", $user->id)->first();
                    if($shortVideo->image_url == $data["promptImage"]) {
                        $taskId  = $shortVideo->task_id;
                        $isExist = true;
                    } else {
                        ShortVideo::where("id", $data["short_video_id"])->update(["model" =>  $data['model']]);
                    }
                }
                if(!$taskId ) {
                    if(!$isTaskId) {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . config('runway.api_key'),
                            'Content-Type' => 'application/json',
                            'X-Runway-Version' => '2024-11-06'
                        ])->timeout(1200)->post('https://api.dev.runwayml.com/v1/image_to_video', $requestData);
                        if (!$response->successful()) {
                            return ["success" => false, 'isExist' => $isExist, 'details' => $response->json(), "code" => 500];
                        }
                        $taskId = $response->json()['id'];
                    } else {
                        $taskIds = ["fb49fae2-98da-42fb-9ef3-4f804d237291", "806b3777-a365-4599-97ca-f39722e599d5", "afe0cda2-68e3-4f81-9ab3-2c6f07c7e04e",
                        "806b3777-a365-4599-97ca-f39722e599d5", "f59e128d-5e23-4ad2-98e6-06ceaf7ed2b0", "edfb411d-3ee2-4154-8214-271b92fe1d7e"];
                        $randId = rand(0,5);
                        $taskId = $taskIds[$randId];
                    }
                }

                if(isset($data["short_video_id"])) {
                    $shortVideo = ShortVideo::where("id", $data["short_video_id"])->where("user_id", $user->id)->first();
                    $dataUpdate = [
                        'description' => $data['video_description'],
                        'audio_description' => $data['audio_description'],
                        'position_camera' => $data['position_camera'] ? $data['position_camera'] : 0,
                        'image_description' => $data['image_description'],
                        'image_url' => $data["promptImage"],
                        'voice_over' => $data['voice_type'] ? $data['voice_type'] : "",
                        'is_upload_audio' => false,
                        'task_id' => $taskId,
                    ];
                    ShortVideo::where("id", $data["short_video_id"])->update($dataUpdate);
                    return ["success" => true, 'id' => $data["short_video_id"], 'isExist' => $isExist, 'code' => 200];
                }
                $record = $this->saveShortVideo([
                    'user_id' => $user_id,
                    'description' => $data['video_description'],
                    'audio_description' => $data['audio_description'],
                    'position_camera' => $data['position_camera'] ? $data['position_camera'] : 0,
                    'image_description' => $data['image_description'],
                    'image_url' => $data["promptImage"],
                    'model' => $data['model'],
                    'scene_number' => $data['scene_number'] ? $data['scene_number'] : 0,
                    'voice_over' => $data['voice_type'] ? $data['voice_type'] : "",
                    'task_id' => $taskId,
                    's3_url' => "",
                    'thumbnail' => "",
                ]);
                return ["success" => true, 'id' => $record->id, 'isExist' => $isExist, 'code' => 200];
            } catch (\Exception $e) {
                return ["success" => false, 'isExist' => true, 'message' => "Ảnh của bạn không hợp lệ. Vui lòng chọn ảnh khác.", "model" => "Runway", "code" => 500];
            }
        } catch (Exception $e) {
            return ["success" => false, 'isExist' => true,  'message' =>  "Ảnh của bạn không hợp lệ. Vui lòng chọn ảnh khác.", "model" => "Runway", "code" => 500];
        }
    }

    public function generateVideoKling($data, $user) {
        try {
            if(!isset($data["short_video_id"])) {
                return ["success" => false, 'message' => "Không tồn tại dữ liệu", "code" => 500];
            }
            $shortVideo = ShortVideo::where("id", $data["short_video_id"])->where("user_id", $user->id)->first();
            $promptImage = $shortVideo->image_url;
            $duration = $data["duration"];
            $resolution = $data["resolution"];
            $responseData = $this->callApiKling($promptImage, $duration, $resolution);
            $taskId = $responseData['data']['task_id'] ?? "";
            // $taskId = "8e16a9a3-1ec1-46c4-a98b-748441c853c0";
            if(isset($data["short_video_id"])) {
                $dataUpdate = [
                    'model' => "kling",
                    'task_id' => $taskId,
                ];
                ShortVideo::where("id", $data["short_video_id"])->update($dataUpdate);
                return ["success" => true, 'id' => $data["short_video_id"], 'code' => 200];
            }
        } catch (Exception $e) {
            return response()->json(["success" => false, "model" => "kling", 'message' => "Ảnh của bạn không hợp lệ. Vui lòng chọn ảnh khác", "code" => 500]);
        }
    }

    public function callApiKling($promptImage, $duration, $resolution) {
        $response = Http::withHeaders([
          'x-api-key' => $this->apiKey,
          'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
          'model' => 'kling',
          'task_type' => 'video_generation',
          'input' => [
            'prompt' => "Mô tả video",
            'image_url' => $promptImage,
            'negative_prompt' => '',
            'cfg_scale' => 0.7,
            'duration' => (int)$duration,
            'aspect_ratio' => $resolution,
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
        ]);
        $responseData = $response->json();
        return  $responseData;
    }

    public function getVideoKling($record) {
        try {
            $statusResponse = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/{$record->task_id}");
            $statusData = $statusResponse->json();
            $status = $statusData['data']['status'];
            if($status == "completed") {
                $output = $statusData['data']['output']['video_url'] ?? null;

                $videoContent = file_get_contents($output);
                $videoPath = 'videos/' . Str::uuid() . '.mp4';
                $videoPath = 'videos/' . Str::uuid() . '.mp4';
                file_put_contents(storage_path('app/public/' . $videoPath), $videoContent);
                Storage::disk('s3')->put($videoPath, Storage::disk('public')->get($videoPath));
                $record->update(["s3_url" => $videoPath]);
                $this->cleanupFiles([$videoPath]);
                return response()->json([
                    's3_url' => Helpers::preSignedS3Url($videoPath),
                    'id' => $record->id
                ]);
            } else if($status == "failed") {
                return response()->json(['success' => false, 'message' => 'Ảnh của bạn không hợp lệ. Vui lòng thử ảnh khác', "model" => "kling", "code" => 500]);
            } else {
                return response()->json(['success' => false, 'message' => 'Đang chờ tạo video', "model" => "kling", "code" => 200]);
            }
        } catch(Exception $ex) {
            return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "model" => "kling", "code" => 500]);
        }
    }

    public function saveShortVideo($data) {
        try {
            $message = ShortVideo::create($data);
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

    public function getDetail($user, $id) {
        return ShortVideo::where("id", $id)->where("user_id", $user->id)->first();
    }

    function getVideoURLRun($short_video_id) {
        $record =  ShortVideo::where('id', $short_video_id)->orderBy("id", "DESC")->first();
        if($record->model == "kling") {
            return $this->getVideoKling($record);
        }
        if(empty($record )) {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"], 500);
        }
        $taskId =  $record->task_id;
        $statusResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('runway.api_key'),
            'Content-Type' => 'application/json',
            'X-Runway-Version' => '2024-11-06'
        ])->timeout(1200)->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");
        if(isset($statusResponse->json()['status'])) {
            $status = $statusResponse->json()['status'];
            if($status == 'SUCCEEDED') {
                $output = $statusResponse->json()['output'][0];
                $videoContent = file_get_contents($output);
                if (!file_exists(storage_path('app/public/videos'))) {
                    mkdir(storage_path('app/public/videos'), 0777, true);
                }
                $videoPath = 'videos/' . Str::uuid() . '.mp4';
                file_put_contents(storage_path('app/public/' . $videoPath), $videoContent);
                Storage::disk('s3')->put($videoPath, Storage::disk('public')->get($videoPath));
                $record->update(["s3_url" => $videoPath]);
                $this->cleanupFiles([$videoPath]);
                return response()->json([
                    's3_url' => Helpers::preSignedS3Url($videoPath),
                    'id' => $record->id
                ]);
            } else if($status == 'FAILED') {
                return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "model" => "Runway", "code" => 500]);
            } else {
                return response()->json(["success" => true, "message" => "Đang chờ tạo video", "model" => "Runway", "code" => 200]);
            }
        } else {
            return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "model" => "Runway", "code" => 500]);
        }
    }

    private function cleanupFiles(array $paths)
    {
        try {
            foreach ($paths as $path) {
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        } catch (Exception $e) {
            Log::error("cleanupFiles: " . $e->getMessage());
        }
    }

    public function mergeAudioVideo($shortVideoId, $audioUrl) {
        $shortVideo = ShortVideo::find($shortVideoId);
        $urls = [];
        $video_url = $shortVideo->s3_url;
        if($shortVideo->transcription_url) {
            $video_url = $shortVideo->transcription_url;
        }
        $video_url = Helpers::preSignedS3Url($video_url);

        $video_path = 'videos/' . Str::uuid() . '.mp4';
        $urls[] = $video_path;
        file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));

        $audioUrlS3 = Helpers::preSignedS3Url($audioUrl);

        file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
        $urls[] = $audioUrl;
        $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
        $audioPath2 = 'audio/' . Str::uuid() . '.mp3';
        $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
        FFmpegHelper::cutAudioV2(
            storage_path('app/public/' . $audioUrl),
            storage_path('app/public/' . $audioPath2),
            0,
            (int)$videoDuration
        );
        $urls[] = $audioPath2;

        $nameAudioConvert = 'audio_convert_'.Str::uuid().'.mp3';
        $audioConvertPath = 'videos/'. $nameAudioConvert;
        FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$video_path) , storage_path('app/public/'.$audioConvertPath));
        if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null
            );
        } else {
            FFmpegHelper::mergerVideoAudioV2(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null,
                "0.2"
            );
        }
        $url[] = $audioConvertPath;
        $urls[] = $videoPath2;
        Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        $this->cleanupFiles($urls);
        ShortVideo::where("id", $shortVideoId)->update(["s3_url_video_audio" => $videoPath2, "is_upload_audio" => true]);
    }

    public function mergeAudioVideoV2($shortVideoId, $audioUrl) {
        try {
            $shortVideo = ShortVideo::find($shortVideoId);
            $urls = [];
            $video_url = $shortVideo->s3_url;
            $video_url = Helpers::preSignedS3Url($video_url);

            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $urls[] = $video_path;
            file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));

            $audioUrlLocal = 'videos/' . Str::uuid() . '.mp3';;

            file_put_contents(storage_path('app/public/' .$audioUrlLocal), file_get_contents($audioUrl));
            $urls[] = $audioUrlLocal;
            $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
            $audioPath2 = 'audio/' . Str::uuid() . '.mp3';
            $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
            FFmpegHelper::cutAudioV2(
                storage_path('app/public/' . $audioUrlLocal),
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
            $urls[] = $videoPath2;
            Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
            $this->cleanupFiles($urls);
            ShortVideo::where("id", $shortVideoId)->update(["s3_url" => $videoPath2, "is_upload_audio" => true]);
            $record = ShortVideo::where("id", $shortVideoId)->first();
            $dataOld = [
                "descriptions" => [
                    [
                        "sub_title" => $record["description"],
                        "description" => $record["audio_description"],
                    ]
                ],
                "shortVideoId" =>  $record["id"],
                "position_camera" =>  $record["position_camera"],
                "image_description" => $record["image_description"] != "undefined" &&  $record["image_description"] != "null" ? $record["image_description"] : "",
                "isVoice" => $record["voice_over"] ? false : true,
                "s3_url" => $record["s3_url"],
                "audioUrl" => $record["audio_url"],
                "videoRef" => $record["s3_url"],
                "video_id" => $record["video_id"],
                "voice_type" => $record["voice_over"],
                "s3_image_url" => $record["image_url"],
                "imageUrl" => $record["image_url"],
            ];
            return ["success" => true, 'data' => $record, "s3_url" => $record->s3_url, 'dataOld' => $dataOld];
        } catch(Exception $ex) {
            return ["success" => false, "message" => "Có lỗi xảy ra trong quá trình merge audio"];
        }
    }

    public function getByIds($ids = []) {
        return ShortVideo::whereIn("id", $ids)->where("video_id", ">", 0)->first();
    }

    public function updByIds($ids = [], $data = []) {
        foreach($ids as $id) {
            ShortVideo::where("id", $id)->update($data);
        }
        return true;
    }

    public function getListByVideoId($videoId) {
        return ShortVideo::where("video_id", $videoId)->orderBy("scene_number", "ASC")->get();
    }

    public function generateVideoWithTranscriptionWithSegmind($user, $data)
    {
        $s3Url = "";
        $shortVideoId = $data['short_video_id'];
        $shortVideo = ShortVideo::find($shortVideoId);
        try {
            $s3Url = $shortVideo->s3_url;
            // Get pre-signed URL for the video
            $video_url = Helpers::preSignedS3Url($s3Url);

            $payload = [
                'MaxChars' => 10,
                'bg_blur' => false,  // Changed from true to false
                'bg_color' => "None",  // Changed from null to "None"
                'color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
                'font' => isset($data['font']) ? $data['font'] : "Arial/Arial_Bold.ttf",  // Changed font
                'fontsize' => 5,  // Changed from 7.0 to 7
                'highlight_color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
                'kerning' => -2,  // Changed from -2.0 to -2
                'opacity' => 0,  // Changed from 1.0 to 0
                'right_to_left' => false,  // Changed from true to false
                'stroke_color' => "black",
                'stroke_width' => $data['stroke_width'] ?? 2,
                'subs_position' =>  isset($data['subs_position']) ? $data['subs_position'] : "bottom",
                'input_video' => $video_url
            ];

            // Log::info('Segmind payload:', ['payload' => $payload]);
            // $response = Http::withHeaders([
            //         'x-api-key' => env("SEGMIND_API_KEY"),
            //         'Content-Type' => 'application/json',
            //     ])
            //     ->post('https://api.segmind.com/v1/video-captioner', $payload);
            // if (!isset($response)) {
            //     return [
            //         'success' => true,
            //         'video' => $shortVideo->s3_url
            //     ];
            // }
            $response = $this->getTran(json_encode($payload));
            // Download and store the captioned video
            $videoContent = $response;
            $tempFile = tempnam(sys_get_temp_dir(), 'captioned_');
            file_put_contents($tempFile, $videoContent);

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'captioned_video.mp4',
                mime_content_type($tempFile),
                null,
                true
            );

            $video_path = $uploadedFile->store('videos', 'public');
            Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));

            // Update the AI media record with the new transcribed video
            $shortVideo->update([
                's3_url' => $video_path,
            ]);

            return [
                'success' => true,
                'video' => Helpers::preSignedS3Url($video_path)
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'video' => $shortVideo->s3_url,
                "message" => "Lỗi tạo phụ đề"
            ];
        }
    }

    function getTran($data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.segmind.com/v1/video-captioner',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'x-api-key: '.env("SEGMIND_API_KEY"),
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        return  $response;
    }

    public function createShortVideoAI($data)
    {
        try {
            $ratio = "768:1280";
            if($data['resolution'] == "16:9") {
                $ratio = "1280:768";
            }
            $requestData = [
                'promptText' => "Mô tả video",
                'promptImage' => $data["s3_image_url"],
                "model" => "gen3a_turbo",
                "watermark" => false,
                'seed' => rand(1, 99999),
                'duration' => (int)$data['duration'],
                'ratio' => $ratio
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('runway.api_key'),
                'Content-Type' => 'application/json',
                'X-Runway-Version' => '2024-11-06'
            ])->timeout(1200)->post('https://api.dev.runwayml.com/v1/image_to_video', $requestData);
            if (!$response->successful()) {
                return ["success" => false, 'details' => $response->json(), "code" => 500];
            }
            $taskId = $response->json()['id'];
            // $taskIds = ["fb49fae2-98da-42fb-9ef3-4f804d237291", "806b3777-a365-4599-97ca-f39722e599d5", "afe0cda2-68e3-4f81-9ab3-2c6f07c7e04e",
            //  "806b3777-a365-4599-97ca-f39722e599d5", "f59e128d-5e23-4ad2-98e6-06ceaf7ed2b0", "edfb411d-3ee2-4154-8214-271b92fe1d7e"];
            // $randId = rand(0,5);
            // $taskId = $taskIds[$randId];
            return ["success" => true, 'task_id' => $taskId, 'code' => "200"];
        } catch (\Exception $e) {
            return ["success" => false, 'message' => $e->getMessage(), 'code' => "500"];
        }
    }

    public function getVideoAIUrl($taskId, $audioUrlS3) {
        try {
            $statusResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('runway.api_key'),
                'Content-Type' => 'application/json',
                'X-Runway-Version' => '2024-11-06'
            ])->timeout(1200)->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");
            if(isset($statusResponse->json()['status'])) {
                $status = $statusResponse->json()['status'];
                if($status == 'SUCCEEDED') {
                    $urls = [];
                    $output = $statusResponse->json()['output'][0];
                    $videoContent = file_get_contents($output);
                    $video_path = 'videos/' . Str::uuid() . '.mp4';
                    $audioUrl = 'videos/' . Str::uuid() . '.mp3';
                    file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
                    file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
                    $urls[] = $video_path;
                    $urls[] = $audioUrl;
                    $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
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
                    $urls[] = $videoPath2;
                    Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
                    $this->cleanupFiles($urls);
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($videoPath2),
                        'success' => true,
                        'code' => 200
                    ]);
                } else if($status == 'FAILED') {
                    return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ","code" => 500]);
                } else {
                    return response()->json(["success" => true, "message" => "Đang chờ tạo video", "code" => 200]);
                }
            } else {
                return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "code" => 500]);
            }
        } catch(Exception $ex) {
            return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "code" => 500]);
        }
    }


    public function createShortVideoAIV2($data)
    {
        try {
            $ratio = $this->resizeImage($data->s3_image_url, true);
            if($ratio < 1) {
                $ratio = "768:1280";
            } else {
                $ratio = "1280:768";
            }
            $promptText = $data->video_description;
            if(empty($promptText)) {
                $promptText = "Mô tả video";
            }
            $promptImage = "";
            if($data->s3_image2_url) {
                $promptImage = [
                    [
                        "uri" => $this->resizeImage($data->s3_image_url),
                        "position" => "first"
                    ],
                    [
                        "uri" => $this->resizeImage($data->s3_image2_url),
                        "position" => "last"
                    ]
                ];
            } else {
                $promptImage = $this->resizeImage($data->s3_image_url);
            }

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
                    $urls = [];
                    $output = $statusResponse->json()['output'][0];
                    $videoContent = file_get_contents($output);
                    $video_path = 'videos/' . Str::uuid() . '.mp4';
                    $audioUrl = 'videos/' . Str::uuid() . '.mp3';
                    $audioUrlS3 = $data->audio_url;
                    file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
                    file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
                    $urls[] = $video_path;
                    $urls[] = $audioUrl;
                    $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
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
                    $urls[] = $videoPath2;
                    Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
                    $this->cleanupFiles($urls);
                    if($data->is_credit) {
                        Auth::user()->chargeCredit('gen3-alphaturbo', '', 5, null, null, 37, 41);
                    }
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($videoPath2),
                        'success' => true,
                        'code' => 200
                    ]);
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

    function createVideoPrediction($startImageUrl, $endImageUrl, $audioUrlS3, $isCreadit = false)
    {
        try {
            $responseData = $this->callApi($startImageUrl, $endImageUrl);
            $responseData = json_decode($responseData, true);
            $get_url = $responseData['urls']['get'];
            $apiToken = env('REPLICATE_API_TOKEN');
            do {
                try {
                    $responseImg = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$apiToken}",
                    ])->get($get_url);
                    $responseImgData = $responseImg->json();
                    $output = $responseImgData['output'];
                } catch (Exception $e) {
                    return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "code" => 500]);
                }
            } while (is_null($output) && $responseImgData['status'] !== 'failed');

            if (is_null($output)) {
                return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "code" => 500]);
            }
            $videoContent = null;
            $videoContent = file_get_contents($output);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $audioUrl = 'videos/' . Str::uuid() . '.mp3';
            file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
            file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
            $urls[] = $video_path;
            $urls[] = $audioUrl;
            $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
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
            $urls[] = $videoPath2;
            Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
            if($isCreadit) {
                Auth::user()->chargeCredit('gen3-alphaturbo', '', 5, null, null, 37, 41);
            }
            $this->cleanupFiles($urls);
            return response()->json([
                's3_url' => Helpers::preSignedS3Url($videoPath2),
                'success' => true,
                'output' => $output,
                'code' => 200
            ]);
        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => "Ảnh của bạn không hợp lệ", "code" => 500]);
        }
    }

    function callApi($startImageUrl, $endImageUrl) {
        $apiToken = env('REPLICATE_API_TOKEN');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.replicate.com/v1/models/luma/ray/predictions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "input": {
            "prompt": "mo ta video",
            "start_image_url":"'.$startImageUrl.'",
            "end_image_url":"'.$endImageUrl.'"
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

    public function mergeVideo($videoURLs, $audioS3Url) {
        if(count($videoURLs) > 0) {
            $textVideo = "";
            $textAudio = "";
            $textFileVideo =  'videos/'.Str::uuid().'_list.txt';
            $textFileAudio =  'videos/audio_'.Str::uuid().'_list.txt';
            $videoResizeFinal = 'videos/final_video_'.Str::uuid().'.mp4';
            $audioUrl = 'videos/final_audio_'.Str::uuid().'.mp3';
            file_put_contents(storage_path('app/public/'.$audioUrl), file_get_contents($audioS3Url));
            $audioFinal = 'audio/' . Str::uuid() . '.mp3';
            FFmpegHelper::cutAudioV2(
                storage_path('app/public/' . $audioUrl),
                storage_path('app/public/' . $audioFinal),
                0,
                10
            );
            $ListVideo[] = $audioFinal;
            $ListVideo[] = $audioUrl;
            $totalDuration = 0;
            for($i = 0; $i < count($videoURLs); $i++) {
                $nameVideo = 'video_'.Str::uuid().''.$i.'.mp4';
                $videoPath = 'videos/'. $nameVideo;
                $s3URL = Helpers::preSignedS3Url($videoURLs[$i]);
                file_put_contents(storage_path('app/public/'.$videoPath), file_get_contents($s3URL));

                $duration = FFmpegHelper::getVideoDuration(storage_path('app/public/'.$videoPath));
                $totalDuration += (int)$duration;

                $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath = 'videos/'. $nameVideoConvert;
                FFmpegHelper::convertVideoToVideo(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$videoConvertPath));
                $nameVideoConvert2 = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath2 = 'videos/'. $nameVideoConvert2;
                FFmpegHelper::cutVideoV2(storage_path('app/public/'.$videoConvertPath), storage_path('app/public/'.$videoConvertPath2), 0, (int)$duration);
                $textVideo = $textVideo."file '".$nameVideoConvert2."'\n";

                $nameAudioConvert = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath = 'videos/'. $nameAudioConvert;
                FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$audioConvertPath));

                $nameAudioConvert2 = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath2 = 'videos/'. $nameAudioConvert2;
                if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
                    FFmpegHelper::createAudio((int)$duration, storage_path('app/public/'.$audioConvertPath2));
                } else {
                    FFmpegHelper::addDuration(storage_path('app/public/'.$audioConvertPath), storage_path('app/public/'.$audioConvertPath2), (int)$duration);
                }
                $textAudio = $textAudio."file '".$nameAudioConvert2."'\n";
                $ListVideo[] = $videoPath;
                $ListVideo[] = $videoConvertPath2;
                $ListVideo[] = $audioConvertPath;
                $ListVideo[] = $videoConvertPath;
                $ListVideo[] = $audioConvertPath2;
            }
            file_put_contents( storage_path('app/public/'.$textFileVideo), $textVideo);
            file_put_contents( storage_path('app/public/'.$textFileAudio), $textAudio);
            $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
            $videoConvertPath = 'videos/'. $nameVideoConvert;
            FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileVideo) , storage_path('app/public/'.$videoConvertPath));
            $ListVideo[] = $videoResizeFinal;
            $ListVideo[] = $videoConvertPath;
            $ListVideo[] = $audioFinal;
            $ListVideo[] = $textFileVideo;
            $ListVideo[] = $textFileAudio;
            $ListVideo[] = $videoConvertPath;
            $dataUpdate['duration'] = $totalDuration;
            $outputPath = 'videos/video_'.Str::uuid().'.mp4';
            $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
            $output_path = 'final_videos/' . Str::uuid() . '.mp4';
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(storage_path('app/public/'.$videoConvertPath),  storage_path('app/public/'.$audioFinal), storage_path('app/public/'.$outputPath),  storage_path('app/public/'.$thumbnailPath));
            $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
            Storage::disk('s3')->put($output_path, $videoContent);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
            }
            $ListVideo[] = $thumbnailPath;
            $ListVideo[] = $outputPath;
            $ListVideo[] = $output_path;
            $dataUpdate['thumbnail'] = $thumbnailPath;
            $dataUpdate['s3_url'] = $output_path;
            $url = Helpers::preSignedS3Url($output_path);
            $this->cleanupFiles($ListVideo);
            Auth::user()->chargeCredit('gen3-alphaturbo', '', 10, null, null, 37, 41);
            return ["success" => true, "s3_url" => $url, "video_path" => $output_path];
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

    public function createShortVideoAIV3($data)
    {
        try {
            $ratio = "1280:768";
            if($data->resolution == '9:16') {
                $ratio = "768:1280";
            }
            $promptText = $data->video_description;
            if(empty($promptText)) {
                $promptText = "Mô tả video";
            }
            $promptImage = "";
            $promptImage = $this->resizeImage($data->s3_url);

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
                    return ["success" => false, 'video_key' => request('video_key'), 'details' => $response->json(), "code" => 500];
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
                    return ["success" => false, 'video_key' => request('video_key'), 'code' => "500"];
                }
                $status = $statusResponse->json()['status'];
                if ($status === 'SUCCEEDED') {
                    $urls = [];
                    $output = $statusResponse->json()['output'][0];
                    $videoContent = file_get_contents($output);
                    $video_path = 'videos/' . Str::uuid() . '.mp4';
                    file_put_contents(storage_path('app/public/' . $video_path), $videoContent);
                    $urls[] = $video_path;
                    $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
                    $urls[] = $thumbnailPath;
                    Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
                    FFmpegHelper::createThumbnail(storage_path('app/public/' . $video_path), storage_path('app/public/' . $thumbnailPath));
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                    $this->cleanupFiles($urls);
                    Auth::user()->chargeCredit('gen3-alphaturbo', '', (int)$data['duration'], null, null, 37, 42);
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($video_path),
                        's3_thumbnail' => Helpers::preSignedS3Url($thumbnailPath),
                        'video_key' => request('video_key'),
                        'success' => true,
                        'code' => 200
                    ]);
                }

                if ($status === 'FAILED') {
                    return ["success" => false, 'video_key' => request('video_key'), 'code' => "500"];
                }

                $attempt++;
                if ($attempt < $maxAttempts) {
                    sleep($pollInterval);
                }
            }
        } catch (\Exception $e) {
            return ["success" => false, 'video_key' => request('video_key'), 'message' => $e->getMessage(), 'code' => "500"];
        }
    }

    public function mergeVideoV2($videoURLs, $audio_url1, $audio_url2, $enableCaption, $totalDuration = 0) {
        if(count($videoURLs) > 0) {
            $textVideo = "";
            $textAudio = "";
            $textFileVideo =  'videos/'.Str::uuid().'_list.txt';
            $textFileAudio =  'videos/audio_'.Str::uuid().'_list.txt';
            $videoResizeFinal = 'videos/final_video_'.Str::uuid().'.mp4';
            $audioUrl = 'videos/final_audio_'.Str::uuid().'.mp3';
            file_put_contents(storage_path('app/public/'.$audioUrl), file_get_contents($audio_url1));
            $audioFinal = 'audio/' . Str::uuid() . '.mp3';
            FFmpegHelper::cutAudioV2(
                storage_path('app/public/' . $audioUrl),
                storage_path('app/public/' . $audioFinal),
                0,
                $totalDuration
            );
            $ListVideo[] = $audioFinal;
            $ListVideo[] = $audioUrl;
            $videos = [];
            $ratio = "";
            for($i = 0; $i < count($videoURLs); $i++) {
                $nameVideo = 'video_'.Str::uuid().''.$i.'.mp4';
                $videoPath = 'videos/'. $nameVideo;
                $s3URL = Helpers::preSignedS3Url($videoURLs[$i]);
                file_put_contents(storage_path('app/public/'.$videoPath), file_get_contents($s3URL));
                if(!$ratio) {
                    $videoInfo = FFmpegHelper::getWidthHeightVideo(storage_path('app/public/'.$videoPath));
                    $videoInfos = (explode(",",$videoInfo));
                    $with = $videoInfos[0];
                    $height = $videoInfos[1];
                    $ratio = $with.":".$height;
                }
                $duration = FFmpegHelper::getVideoDuration(storage_path('app/public/'.$videoPath));

                $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath = 'videos/'. $nameVideoConvert;
                FFmpegHelper::convertVideoToVideo(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$videoConvertPath));
                $nameVideoConvert2 = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath2 = 'videos/'. $nameVideoConvert2;
                FFmpegHelper::cutVideoV2(storage_path('app/public/'.$videoConvertPath), storage_path('app/public/'.$videoConvertPath2), 0, (int)$duration);
                $textVideo = $textVideo."file '".$nameVideoConvert2."'\n";

                $videos[] = storage_path('app/public/'.$videoConvertPath);
                $nameAudioConvert = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath = 'videos/'. $nameAudioConvert;
                FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$audioConvertPath));

                $nameAudioConvert2 = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath2 = 'videos/'. $nameAudioConvert2;
                if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
                    FFmpegHelper::createAudio((int)$duration, storage_path('app/public/'.$audioConvertPath2));
                } else {
                    FFmpegHelper::addDuration(storage_path('app/public/'.$audioConvertPath), storage_path('app/public/'.$audioConvertPath2), (int)$duration);
                }
                $textAudio = $textAudio."file '".$nameAudioConvert2."'\n";
                $ListVideo[] = $videoPath;
                $ListVideo[] = $videoConvertPath2;
                $ListVideo[] = $audioConvertPath;
                $ListVideo[] = $videoConvertPath;
                $ListVideo[] = $audioConvertPath2;
            }
            file_put_contents( storage_path('app/public/'.$textFileVideo), $textVideo);
            file_put_contents( storage_path('app/public/'.$textFileAudio), $textAudio);
            $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
            $videoConvertPath = 'videos/'. $nameVideoConvert;
            $result = $this->mergeVideosToVertical($videos,  storage_path('app/public/'.$videoConvertPath), $ratio);
            // FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileVideo) , storage_path('app/public/'.$videoConvertPath));
            $ListVideo[] = $videoResizeFinal;
            $ListVideo[] = $videoConvertPath;
            $ListVideo[] = $audioFinal;
            $ListVideo[] = $textFileVideo;
            $ListVideo[] = $textFileAudio;
            $ListVideo[] = $videoConvertPath;
            $dataUpdate['duration'] = $totalDuration;
            $outputPath = 'videos/video_'.Str::uuid().'.mp4';
            $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
            $output_path = 'final_videos/' . Str::uuid() . '.mp4';
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(storage_path('app/public/'.$videoConvertPath),  storage_path('app/public/'.$audioFinal), storage_path('app/public/'.$outputPath),  storage_path('app/public/'.$thumbnailPath));
            $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
            $ListVideo[] = $outputPath;
            if($enableCaption) {
                Storage::disk('s3')->put($output_path, $videoContent);
                $outputPath = $this->generateTranscription($output_path, $totalDuration);
                $ListVideo[] = $outputPath;
            }
            if($audio_url2) {
                $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
                Storage::disk('s3')->put($output_path, $videoContent);
                $outputPath = $this->mergeVideoAudio($output_path, $audio_url2);
                $ListVideo[] = $outputPath;
            }

            $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
            Storage::disk('s3')->put($output_path, $videoContent);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
            }
            $ListVideo[] = $thumbnailPath;
            $ListVideo[] = $outputPath;
            $ListVideo[] = $output_path;
            $url = Helpers::preSignedS3Url($output_path);
            $this->cleanupFiles($ListVideo);
            return ["success" => true, "s3_url" => $url, "video_path" => $output_path];
        }
    }

    public function mergeVideosToVertical(array $inputVideos, string $outputPath, $ratio): bool
    {
        $storagePath = storage_path('app/public');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath);
        }

        $concatListPath = $storagePath . '/'.Str::uuid().'txt';
        $urls[] = $concatListPath;
        $fixedVideos = [];

        foreach ($inputVideos as $index => $videoPath) {
            $fixedPath = $storagePath.'/'.Str::uuid().".mp4";
            $fixedVideos[] = $fixedPath;
            $urls[] = $fixedPath;
            // FFmpeg: scale video thành 9:16, căn giữa, convert H264 + AAC
            $cmd = "ffmpeg -i " . escapeshellarg($videoPath) .
                " -vf \"scale=$ratio:force_original_aspect_ratio=decrease," .
                "pad=$ratio:(ow-iw)/2:(oh-ih)/2,setsar=1\" " .
                "-c:v libx264 -preset fast -crf 23 -c:a aac -movflags +faststart " .
                escapeshellarg($fixedPath) . " -y";
            shell_exec($cmd);
        }

        // Tạo danh sách concat
        $concatText = collect($fixedVideos)->map(fn($v) => "file '" . str_replace("'", "'\\''", $v) . "'")->implode("\n");
        file_put_contents($concatListPath, $concatText);

        // Lệnh nối các video
        $cmdMerge = "ffmpeg -f concat -safe 0 -i " . escapeshellarg($concatListPath) .
            " -c:v libx264 -preset fast -crf 23 -c:a aac -movflags +faststart " .
            escapeshellarg($outputPath) . " -y";
        shell_exec($cmdMerge);
        $this->cleanupFiles($urls);
        return File::exists($outputPath);
    }

    public function mergeVideoAudio($video_url, $audioUrlS3) {
        $video_url = Helpers::preSignedS3Url($video_url);

        $video_path = 'videos/' . Str::uuid() . '.mp4';
        $urls[] = $video_path;
        file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
        $audioUrl = 'audio/' . Str::uuid() . '.mp3';
        file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
        $urls[] = $audioUrl;
        $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
        $audioPath2 = 'audio/' . Str::uuid() . '.mp3';
        $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
        FFmpegHelper::cutAudioV2(
            storage_path('app/public/' . $audioUrl),
            storage_path('app/public/' . $audioPath2),
            0,
            (int)$videoDuration
        );
        $urls[] = $audioPath2;

        $nameAudioConvert = 'audio_convert_'.Str::uuid().'.mp3';
        $audioConvertPath = 'videos/'. $nameAudioConvert;
        FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$video_path) , storage_path('app/public/'.$audioConvertPath));
        if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null
            );
        } else {
            FFmpegHelper::mergerVideoAudioV2(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null,
                "0.2"
            );
        }
        $url[] = $audioConvertPath;
        Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        $this->cleanupFiles($urls);
        return $videoPath2;
    }

    public function generateTranscription($s3Url) {
        $video_url = Helpers::preSignedS3Url($s3Url);

        $payload = [
            'MaxChars' => 10,
            'bg_blur' => false,  // Changed from true to false
            'bg_color' => "None",  // Changed from null to "None"
            'color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
            'font' => isset($data['font']) ? $data['font'] : "Arial/Arial_Bold.ttf",  // Changed font
            'fontsize' => 5,  // Changed from 7.0 to 7
            'highlight_color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
            'kerning' => -2,  // Changed from -2.0 to -2
            'opacity' => 0,  // Changed from 1.0 to 0
            'right_to_left' => false,  // Changed from true to false
            'stroke_color' => "black",
            'stroke_width' => $data['stroke_width'] ?? 2,
            'subs_position' =>  isset($data['subs_position']) ? $data['subs_position'] : "bottom75",
            'input_video' => $video_url
        ];
        $response = $this->getTran(json_encode($payload));

        // Download and store the captioned video
        $videoContent = $response;
        $video_path = 'videos/'. 'video_'.Str::uuid().'.mp4';
        file_put_contents(storage_path('app/public/'.$video_path), $videoContent);
        return $video_path;
    }
}

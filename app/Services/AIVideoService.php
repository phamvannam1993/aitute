<?php

namespace App\Services;

use App\Models\AIGeneratedMedia;
use App\Models\Lipsync;

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
use App\Models\AIVideo;
use App\Models\McVirtual;
use App\Models\SwapFace;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AIVideoService
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

    public function translatePrompt($prompt, $language, $user = null)
    {
        try {
            // dịch prompt sang ngôn ngữ tương ứng
            $translatedTitle = $this->chatGPTService->getResponse("Translate the following text to " . $language . ": \"$prompt\", just give me the text in " . $language . ", DO NOT explain ANYELSE.", null, 'gpt-4o-mini', $user);
            // Đảm bảo kết quả dịch không phải là null
            if (empty($translatedTitle)) {
                Log::warning("Translation result is empty. Returning original prompt.");
                return $prompt;
            }
            return $translatedTitle;
        } catch (Exception $e) {
            Log::error("translatePrompt: " . $e->getMessage());
            return $prompt;
        }
    }

    function generateVideo($prompt_video, $language = 'vi-VN', $user = null)
    {
        try {
            Log::info('generateVideo:', ['prompt' => $prompt_video]);
            $translatedTitle = $this->translatePrompt($prompt_video, 'en-US', $user);
            $requestData = [
                'version' => 'beecf59c4aee8d81bf04f0381033dfa10dc16e845b4ae00d281e2fa377e48a9f',
                'input' => [
                    "seed" => 255224556,
                    "prompt" => $translatedTitle . ", " . "8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3",
                ],
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->apiToken}",
            ])->post('https://api.replicate.com/v1/predictions', $requestData);
            $responseData = $response->json();
            Log::info('generateVideo:', ['response' => $responseData]);
            $get_url = $responseData['urls']['get'];
            do {
                try {

                    $responseImg = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$this->apiToken}",
                    ])->get($get_url);
                    $responseImgData = $responseImg->json();
                    $output = $responseImgData['output'];
                } catch (Exception $e) {
                    Log::error("generateVideo: " . $e->getMessage());
                    return null;
                }
            } while (is_null($output) && $responseImgData['status'] !== 'failed');

            if (is_null($output)) {
                Log::error('Failed to generate diff.');
                return null;
            }

            Log::info('generateVideo:', ['output' => $output]);

            $videoContent = null;
            $videoContent = file_get_contents($output);
            $tempFile = tempnam(sys_get_temp_dir(), 'diff_');
            file_put_contents($tempFile, $videoContent);
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'video_template.mp4',
                mime_content_type($tempFile),
                null,
                true
            );
            Log::info('generateVideo:', ['uploadedFile' => $uploadedFile]);
            $video_path = $uploadedFile->store('videos', 'public');
            return $video_path;
        } catch (Exception $e) {
            Log::error("generateVideo: " . $e->getMessage());
            return null;
        }
    }

    public function generateContentAudio($prompt, $language = 'vi-VN', $durations = 2, $user = null)
    {
        try {
            $responseData = $this->chatGPTService->getResponse(
                "You are tasked with creating SSML (Speech Synthesis Markup Language) content for an AI-generated video based on a given topic. The content should be suitable for Google Text-to-Speech and match the duration of the video." .
                    "Here's the topic for the video:" .
                    "<topic>" .
                    "{{" . $prompt . "}}" .
                    "</topic>" .
                    "The duration of the video is:" . $durations . " seconds" .
                    "The language of the video is:" .
                    "<language>" . $language . "</language>" .
                    "Follow these steps to create the SSML content:" .
                    "1. Analyze the topic and create a brief outline of key points to cover in the video." .
                    "2. Structure the content using SSML tags to enhance the speech output. Use appropriate tags such as <break>, <prosody>, and <emphasis> to improve the natural flow and emphasis of the speech." .
                    "3. Adjust the content length to match the video duration. It must be only in " . $durations . " seconds" .
                    "4. Fine-tune the script by adding or removing content and adjusting SSML tags to closely match the target word count and video duration." .
                    "5. Format your final output as follows:" .
                    "   <ssml>" .
                    "   (Place your SSML-formatted script here)" .
                    "   </ssml>" .
                    "Ensure that the SSML content is well-structured, informative, and timed appropriately for the video duration. The content should flow naturally and engage the viewer throughout the video.",
                null,
                'gpt-4o-mini',
                $user
            );

            // Log the entire response for debugging
            Log::info('Audio content:', ['response' => $responseData]);
            if (!$responseData) {
                throw new Exception('No text content in Claude API response');
            }

            preg_match('/<ssml>(.*?)<\/ssml>/s', $responseData, $matches);

            if (isset($matches[0])) {
                $responseData = $matches[0];
            }

            return [
                'ssml' => $responseData,
                'duration' => $durations
            ];
        } catch (Exception $e) {
            Log::error("generateContentAudio error: " . $e->getMessage());
            return null;
        }
    }
    public function generateVideoAudio($data, $user_id)
    {
        try {
            $user = User::find($user_id);
            $prompt = $data['video_description'] . ", with durations is: " . $data['duration'] . " seconds";

            $prompt = $this->translatePrompt($prompt, 'en-US', $user);
            [$width, $height] = explode('x', $data['resolution']);
            $audio_path_cut = "";
            if($data['audio_description'] || isset($data["audio_path"])) {
                $audio_path_cut = 'audio/' . Str::uuid() . '.mp3';
                if (!isset($data["audio_path"])) {
                    $prompt_audio = $data['audio_description'] . ", with durations is: " . $data['duration'] . " seconds";
                    $content = $this->generateContentAudio($prompt_audio, $data['language'], $data['duration'], $user);
                    $audio_path_cut = $this->textToSpeechService->ssml2Audio($data['audio_description'], $data['language']);
                    FFmpegHelper::cutAudioV2(
                        storage_path('app/public/' . $audio_path_cut),
                        storage_path('app/public/' . $audio_path_cut),
                        0,
                        $data['duration']
                    );
                } else {
                    FFmpegHelper::cutAudioV2(
                        storage_path('app/public/' . $data["audio_path"]),
                        storage_path('app/public/' . $audio_path_cut),
                        0,
                        $data['duration']
                    );
                }
            }
          
            $video_path = $this->generateVideo($prompt);
            $outputPath = 'videos/' . Str::uuid() . '.mp4';
            if($audio_path_cut) {
                $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
                FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                    storage_path('app/public/' . $video_path),
                    storage_path('app/public/' . $audio_path_cut),
                    storage_path('app/public/' . $outputPath),
                    storage_path('app/public/' . $thumbnailPath)
                );
            } else {
                $outputPath = $video_path;
            }
           
            $dataSave = [
                'user_id' => $user_id,
                'description' => $data['video_description'] . "| " . $data['audio_description'],
                'model' => $data['model'],
                'duration' => $data['duration'],
                'width' => $width,
                'height' => $height,
                's3_url' => $outputPath,
                'thumbnail' => $thumbnailPath,
                'type' => 'video',
            ];
            if(Storage::disk('public')->get($outputPath)) {
                Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                }
                $record = $this->storeMedia($dataSave);
                return [
                    's3_url' => Helpers::preSignedS3Url($outputPath),
                    'path'  => $outputPath,
                    'generate_file' => $record,
                    'id' => isset($record->id) ? $record->id : 0
                ];
            } else {
                $dataSave["outputPath"] = $video_path;
                Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                }
                $record = $this->storeMedia($dataSave);
                return [
                    's3_url' => Helpers::preSignedS3Url($outputPath),
                    'path'  => $outputPath,
                    'generate_file' => $record,
                    'id' => isset($record->id) ? $record->id : 0
                ];
            }
          
        } catch (Exception $e) {
            return response()->json(["success" => false, 'message' =>  $e->getMessage()], 500);
        }
    }

    public function generateVideoAudioRunway($data, $user_id)
    {
        try {
            $user = User::find($user_id);
            $prompt = $data['video_description'] . ", with durations is: " . $data['duration'] . " seconds";

            $prompt = $this->translatePrompt($prompt, 'en-US', $user);
            $audio_path_cut = "";
            $dataFiles = [];
            if($data["audio_url"]) {
                $audioContent = file_get_contents($data["audio_url"]);
                $audioPath = 'videos/' . Str::uuid() . '.mp3';
                $audio_path_cut = 'videos/' . Str::uuid() . '.mp3';
                file_put_contents(storage_path('app/public/' . $audioPath), $audioContent);
                FFmpegHelper::cutAudioV2(
                    storage_path('app/public/' . $audioPath),
                    storage_path('app/public/' . $audio_path_cut),
                    0,
                    $data['duration']
                );
                $dataFiles[] = storage_path('app/public/' . $audioPath);
            }
            $this->cleanupFiles($dataFiles);
            $resCallApi = $this->generateVideoRunway($prompt, $data, $user_id);
            $resCallApi["audio_path_cut"] =  $audio_path_cut;
            return response()->json($resCallApi,  $resCallApi["code"]);
        } catch (Exception $e) {
            return response()->json(["success" => false, 'message' =>  $e->getMessage()], 500);
        }
    }

    function generateVideoRunway($prompt_video, $data,  $user_id)
    {
        try {
            $ratio = "768:1280";
            if($data['resolution'] == "16:9") {
                $ratio = "1280:768";
            }
            $requestData = [
                'promptText' => "Mô tả video",
                'promptImage' => $data["promptImage"],
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
                "806b3777-a365-4599-97ca-f39722e599d5", "f59e128d-5e23-4ad2-98e6-06ceaf7ed2b0", "edfb411d-3ee2-4154-8214-271b92fe1d7e"];
               $randId = rand(0,5);
               $taskId = $taskIds[$randId];
            }
            $this->storeMedia([
                'user_id' => $user_id,
                'description' => $data['video_description'],
                'model' => $data['model'],
                'duration' => $data['duration'],
                'task_id' => $taskId,
                's3_url' => "",
                'thumbnail' => "",
                'type' => 'video',
            ]);
            return ["success" => true, 'task_id' => $taskId, 'code' => "200"];
        } catch (\Exception $e) {
            return ["success" => false, 'message' => $e->getMessage(), 'code' => "500"];
        }
    }

    function getVideoURL($taskId, $audio_path_cut) {
        $aiMedia =  AIGeneratedMedia::where('task_id', $taskId)->orderBy("id", "DESC")->first();
        if(empty($aiMedia )) {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"], 500);
        }
        $statusResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('runway.api_key'),
            'Content-Type' => 'application/json',
            'X-Runway-Version' => '2024-11-06'
        ])->timeout(1200)->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");
        if(isset( $statusResponse->json()['status'])) {
            $status = $statusResponse->json()['status'];
            if($status == 'SUCCEEDED') {
                $output = $statusResponse->json()['output'][0];
                $videoContent = file_get_contents($output);
                $videoPath = 'videos/' . Str::uuid() . '.mp4';
                file_put_contents(storage_path('app/public/' . $videoPath), $videoContent);
                $video_path = $videoPath;
                $video_path_cut = 'videos/' . Str::uuid() . '.mp4';
                $outputPath = 'videos/' . Str::uuid() . '.mp4';
                $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
                if(!empty($audio_path_cut)) {
                    FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                        storage_path('app/public/' . $video_path),
                        storage_path('app/public/' . $audio_path_cut),
                        storage_path('app/public/' . $outputPath),
                        storage_path('app/public/' . $thumbnailPath)
                    );
                    FFmpegHelper::cutVideoV2(
                        storage_path('app/public/' . $outputPath),
                        storage_path('app/public/' . $video_path_cut),
                        0,
                        $aiMedia->duration
                    );
                }
               
                if(!empty($audio_path_cut) && Storage::disk('public')->get($video_path_cut)) {
                    Storage::disk('s3')->put($video_path_cut, Storage::disk('public')->get($video_path_cut));
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                    }
                    $aiMedia->update(["s3_url" => $video_path_cut, "thumbnail" => $thumbnailPath]);
                    $this->cleanupFiles([
                        $video_path,
                        $audio_path_cut,
                        $video_path_cut,
                        $outputPath,
                        $thumbnailPath,
                        $videoPath
                    ]);
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($video_path_cut),
                        'generate_file' => $aiMedia,
                        'id' => isset($aiMedia->id) ? $aiMedia->id : 0
                    ]);
                } else {
                    $outputPath = $video_path;
                    Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
                    FFmpegHelper::createThumbnail(
                        storage_path('app/public/' . $video_path),
                        storage_path('app/public/' . $thumbnailPath)
                    );
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                    }
                    $aiMedia->update(["s3_url" => $outputPath, "thumbnail" => $thumbnailPath]);
                    $this->cleanupFiles([
                        $video_path,
                        $audio_path_cut,
                        $video_path_cut,
                        $outputPath,
                        $thumbnailPath,
                        $videoPath
                    ]);
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($outputPath),
                        'generate_file' => $aiMedia,
                        'id' => isset($aiMedia->id) ? $aiMedia->id : 0
                    ]);
                } 
            } else if($status == 'FAILED') {
                return response()->json(["success" => false, "message" => "Có lỗi xảy ra"], 500);
            } else {
                return response()->json(["success" => true, "message" => "đang chờ tạo video"], 200);
            }
        } else {
            return response()->json(["success" => false, "message" => "Có lỗi xảy ra"], 500);
        }
    }

    public function checkStatusRun($model) {
        $aiMedia = AIGeneratedMedia::where("model", $model)->orderBy("created_at", "DESC")->first();
        if(empty($aiMedia)) {
            return true;
        }
        $taskId = $aiMedia["task_id"];
        $statusResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('runway.api_key'),
            'Content-Type' => 'application/json',
            'X-Runway-Version' => '2024-11-06'
        ])->timeout(1200)->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");
        if(isset( $statusResponse->json()['status'])) {
            $status = $statusResponse->json()['status'];
            if($status == 'THROTTLED') {
                return false;
            }
        }
        return true;
    }

    public function updateById($id, $data = []) {
        return  AIGeneratedMedia::where("id", $id)->update($data);
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

    public function mergeVideoAudio($file_video, $file_audio, $user)
    {
        try {
            $video_path = $file_video->store('videos_upload', 'public');
            $audio_path = $file_audio->store('audios_upload', 'public');

            $video_path = storage_path('app/public/' . $video_path);
            $audio_path = storage_path('app/public/' . $audio_path);

            $video_duration = FFmpegHelper::getVideoDuration($video_path);
            $audio_duration = FFmpegHelper::getAudioDuration($audio_path);

            $cut_duration = 0;
            if ($video_duration > $audio_duration) {
                $cut_duration = $audio_duration;
            } else {
                $cut_duration = $video_duration;
            }

            // mkdir final_videos
            if (!file_exists(storage_path('app/public/final_videos'))) {
                mkdir(storage_path('app/public/final_videos'), 0777, true);
            }

            $output_video_path = 'final_videos/' . Str::uuid() . '.mp4';
            $output_audio_path = 'final_videos/' . Str::uuid() . '.mp3';

            $output_video_path = storage_path('app/public/' . $output_video_path);
            $output_audio_path = storage_path('app/public/' . $output_audio_path);

            FFmpegHelper::cutVideo($video_path, $output_video_path, 0, $cut_duration);
            FFmpegHelper::cutAudio($audio_path, $output_audio_path, 0, $cut_duration);

            $video_duration = FFmpegHelper::getVideoDuration($output_video_path);
            $audio_duration = FFmpegHelper::getAudioDuration($output_audio_path);

            $output_path = 'final_videos/' . Str::uuid() . '.mp4';
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                $output_video_path,
                $output_audio_path,
                storage_path('app/public/' . $output_path)
            );

            $videoContent = file_get_contents(storage_path('app/public/' . $output_path));
            Storage::disk('s3')->put($output_path, $videoContent);

            // remove tmp file
            $this->cleanupFiles([
                $output_video_path,
                $output_audio_path,
                storage_path('app/public/' . $output_path)
            ]);

            $record = $this->storeMedia([
                'user_id' => $user->id,
                'description' => "",
                'style' => "",
                'artist' => "",
                'width' => 0,
                'height' => 0,
                's3_url' => $output_path,
                'type' => 'video',
            ]);

            $url = Helpers::preSignedS3Url($output_path);
            return [
                'url' => $url,
                'generate_file' => $record
            ];
        } catch (Exception $e) {
            Log::error("mergeVideoAudioWith: " . $e->getMessage());
            return [
                'error' => $e->getMessage()
            ];
        }
    }


    public function getHistories($user, $isVideoMerge = false, $is_image = false, $type_query = "")
    {
        try {
            $userId = $user->id;
            $per_page = 8;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)
            ->where('type', 'video')->where('thumbnail', '!=',  '');
            $modelMerge = "merge-video";
            if(empty($type_query)) {
                if($is_image) {
                    $modelMerge =  "merge-video-image";
                } 
                if(!$isVideoMerge) {
                    $result = $result->where('model', '!=', $modelMerge);
                } else {
                    $result = $result->where('model', $modelMerge);
                }
                if(!$isVideoMerge) {
                    $result = $result->where('model', '!=', 'merge-video-image')->where('model', '!=', 'merge-video')->where('model', '!=', 'merge-video-all');
                    if(!$is_image) {
                        $result = $result->where(function ($query) {
                            $query->where('model', '!=', 'img-video')
                                ->orWhereNull('model');
                        });
                    } else {
                        $result = $result->where(function ($query) {
                            $query->where('model', 'img-video')
                                ->orWhereNull('model');
                        });
                    }
                }
            } else {
                if(!$isVideoMerge) {
                    $result = $result->whereNotIn('model', ['merge-video-image', 'merge-video', 'merge-video-all']);
                } else {
                    $result = $result->where('model', 'merge-video-all');
                }
            }
            return $result->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height', 'thumbnail')
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function mergeAudioVideo($aiMediaId, $audioUrl) {
        $aiMedia = AIGeneratedMedia::find($aiMediaId);
        $urls = [];
        $video_url_2 = "";
        $video_url = $aiMedia->s3_url;
        if($aiMedia->transcription_url) {
            $video_url = $aiMedia->transcription_url;
        }
        if($aiMedia->s3_url_video_image) {
            $video_url_2 = $aiMedia->s3_url_video_image;
        }
        $dataUpdate = [];
        $dataUpdate["is_upload_audio"] = true;
        $audioUrlS3 = Helpers::preSignedS3Url($audioUrl);
      
        file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
        for ($i = 0 ; $i < 2; $i++) {
            if($i == 1 && !empty($video_url_2)) {
                $video_url = $video_url_2;
            } else if($i != 0) {
                continue;
            }
            $video_url = Helpers::preSignedS3Url($video_url);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $urls[] = $video_path;
            file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
        
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
            $urls[] = $videoPath2;
            if($aiMedia->ratio) {
                $outputPath = 'videos/' . Str::uuid() . '.mp4';
                FFmpegHelper::convertRatio(storage_path('app/public/' .$videoPath2), storage_path('app/public/' .$outputPath), $aiMedia->ratio);
                $dataUpdate["s3_url_video_ratio"] = $outputPath;
                Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
                $urls[] = $outputPath;
            }
            Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
            $dataUpdate["s3_url_video_result"] = $videoPath2;   
            if($i == 0) {
                $dataUpdate["s3_url_video_audio"] = $videoPath2;
            } 
        } 
        $this->cleanupFiles($urls); 
        AIGeneratedMedia::where("id", $aiMediaId)->update($dataUpdate);
    }
    
    public function mergeAudioVideoLipsync($lipsynId, $audioUrl) {
        $lipsyn = Lipsync::where("lip_sync_id", $lipsynId)->first();
        $video_url = $lipsyn->result;
        if(!empty($lipsyn->transcription_url)) {
            $video_url = $lipsyn->transcription_url;
        }
        $video_url = Helpers::preSignedS3Url($video_url);
        $video_path = 'videos/' . Str::uuid() . '.mp4';
        file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
        $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
        $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
        $audioPath2 = 'audio/' . Str::uuid() . '.mp3';


        $audioUrlS3 = Helpers::preSignedS3Url($audioUrl);

        file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
        $urls[] = $audioUrl;

        FFmpegHelper::cutAudioV2(
            storage_path('app/public/' . $audioUrl),
            storage_path('app/public/' . $audioPath2),
            0,
            (int)$videoDuration
        );

        FFmpegHelper::mergerVideoAudioV2(
            storage_path('app/public/' . $video_path),
            storage_path('app/public/' . $audioPath2),
            storage_path('app/public/' . $videoPath2),
            null,
            '0.2'
        );
        $urls[] = $audioPath2;
        $urls[] = $videoPath2;
        Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        $this->cleanupFiles($urls);
        Lipsync::where("lip_sync_id", $lipsynId)->update(["s3_url_video_audio" => $videoPath2, "is_upload_audio" => true]);
    }

    public function convertRatio($aiMediaId, $ratio) {
        $urls = [];
        $dataUpdate = ["is_convert_ratio" => true, 'ratio' => $ratio];
        $aiMedia = AIGeneratedMedia::find($aiMediaId);
        if($aiMedia->ratio == $ratio) {
            AIGeneratedMedia::where("id", $aiMediaId)->update($dataUpdate);
            return true;
        }
        if($aiMedia->transcription_url) {
            $video_url = $aiMedia->transcription_url;
            $video_url = Helpers::preSignedS3Url($video_url);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $outputPath = 'videos/' . Str::uuid() . '.mp4';
            $urls[] = $video_path;
            file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
            FFmpegHelper::convertRatio(storage_path('app/public/' .$video_path), storage_path('app/public/' .$outputPath), $ratio);
            $dataUpdate["s3_url_video_ratio"] = $outputPath;
            Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
            $urls[] = $outputPath;
        } else {
            $video_url = $aiMedia->s3_url;
            $video_url = Helpers::preSignedS3Url($video_url);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $outputPath = 'videos/' . Str::uuid() . '.mp4';
            $urls[] = $video_path;
            file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
            FFmpegHelper::convertRatio(storage_path('app/public/' .$video_path), storage_path('app/public/' .$outputPath), $ratio);
            $dataUpdate["s3_url_video_ratio"] = $outputPath;
            Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
            $urls[] = $outputPath;
        }

        if($aiMedia->s3_url_video_result) {
            $video_url = $aiMedia->s3_url_video_result;
            $video_url = Helpers::preSignedS3Url($video_url);
            $video_path = 'videos/' . Str::uuid() . '.mp4';
            $outputPath = 'videos/' . Str::uuid() . '.mp4';
            $urls[] = $video_path;
            file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
            FFmpegHelper::convertRatio(storage_path('app/public/' .$video_path), storage_path('app/public/' .$outputPath), $ratio);
            $dataUpdate["s3_url_video_ratio"] = $outputPath;
            Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
            $urls[] = $outputPath;
        }
        $this->cleanupFiles($urls);
        AIGeneratedMedia::where("id", $aiMediaId)->update($dataUpdate);
    }

    public function convertFileToMp4($videoPathS3, $video_convert) {
        $videoPath = 'videos/' . Str::uuid() . '.mp4';
        file_put_contents(storage_path('app/public/' .$videoPath), file_get_contents($videoPathS3));
        FFmpegHelper::convertContentToMp4(storage_path("app/public/{$videoPath}"), storage_path("app/public/{$video_convert}"));
        Storage::disk('s3')->put($video_convert, Storage::disk('public')->get($video_convert));
        $videoS3 = Helpers::preSignedS3Url($video_convert);
        $this->cleanupFiles([$videoPath, $video_convert]);
        return true;
    }

    public function getHistoriesWithoutPaginate($user, $isImage = false, $isMerge = false, $model = "")
    {
        try {
            $userId = $user->id;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('type', 'video')->where('thumbnail', '!=',  '');
          
            if(!$isMerge) {
                if($isImage) {
                    $result =  $result->where('model', 'img-video');
                } else {
                    $result =  $result->where(function ($query) {
                        $query->where('model', '!=', 'img-video');
                    });
                    $result = $result->whereNotIn('model', ['merge-video-image', 'merge-video-all', 'merge-video']);
                }
            } else {
                if($model == 'merge-video-image') {
                    $result =  $result->where('model', 'merge-video-image');
                } else {
                    $result =  $result->where('model', 'merge-video');
                }
            }

            $result =  $result->select('id', 'model', 'description', 'style', 'artist', 's3_url', 'width', 'height', 'thumbnail')
                ->orderBy('created_at', 'desc')
                ->limit(4) // limit result 3 result
                ->get();
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function getHistoriesImg2Video($user)
    {
        try {
            $userId = $user->id;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)
                ->where('type', 'video')
                ->where('thumbnail', '!=',  '')
                ->where('model', 'img-video')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height', 'thumbnail')
                ->orderBy('created_at', 'desc')
                ->limit(4) // limit result 4 result
                ->get();
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function getHistoryDetail($user, $id)
    {
        try {
            $userId = $user->id;

            $result = AIGeneratedMedia::where('user_id', $userId)
                ->where('type', 'video')
                ->where('id', $id)
                ->select('*')
                ->firstOrFail();

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    // Get danh sách video được tạo từ hình ảnh.
    public function getImgToVideoHistory($per_page = 8)
    {
        try {
            $userId = auth('web')->id();

            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('model', 'img-video')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height', 'thumbnail')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video tạo từ hình ảnh: ' . $e->getMessage());
            return null;
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
    public function deleteFileById($id)
    {
        try {
            $message = AIGeneratedMedia::find(id: $id);
            if (!$message) {
                return [
                    'message' => 'File không tồn tại.'
                ];
            }
            $message->delete();
            return [
                'message' => 'Xoá file thành công.'
            ];
        } catch (Exception $e) {
            Log::error('Lỗi khi xoá file: ' . $e->getMessage());
            return [
                'message' => 'Lỗi khi xoá file.'
            ];
        }
    }

    public function storeTextToVideo($title, $slideData, $theme)
    {
        $userId = auth('web')->id();
        // Get the first image in the first slide to thumbnail
        $thumbnail = $slideData[0]['components'][0]['src'];
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'content' => is_array($slideData) ? json_encode($slideData) : $slideData,
            'theme' => is_array($theme) ? json_encode($theme) : $theme,
            'thumbnail' => is_array($thumbnail) ? json_encode($thumbnail) : $thumbnail,
        ];
        return $this->aiVideoRepository->create($data);
    }

    public function getTexToVideoHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 8;
            $result = AIVideo::where('user_id', '=', $userId)
                ->select('id', 'title', 'thumbnail')
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function getVideoById($id)
    {
        try {
            $record = $this->aiVideoRepository->findOrFail($id);
            return $record;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Video not found'], 404);
        } catch (Exception $e) {
            Log::error("Error retrieving video with ID $id: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving the video'], 500);
        }
    }

    public function createOrUpdateVideo($data)
    {
        try {
            $attributes = ['id' => $data['video_id']];
            $thumbnail = $data['slides'][0]['props']['components'][0]['src'];

            $iniVirtual = [
                'x' => 100,
                'y' => 130,
                'width' => 300,
                'animation' => '',
                'delayTime' => 0,
            ];
            if (is_array($data['slides'])) {
                foreach ($data['slides'] as $key => $slide) {
                    // collect unused data
                    if (!empty($slide['props']['components']) && is_array($slide['props']['components'])) {

                        foreach ($slide['props']['components'] as $i => $item) {
                            if (empty($item['style']) || empty($item['src'])) {
                                unset($data['slides'][$key]['props']['components'][$i]);
                            }
                        }
                    }

                    // fix missing data
                    if (!empty($slide['props']['virtual']) && is_array($slide['props']['virtual'])) {
                        foreach ($iniVirtual as $k => $unit) {
                            if (empty($slide['props']['virtual'][$k])) {
                                $data['slides'][$key]['props']['virtual'][$k] = $unit;
                            }
                        }
                    }
                }
            }

            $values = [
                'content' => is_array($data['slides']) ? json_encode($data['slides']) : $data['slides'],
                'theme' => is_array($data['theme']) ? json_encode($data['theme']) : $data['theme'],
                'thumbnail' => isset($thumbnail) ? $thumbnail : null,
            ];


            $result = $this->aiVideoRepository->updateOrCreate($attributes, $values);

            Log::info('Video đã được tạo hoặc cập nhật thành công:', ['video_id' => $data['video_id']]);

            return [
                'success' => true,
                'video' => $result,
                'message' => 'Video đã được tạo hoặc cập nhật thành công!'
            ];
        } catch (Exception $e) {
            Log::error("createOrUpdateVideo lỗi: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể tạo hoặc cập nhật video. Lỗi: ' . $e->getMessage()
            ];
        }
    }

    public function deleteVideoById($id)
    {
        try {
            $video = AIVideo::findOrFail($id);
            $video->delete();

            return [
                'success' => true,
                'message' => 'Video đã được xóa thành công.'
            ];
        } catch (ModelNotFoundException $e) {
            Log::error("deleteVideoById: Video không tìm thấy với ID {$id}.");
            return [
                'success' => false,
                'message' => 'Video không tìm thấy.'
            ];
        } catch (Exception $e) {
            Log::error("deleteVideoById: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình xóa video.'
            ];
        }
    }

    public function getHistoriesSwapFace($user)
    {
        try {
            $userId = $user->id;
            $result = SwapFace::where('user_id', '=', $userId)
                ->select('id', 'img', 'result')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Lịch sử swap face:', ['result' => $result]);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function getHistoryDetailSwapFace($user, $id)
    {
        try {
            $userId = $user->id;

            $result = SwapFace::where('user_id', $userId)
                ->where('id', $id)
                ->select('id', 'img', 'video')
                ->firstOrFail();

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadTaklingPhotoHeyGen($file)
    {
        $client = new Client();

        $apiKey = env('HEYGEN_API_KEY');
        $url = 'https://upload.heygen.com/v1/talking_photo';
        $fileContent = file_get_contents($file->getRealPath());
        $mimeType = $file->getMimeType();
        Log::info("type of image is: ", ['type' => $mimeType]);
        $response = $client->request('POST', $url, [
            'body' => $fileContent,
            'headers' => [
                'accept' => 'application/json',
                'content-type' => $mimeType,
                'X-Api-Key' => $apiKey,
            ],
        ]);
        Log::info('uploadTaklingPhotoHeyGen:', ['response' => json_decode($response->getBody())]);
        return json_decode($response->getBody());
    }

    public function createAnAvatarVideo($body)
    {
        try {
            $client = new Client();

            $apiKey = env('HEYGEN_API_KEY');
            $url = 'https://api.heygen.com/v2/video/generate';
            $response = $client->request('POST', $url, [
                'body' => $body,
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'x-api-key' => $apiKey,
                ],
            ]);
            Log::info('createAnAvatarVideo:', ['response' => json_decode($response->getBody())]);
            return json_decode($response->getBody());
        } catch(\Exception $ex) {
            return ["success" => false, "error" => "Chức năng đang bảo trì. Bạn vui lòng quay lại sau."];
        }
    }
    public function retrieveVideo($videoId)
    {
        $client = new Client();

        $apiKey = env('HEYGEN_API_KEY');
        $url = 'https://api.heygen.com/v1/video_status.get?video_id=' . $videoId;
        $response = $client->request('GET', $url, [
            'headers' => [
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ],
        ]);
        Log::info('retrieveVideo:', ['response' => json_decode($response->getBody())]);
        return json_decode($response->getBody());
    }
    public function uploadAssetToHeyGen($file)
    {
        $client = new Client();
        $apiKey = env('HEYGEN_API_KEY');

        $fileContent = file_get_contents($file->getRealPath());
        $mimeType = $file->getMimeType();
        $response = $client->request('POST', 'https://upload.heygen.com/v1/asset', [
            'body' => $fileContent,
            'headers' => [
                'accept' => 'application/json',
                'content-type' => $mimeType,
                'X-API-KEY' => $apiKey,
            ],
        ]);
        Log::info('uploadAssetToHeyGen:', ['response' => json_decode($response->getBody())]);
        return json_decode($response->getBody());
    }

    public function getHeygenAvatars()
    {
        $apiKey = env('HEYGEN_API_KEY');
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $apiKey,
        ])->get('https://api.heygen.com/v2/avatars');

        if ($response->successful()) {
            return $response->json()['data']['avatars'] ?? [];
        }

        throw new Exception('Failed to fetch HeyGen avatars: ' . $response->body());
    }

    public function getHeygenTalkingPhotos()
    {
        $apiKey = env('HEYGEN_API_KEY');
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $apiKey,
        ])->get('https://api.heygen.com/v1/talking_photo.list');

        try {
            if ($response->successful()) {
                $data = json_decode($response->getBody())->data;
                return $data;
            }
        } catch (\Throwable $th) {
            Log::error('getHeygenTalkingPhotos:', ['error' => $th->getMessage()]);
            return [];
        }
    }

    public function createVideoWithTranscription($user, $data)
    {
        try {
            $ai_media_id = $data['ai_media_id'];
            $aiMedia = AIGeneratedMedia::find($ai_media_id);
            if (!$aiMedia) {
                throw new Exception('Video không tồn tại.');
            }
            $video_url = $aiMedia->s3_url;
            $video_url = Helpers::preSignedS3Url($video_url);

            // Initial API call to start transcription
            $response = Http::timeout(120)->withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
                'Content-Type' => 'application/json',
            ])->post('https://api.replicate.com/v1/predictions', [
                'version' => '18a45ff0d95feb4449d192bbdc06b4a6df168fa33def76dfc51b78ae224b599b',
                'input' => [
                    'video_file_input' => $video_url,
                    'font' => 'Arial/Arial_Bold.ttf'
                ]
            ]);

            $responseData = $response->json();
            Log::info('Initial transcription response:', ['response' => $responseData]);

            if (!isset($responseData['id'])) {
                throw new Exception('Failed to start transcription process');
            }

            // Poll for results
            $maxAttempts = 30; // Maximum number of polling attempts
            $attempt = 0;
            $output = null;

            while ($attempt < $maxAttempts) {
                $pollResponse = Http::timeout(10)->withHeaders([
                    'Authorization' => "Bearer {$this->apiToken}",
                    'Content-Type' => 'application/json',
                ])->get("https://api.replicate.com/v1/predictions/{$responseData['id']}");

                $pollData = $pollResponse->json();
                Log::info('Poll response:', ['attempt' => $attempt, 'response' => $pollData]);

                if (isset($pollData['output'])) {
                    $output = $pollData['output'];
                    break;
                }

                if (isset($pollData['status']) && $pollData['status'] === 'failed') {
                    throw new Exception('Transcription process failed');
                }

                $attempt++;
                sleep(2); // Wait 2 seconds between polls
            }

            if (!$output) {
                throw new Exception('Transcription timed out');
            }

            $transcription_url = $output[0];
            $ssml_text = $output[1];
            $video_path = null;
            $ssml_path = null;
            do {
                try {
                    $videoContent = null;
                    $ssmlContent = null;

                    $videoContent = file_get_contents($transcription_url);
                    $ssmlContent = file_get_contents($ssml_text);

                    $tempFile = tempnam(sys_get_temp_dir(), 'diff_');
                    $ssmlTempFile = tempnam(sys_get_temp_dir(), 'ssml_');

                    file_put_contents($tempFile, $videoContent);
                    file_put_contents($ssmlTempFile, $ssmlContent);

                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempFile,
                        'video_template.mp4',
                        mime_content_type($tempFile),
                        null,
                        true
                    );

                    $ssmlUploadedFile = new \Illuminate\Http\UploadedFile(
                        $ssmlTempFile,
                        'ssml_template.json',
                        mime_content_type($ssmlTempFile),
                        null,
                        true
                    );
                    Log::info('generateVideo:', ['uploadedFile' => $uploadedFile]);
                    $video_path = $uploadedFile->store('videos', 'public');
                    $ssml_path = $ssmlUploadedFile->store('ssml', 'public');

                    Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
                    Storage::disk('s3')->put($ssml_path, Storage::disk('public')->get($ssml_path));
                } catch (Exception $e) {
                    Log::error("createVideoWithTranscription error: " . $e->getMessage());
                    return [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } while (empty($video_path) || empty($ssml_path));

            if (empty($video_path) || empty($ssml_path)) {
                throw new Exception('Failed to upload video to disk');
            }
            $aiMedia->update([
                'transcription_url' => $video_path,
                'ssml_text' => $ssml_path
            ]);
            return [
                'video' => Helpers::preSignedS3Url($video_path)
            ];
        } catch (Exception $e) {
            Log::error("createVideoWithTranscription error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateVideoWithTranscriptionWithSegmind($user, $data)
    {
        try {
            $aiMedia = "";
            $s3Url = "";
            if(isset($data['ai_media_id'])) {
                $ai_media_id = $data['ai_media_id'];
                $aiMedia = AIGeneratedMedia::find($ai_media_id);
                $s3Url = $aiMedia->s3_url;
                if (!$aiMedia) {
                    throw new Exception('Video không tồn tại.');
                }    
            } else {
                $lip_sync_id = $data['lip_sync_id'];
                $aiMedia = Lipsync::where("lip_sync_id", $lip_sync_id)->first();
                $s3Url =  $aiMedia->result;
                if (!$aiMedia) {
                    throw new Exception('Video không tồn tại.');
                }
            }
          
            // Get pre-signed URL for the video
            $video_url = Helpers::preSignedS3Url($s3Url);
      
            Log::info('generateVideoWithTranscriptionWithSegmind:', ['video_url' => $video_url]);


            $payload = [
                'MaxChars' => 10,
                'bg_blur' => false,  // Changed from true to false
                'bg_color' => "None",  // Changed from null to "None"
                'color' => "white",
                'font' => "Arial/Arial_Bold.ttf",  // Changed font
                'fontsize' => 7,  // Changed from 7.0 to 7
                'highlight_color' => "yellow",
                'kerning' => -2,  // Changed from -2.0 to -2
                'opacity' => 0,  // Changed from 1.0 to 0
                'right_to_left' => false,  // Changed from true to false
                'stroke_color' => "black",
                'stroke_width' => 2,  // Changed from 2.0 to 2
                'subs_position' => "bottom75",
                'input_video' => $video_url
            ];

            Log::info('Segmind payload:', ['payload' => $payload]);
            $response = Http::timeout(120)
                ->withHeaders([
                    'x-api-key' => env("SEGMIND_API_KEY"),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.segmind.com/v1/video-captioner', $payload);

            if (!isset($response)) {
                throw new Exception('Failed to generate video captions');
            }

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
            $aiMedia->update([
                'transcription_url' => $video_path
            ]);

            return [
                'success' => true,
                'video' => Helpers::preSignedS3Url($video_path)
            ];
        } catch (Exception $e) {
            Log::error("generateVideoWithTranscriptionWithSegmind error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function mergeVideo($ids = [], $aiMediaId, $audioPath, $imagePath, $audioDescription, $ratio) {
        $aiMediaUpdate = AIGeneratedMedia::where("id", $aiMediaId)->first();
        if(!$aiMediaUpdate) {
            return;
        }
        $dataUpdate = [];
        if(count($ids) > 0) {
            $videoURLs = [];
            foreach($ids as $value) {
                $id = 0;
                $type = "ai_media";
                if(isset($value["id"])) {
                    $id = $value["id"];
                    $type = $value["type"];
                } else {
                    $id = $value;
                }
                if($type == "video") {
                    $type = "ai_media";
                }
                if($type == "ai_media") {
                    $aiMedia = AIGeneratedMedia::where("id", $id)->first();
                    if(!$aiMedia) {
                        $aiMediaUpdate->update(['error_msg' => "Video không tồn tại, vui lòng kiểm tra lại."]);
                        return;
                    }
                    $s3_url = $aiMedia->s3_url;
                    if($aiMedia->transcription_url) {
                        $s3_url = $aiMedia->transcription_url;
                    }
                    if($aiMedia->s3_url_video_result) { 
                        $s3_url = $aiMedia->s3_url_video_result;
                    }
                    $videoURLs[] = Helpers::preSignedS3Url($s3_url);
                } else if ($type == "lipsync") {
                    $lipsync = Lipsync::where("id", $id)->first();
                    if(!$lipsync) {
                        $aiMediaUpdate->update(['error_msg' => "Video không tồn tại, vui lòng kiểm tra lại."]);
                        return;
                    }
                    $s3_url = $lipsync->result;
                    if($lipsync->transcription_url) {
                        $s3_url = $lipsync->transcription_url;
                    }
                    $videoURLs[] = Helpers::preSignedS3Url($s3_url);
                } else if ($type == "mc_virtual") {
                    $mcVirtual = McVirtual::where("id", $id)->first();
                    if(!$mcVirtual) {
                        $aiMediaUpdate->update(['error_msg' => "Video không tồn tại, vui lòng kiểm tra lại."]);
                        return;
                    }
                    $s3_url = $mcVirtual->result_url;
                    if($mcVirtual->transcription_url) {
                        $s3_url = $mcVirtual->transcription_url;
                    }
                    $videoURLs[] = Helpers::preSignedS3Url($s3_url);
                }
            }
           
            $ListVideo = [];
            $textVideo = "";
            $textAudio = "";
            $textFileVideo =  'videos/'.Str::uuid().'_list.txt';
            $textFileAudio =  'videos/audio_'.Str::uuid().'_list.txt';
            $videoResizeFinal = 'videos/final_video_'.Str::uuid().'.mp4';
            $audioFinal = 'videos/final_audio_'.Str::uuid().'.mp3';
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
           
            $ListVideo[] = $audioFinal; 
            $ListVideo[] = $textVideo;
            file_put_contents( storage_path('app/public/'.$textFileVideo), $textVideo);
            file_put_contents( storage_path('app/public/'.$textFileAudio), $textAudio);
             
            FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileVideo) , storage_path('app/public/'.$videoResizeFinal));
            FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileAudio) , storage_path('app/public/'.$audioFinal));

            if(!empty($audioDescription)) {
                $audioNew = 'videos/final_audio_'.Str::uuid().'.mp3';
                $audioCut = 'videos/final_audio_'.Str::uuid().'.mp3';
                file_put_contents(storage_path('app/public/' .$audioNew), file_get_contents($audioDescription));
                FFmpegHelper::cutAudioV2(storage_path('app/public/' .$audioNew), storage_path('app/public/' .$audioCut), 0, $totalDuration);
                $audioFinal = $audioCut;
            }
            $dataUpdate['duration'] = $totalDuration;
            $outputPath = 'videos/video_'.Str::uuid().'.mp4';
            $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
            $output_path = 'final_videos/' . Str::uuid() . '.mp4';
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(storage_path('app/public/'.$videoResizeFinal),  storage_path('app/public/'.$audioFinal), storage_path('app/public/'.$outputPath),  storage_path('app/public/'.$thumbnailPath));
            $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
            Storage::disk('s3')->put($output_path, $videoContent);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
            }
            $dataUpdate['s3_url'] = $output_path;
            $dataUpdate['s3_url_video_result'] = $output_path;
            $urlVideoAudio = "";
            if(!empty($audioPath)) {
                $audioUrlS3 = Helpers::preSignedS3Url($audioPath);
                file_put_contents(storage_path('app/public/' .$audioPath), file_get_contents($audioUrlS3));

                $videoMerge = 'videos/video_'.Str::uuid().'.mp4';
                $audioCut = 'videos/video_'.Str::uuid().'.mp3';
                FFmpegHelper::cutAudioV2(storage_path('app/public/'.$audioPath), storage_path('app/public/'.$audioCut), 0, $totalDuration);
                $ListVideo[] = $audioCut;
                $ListVideo[] = $videoMerge;
                $vol = "1.0";
                if(!empty($audioDescription)) { 
                    $vol = "0.2";
                }
                FFmpegHelper::mergerVideoAudioV2(storage_path('app/public/'.$outputPath),  storage_path('app/public/'.$audioCut), storage_path('app/public/'.$videoMerge),NULL, $vol);
                $videoContent = file_get_contents(storage_path('app/public/' . $videoMerge));
                Storage::disk('s3')->put($output_path, $videoContent);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                }
                $dataUpdate['s3_url_video_audio'] = $output_path;
                $dataUpdate['s3_url_video_result'] = $output_path;
                $urlVideoAudio = $videoMerge;
            } 

            if(!empty($imagePath)) {
                $imagePathS3 = Helpers::preSignedS3Url($imagePath);
                file_put_contents(storage_path('app/public/' .$imagePath), file_get_contents($imagePathS3));
                $videoMerge = 'videos/video_'.Str::uuid().'.mp4';
                $ListVideo[] = $videoMerge;
                FFmpegHelper::mergeImageVideo(
                    storage_path('app/public/' . $outputPath),
                    storage_path('app/public/' . $imagePath),
                    storage_path('app/public/' . $videoMerge)
                ); 
                $videoContent = file_get_contents(storage_path('app/public/' . $videoMerge));
                Storage::disk('s3')->put($output_path, $videoContent);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                }
                $dataUpdate['s3_url_video_image'] = $output_path;
                $dataUpdate['s3_url_video_result'] = $output_path;
            }  

            if(!empty($audioPath) && !empty($imagePath)) {
                $videoMerge = 'videos/video_'.Str::uuid().'.mp4';
                $ListVideo[] = $videoMerge;
                $outputPath = $urlVideoAudio;
                FFmpegHelper::mergeImageVideo(
                    storage_path('app/public/' . $outputPath),
                    storage_path('app/public/' . $imagePath),
                    storage_path('app/public/' . $videoMerge)
                ); 
                $videoContent = file_get_contents(storage_path('app/public/' . $videoMerge));
                Storage::disk('s3')->put($output_path, $videoContent);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                }
                $dataUpdate['s3_url_video_result'] = $output_path;
            }
            $dataUpdate["thumbnail"] =  $thumbnailPath;
            $aiMediaUpdate->update($dataUpdate);
            $ListVideo[] = $videoResizeFinal;
            $ListVideo[] = $thumbnailPath;
            $ListVideo[] = $outputPath;
            $this->cleanupFiles($ListVideo);
        }
    }

    public function getAllVideo($userId, $isImage = true, $queryType = "") {
        $result = AIGeneratedMedia::where("user_id", $userId)->where("type", "video")->where("s3_url", "!=", "");
        if(empty($queryType)) {
            if(!$isImage) {
                $result = $result->whereNotIn("model", ['merge-video-image', 'merge-video-all', 'img-video']);
            } else {
                $result = $result->whereIn("model", ['merge-video-image', 'img-video']);
            }
        } 
        return $result->orderBy('created_at', 'desc')->get();
    }

    public function mergeImageVideo($aiMediaId, $imageUrl) {
        $aiMedia = AIGeneratedMedia::find($aiMediaId);
        $urls = [];
        $dataUpdate = [];
        $dataUpdate["is_upload_image"] = true;
        $video_url = $aiMedia->s3_url;
        $video_url_2 = "";

        if($aiMedia->transcription_url) {
            $video_url = $aiMedia->transcription_url;
        }
        if($aiMedia->s3_url_video_audio) {
            $video_url_2 = $aiMedia->s3_url_video_audio;
        }
        $imageUrlS3 = Helpers::preSignedS3Url($imageUrl);
        file_put_contents(storage_path('app/public/' .$imageUrl), file_get_contents($imageUrlS3));
        for($i = 0; $i < 2; $i++) {
            if($i == 1 && empty($video_url_2)) {
                continue;
            } else {
                if($i == 1 && !empty($video_url_2)) {
                    $video_url = $video_url_2;
                }
                $video_url = Helpers::preSignedS3Url($video_url);
   
                $video_path = 'videos/' . Str::uuid() . '.mp4';
                $urls[] = $video_path;
                file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
               
   
                $urls[] = $imageUrl;
                $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
                FFmpegHelper::mergeImageVideo(
                    storage_path('app/public/' . $video_path),
                    storage_path('app/public/' . $imageUrl),
                    storage_path('app/public/' . $videoPath2)
                ); 
                $urls[] = $videoPath2;
                if($aiMedia->ratio) {
                    $outputPath = 'videos/' . Str::uuid() . '.mp4';
                    FFmpegHelper::convertRatio(storage_path('app/public/' .$videoPath2), storage_path('app/public/' .$outputPath), $aiMedia->ratio);
                    $dataUpdate["s3_url_video_ratio"] = $outputPath;
                    Storage::disk('s3')->put($outputPath, Storage::disk('public')->get($outputPath));
                    $urls[] = $outputPath;
                }
                Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
               
                $dataUpdate["s3_url_video_result"] = $videoPath2;
                if($i == 0) {
                    $dataUpdate["s3_url_video_image"] = $videoPath2;
                }
            }
        }
        $this->cleanupFiles($urls);
        AIGeneratedMedia::where("id", $aiMediaId)->update($dataUpdate);
    }
}

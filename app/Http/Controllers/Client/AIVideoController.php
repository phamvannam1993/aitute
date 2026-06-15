<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\UsageLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\PictoryVideo;
use App\Services\ChatGPTService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use DevDojo\GoogleImageSearch\ImageSearch;
use App\Services\AIVideoService;
use App\Services\AIVideoServiceV2;
use App\Services\KlingService;
use App\Services\CreditHistoryService;
use App\Services\AIAssistantsService;
use App\Services\StorageService;
use App\Models\User;
use App\Models\SwapFace;
use App\Models\Lipsync;
use App\Models\McVirtual;
use App\Jobs\MergeVideo;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helpers;
use Exception;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Helper\FFmpegHelper;
use App\Jobs\MergeAudioVideo;
use App\Jobs\MergeAudioVideoLipsync;
use App\Jobs\ConvertFileToMp4;
use App\Jobs\MergeImageVideo;
use App\Jobs\ConvertRatio;

class AIVideoController extends Controller
{
    private $aIVideoService;
    protected $aiVideoServiceNew;
    private $aIAssistantsService;
    private $storageService;
    private $klingService;
    private $creditHistoryService;
    private $chatGPTService;

    public function __construct(
        AIAssistantsService $aIAssistantsService,
        StorageService $storageService,
        AIVideoService $aIVideoService,
        AIVideoServiceV2 $aiVideoServiceNew,
        CreditHistoryService $creditHistoryService,
        KlingService $klingService,
        ChatGPTService $chatGPTService,
    ) {
        $this->aIVideoService = new AIVideoService();
        $this->aIAssistantsService = $aIAssistantsService;
        $this->storageService = $storageService;
        $this->klingService = $klingService;
        $this->creditHistoryService = $creditHistoryService;
        $this->chatGPTService = $chatGPTService;
        $this->aiVideoServiceNew = $aiVideoServiceNew;
    }

    public function generateVideoAudio(Request $request)
    {

        try {
            $validated = $request->validate([
                'video_description' => 'required|string',
                'model' => 'required|string|max:255',
                'duration' => 'required|string|max:255',
                'ratio' => 'required|max:255',
                'audio_file' => 'file',
                'language' => 'required|string|max:255',
            ]);

            $user = auth()->user();
            $audio_file = $request->file('audio_file');
            $image_file = $request->file('image_file');

            $payload = [
                'model' => $validated['model'],
                'video_description' => $validated['video_description'],
                'duration' => $validated['duration'],
                'ratio' => $validated['ratio'],
                'language' => $validated['language'],
                'audio_url' => $request["audio_url"]
            ];

            if ($audio_file) {
                if (!file_exists(storage_path('app/public/audio'))) {
                    mkdir(storage_path('app/public/audio'), 0777, true);
                }
                $audio_path = $audio_file->store('audio', 'public');
                $validated['audio_path'] = $audio_path;
                $payload['audio_path'] = $audio_path;
            }

            if (isset($request["s3_url"]) && !empty($request["s3_url"])) {
                $payload['promptImage'] = $request["s3_url"];
            } else if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
                $promptImage = Helpers::preSignedS3Url($image_file);
                $payload['promptImage'] = $promptImage;
            }
            // Thay vì truyền $user trực tiếp, chỉ truyền user_id
            $user_id = Auth::user()->id;
            $res = $this->aiVideoServiceNew->generateVideo($payload, $user_id);
            return response()->json($res);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error generateVideoAudioe: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getVideoURL($taskId, Request $request)
    {
        $audio_path =  $request->audio_path_cut;
        return $this->klingService->getVideoUrl($taskId, $audio_path);
    }

    public function mergeAudioVideoQueue(Request $request)
    {
        $audio_file = $request->file('audio_file');
        $aiAudioId = $request->ai_media_id;
        if ($audio_file) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_path = $audio_file->store('audio', 'public');
            $this->aIVideoService->updateById($aiAudioId, ["is_upload_audio" => false]);
            Storage::disk('s3')->put($audio_path, file_get_contents(storage_path('app/public/'.$audio_path)));
            $this->aIVideoService->mergeAudioVideo($aiAudioId, $audio_path);
            $this->cleanupFiles([
                $audio_path,
            ]);
            return  response()->json(['message' => "Nhạc nền đang trong quá trình tạo", "success" => true]);
        } else {
            return response()->json(['message' => "audio không tồn tại", "success" => false], 400);
        }
    }

    public function mergeAudioVideoLipsyncQueue(Request $request)
    {
        $audio_file = $request->file('audio_file');
        $lipSyncId = $request->lip_sync_id;
        if ($audio_file) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_path = $audio_file->store('audio', 'public');

            Storage::disk('s3')->put($audio_path, file_get_contents(storage_path('app/public/'.$audio_path)));
            // $job = (new MergeAudioVideoLipsync($lipSyncId, $audio_path))->onQueue('my-ai-video');
            // dispatch($job);
            // $jobRecord = DB::table('jobs')->latest('id')->first();
            // $jobId = $jobRecord->id;
            $this->aIVideoService->mergeAudioVideoLipsync($lipSyncId, $audio_path);
            $this->cleanupFiles([
                $audio_path,
            ]);
            return  response()->json(['message' => "Nhạc nền đang trong quá trình tạo", "job_id" => "", "success" => true]);
        } else {
            return response()->json(['message' => "audio không tồn tại", "success" => false], 400);
        }
    }


    public function getVideoURLRun($aiMediaId, Request $request)
    {
        $audio_path_cut = $request->audio_path_cut;
        return $this->aIVideoService->getVideoURL($aiMediaId, $audio_path_cut);
    }

    public function mergeVideoAudio(Request $request)
    {
        $user = Auth::user();
        $file_video = $request->file('video_file');
        $file_audio = $request->file('audio_file');

        $response = $this->aIVideoService->mergeVideoAudio($file_video, $file_audio, $user);
        return response()->json($response);
    }

    public function index(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIVideo/Index', ['ai_assistant' => $ai_assistant]);
    }

    public function img2Video(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        $imageUrl = $request->imageUrl;
        return Inertia::render('Client/AIVideo/ImgToVideo', ['ai_assistant' => $ai_assistant, 'imageUrl' => $imageUrl]);
    }

    public function history(Request $request)
    {
        try {
            $user = Auth::user();
            $list = $this->aIVideoService->getHistories($user, false);
            $historyList = $this->aIVideoService->getHistoriesWithoutPaginate($user);
            $type = $request["type"];
            // save danh sách vào session
            $request->session()->put('historyList', $historyList);
            return Inertia::render('Client/AIVideo/History', ['list' => $list, 'typeQuery' => $type]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ApiGetHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $list = $this->aIVideoService->getHistories($user, $request->is_merge, $request->is_image, $request->type_query);
            $historyList = $this->aIVideoService->getHistoriesWithoutPaginate($user);

            // save danh sách vào session
            $request->session()->put('historyList', $historyList);
            return response()->json(["list" => $list, "success" => true]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ImgToVideoHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $list = $this->aIVideoService->getImgToVideoHistory();
            $historyImg2VideoList = $this->aIVideoService->getHistoriesImg2Video($user);
            // save danh sách vào session
            $request->session()->put('historyImg2VideoList', $historyImg2VideoList);

            return Inertia::render('Client/AIVideo/ImgToVideoHistory', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getListImgToVideo(Request $request)
    {
        try {
            $per_page = $request->per_page;
            $list = $this->aIVideoService->getImgToVideoHistory($per_page);

            return response()->json(['data' => $list], 200);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'type' => 'required|string',
            's3_url' => 'required|string',
            'duration' => 'required|integer|in:5,10',
            'model' => 'required|string',
            'thumbnail' => 'required|string',
        ]);

        try {
            $user_id =  Auth::user()->id;

            $type = $request->type;
            $s3_url = $request->s3_url;
            $duration = $request->duration;
            $thumbnail = $request->thumbnail;
            $model = $request->model;

            $data = [
                'user_id' => $user_id,
                'description' => "Video được tạo ra từ hình ảnh",
                'style' => "",
                'artist' => "",
                'type' => $type,
                's3_url' => $s3_url,
                'duration' => $duration,
                'thumbnail' => $thumbnail,
                'model' => $model,
            ];
            $record = $this->aIVideoService->storeMedia($data);

            return response()->json([
                'success' => true,
                'response' => $record->toArray(),  // hoặc $record->toJson()
                'credits' => $user->credit ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error save data history_img2video:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình lưu '
            ]);
        }
    }

    public function historyDetail(Request $request, $id)
    {
        $user = Auth::user();
        $record = $this->aIVideoService->getHistoryDetail($user, $id);
        if($record) {
            $isImage = false;
            $isMerge = false;
            if($record->model == "img-video") {
                $isImage = true;
            }
            if($record->model == "merge-video" || $record->model == "merge-video-all" || $record->model == "merge-video-image") {
                $isMerge = true;
            }
            $historyList = $this->aIVideoService->getHistoriesWithoutPaginate($user, $isImage, $isMerge, $record->model);
            // Convert Collection toArray
            if ($historyList instanceof \Illuminate\Database\Eloquent\Collection) {
                $historyList = $historyList->toArray();
            }

            // Filter element duplicate with $record
            $filteredHistoryList = array_filter($historyList, function ($item) use ($record) {
                return $item['id'] !== $record['id']; // Giả sử $record có trường 'id'
            });

            // Without duplicate id => get 3 (0,3)
            if (empty($filteredHistoryList)) {
                $filteredHistoryList = array_slice($historyList, 0, 3);
            } else {
                $filteredHistoryList = array_slice($filteredHistoryList, 0, 3);
            }
            if($record->transcription_url) {
                $record->s3_url = $record->transcription_url;
            }
            if($record->s3_url_video_audio) {
                $record->s3_url = $record->s3_url_video_audio;
            }
            if($record->s3_url_video_image) {
                $record->s3_url = $record->s3_url_video_image;
            }
            if($record->s3_url_video_result) {
                $record->s3_url = $record->s3_url_video_result;
            }
            if($record->s3_url_video_ratio) {
                $record->s3_url = $record->s3_url_video_ratio;
            }

            $record->s3_url = Helpers::updatePresignS3($record->s3_url);
            $params = ['record' => $record, 'list' => $filteredHistoryList];

            return Inertia::render('Client/AIVideo/HistoryDetail', $params);
        } else {
            return redirect()->back();
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $record = $this->aIVideoService->getHistoryDetail($user, $id);
        if($record) {
            $url = "";
            if($record->transcription_url) {
                $record->s3_url = $record->transcription_url;
            }
            if($record->s3_url_video_audio) {
                $record->s3_url = $record->s3_url_video_audio;
            }
            if($record->s3_url_video_image) {
                $record->s3_url = $record->s3_url_video_image;
            }
            if($record->s3_url_video_result) {
                $record->s3_url = $record->s3_url_video_result;
            }
            if($record->s3_url_video_ratio) {
                $record->s3_url = $record->s3_url_video_ratio;
            }
            if($record->s3_url) {
                $url = Helpers::preSignedS3Url($record->s3_url);
            }
            return response()->json(["success" => true, "s3_url" => $url, 'id' => $id,  'error_msg' =>  $record->error_msg, 'data' => $record, 'is_upload_audio' => $record->is_upload_audio, 'is_upload_image' => $record->is_upload_image, 'is_convert_ratio' => $record->is_convert_ratio, 'is_upload_image' => $record->is_upload_image]);
        } else {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"]);
        }
    }

    public function detailLipsync($id)
    {
        $record = Lipsync::where("lip_sync_id", $id)->first();
        $url = "";
        if($record->s3_url_video_audio) {
            $record->s3_url = $record->s3_url_video_audio;
        }
        if($record->s3_url) {
            $url = Helpers::preSignedS3Url($record->s3_url);
        }
        if($record) {
            return response()->json(["success" => true, "url" => $url, 'id' => $id, 'data' => $record, 'is_upload_audio' => $record->is_upload_audio]);
        } else {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"], 400);
        }
    }


    public function ImgToVideoHistoryDetail(Request $request, $id)
    {
        $user = Auth::user();
        $record = $this->aIVideoService->getHistoryDetail($user, $id);
        $historyList = $request->session()->get('historyImg2VideoList', []);

        // Convert Collection toArray
        if ($historyList instanceof \Illuminate\Database\Eloquent\Collection) {
            $historyList = $historyList->toArray();
        }

        // Filter element duplicate with $record
        $filteredHistoryList = array_filter($historyList, function ($item) use ($record) {
            return $item['id'] !== $record['id']; // Giả sử $record có trường 'id'
        });

        // Without duplicate id => get 3 (0,3)
        if (empty($filteredHistoryList)) {
            $filteredHistoryList = array_slice($historyList, 0, 3);
        } else {
            $filteredHistoryList = array_slice($filteredHistoryList, 0, 3);
        }

        $params = ['record' => $record, 'list' => $filteredHistoryList];
        return Inertia::render('Client/AIVideo/ImgToVideoHistoryDetail', $params);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->aIVideoService->deleteFileById($id);
        return response()->json($response);
    }
    public function generateImg2Video(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'imageUrl' => 'required|url',
                'duration' => 'required|integer|in:5,10',
                'resolution' => 'required|string|in:16:9,9:16,1:1',
            ]);

            try {
                Log::info('Calling Runway API', [
                    'imageUrl' => $request->imageUrl,
                    'duration' => $request->duration,
                    'resolution' => $request->resolution
                ]);
                $promptText = $request->video_description;
                if(empty($promptText)) {
                    $promptText = "Mô tả video";
                }
                $promptImage = $request->imageUrl;
                if($request->imageUrl2) {
                    $promptImage = [
                        [
                            "uri" => $request->imageUrl,
                            "position" => "first"
                        ],
                        [
                            "uri" => $request->imageUrl2,
                            "position" => "last"
                        ]
                    ];
                }

                $requestData = [
                    'promptText' => $promptText,
                    'promptImage' => $promptImage,
                    "model" => "gen3a_turbo",
                    "watermark" => false,
                    'seed' => rand(1, 99999),
                    'duration' => (int)$request->duration,
                    'ratio' => $request->resolution
                ];
                // Initial API call to create the task
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('runway.api_key'),
                    'Content-Type' => 'application/json',
                    'X-Runway-Version' => '2024-09-13'
                ])->post('https://api.dev.runwayml.com/v1/image_to_video', $requestData);

                if (!$response->successful()) {
                    Log::error('Runway API error response', [
                        'statusCode' => $response->status(),
                        'response' => $response->json(),
                        'body' => $response->body()
                    ]);

                    return response()->json([
                        'error' => 'Failed to generate video',
                        'details' => $response->json()
                    ], 500);
                }

                $taskId = $response->json()['id'];
                Log::info('Successfully created task', ['taskId' => $taskId]);

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
                        Log::error('Failed to check task status', [
                            'taskId' => $taskId,
                            'statusCode' => $statusResponse->status(),
                            'response' => $statusResponse->json()
                        ]);
                        throw new \Exception('Failed to check task status');
                    }

                    $status = $statusResponse->json()['status'];
                    Log::info('Task status check', [
                        'taskId' => $taskId,
                        'status' => $status,
                        'attempt' => $attempt + 1
                    ]);

                    if ($status === 'SUCCEEDED') {
                        $output = $statusResponse->json()['output'][0];
                        $imageContent = file_get_contents($output);
                        if ($imageContent === false) {
                            Log::error('Không thể tải hình ảnh từ URL.');
                        }

                        $filename = 'image_to_video/' . uniqid('video_', true) . '.mp4';
                        Storage::disk('s3')->put($filename, $imageContent);

                        $s3Url = Storage::disk('s3')->url($filename);
                        Log::info('Video generation completed successfully', [
                            'taskId' => $taskId,
                            'output' => $s3Url
                        ]);
                        $url = Helpers::preSignedS3Url($filename);
                        // Trừ tiền credit user
                        Auth::user()->chargeCredit('image_to_video', null, null, null, null, 8, 8);
                        return response()->json([
                            'success' => true,
                            'url' => $url,
                            'path' => $filename,
                            'thumbnail' => Helpers::extractRelativePathFromSignedS3Url($request->imageUrl),
                        ]);
                    }

                    if ($status === 'FAILED') {
                        throw new \Exception('Task failed: ' . ($statusResponse->json()['error'] ?? 'Unknown error'));
                    }

                    $attempt++;
                    if ($attempt < $maxAttempts) {
                        sleep($pollInterval);
                    }
                }

                throw new \Exception('Task timed out after ' . ($maxAttempts * $pollInterval) . ' seconds');
            } catch (\Exception $e) {
                Log::error('Error in video generation process', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Failed to generate video: ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error in video generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate video',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchImg(Request $request)
    {
        try {
            $keyword = $request->string('keyword');
            if (!$keyword) {
                return null;
            }

            $colorType = $request->query('color_type', 'color');
            $page = $request->integer('page', 1);
            $perPage = $request->integer('per_page', 10);
            $postFix = match ($colorType) {
                'trans', 'gray' => ' png',
                default => ''
            };
            $query = $keyword . $postFix; // Default to 'apple' if no query provided
            $parameters = [
                'start' => ($page - 1) * $perPage + 1, // start from the 1st results,
                'num' => $perPage, // number of results to get, 10 is maximum and also default value,
                'imgColorType' => $colorType, // color, gray, mono, trans
            ];
            $apiKey = config('common.google_search_api_key');
            $cx = config('common.google_search_engine_id');
            ImageSearch::config()->apiKey($apiKey);
            ImageSearch::config()->cx($cx);

            $results = ImageSearch::search($query, $parameters);

            return response()->json($results);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function faceSwap(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIVideo/FaceSwap/Index', ['ai_assistant' => $ai_assistant]);
    }

    public function generateSwapFaceVideo(Request $request)
    {
        try {
            // Prepare API request payload
            $payload = [
                'source_img' => $request['img'],
                'video_input' => $request['video'],
                'face_restore' =>  true,
                'input_faces_index' => 0,
                'source_faces_index' => 0,
                'face_restore_visibility' =>  1,
                'codeformer_weight' => 0.95,
                'detect_gender_input' => 'no',
                'detect_gender_source' => 'no',
                'frame_load_cap' => 115,
                'base_64' => false
            ];

            Log::info('Segmind API request', ['payload' => $payload]);

            // Make API request
            $response = Http::withHeaders([
                'x-api-key' => config('segmind.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'video/*'
            ])
                ->timeout(1200)
                ->post('https://api.segmind.com/v1/videofaceswap', $payload);

            Log::info('Segmind API response', [
                'status' => $response->status(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');

                if (!str_contains(strtolower($contentType), 'video/')) {
                    throw new \Exception('Expected video response, got: ' . $contentType);
                }

                // Generate unique filename
                $filename = 'videos/' . uniqid('video_', true) . '.mp4';

                // Upload to S3
                Storage::disk('s3')->put($filename, $response->body());

                // Get S3 URL
                $s3Url = Storage::disk('s3')->url($filename);
                Log::info('S3 url generate video: ' . $s3Url);
                $url = Helpers::preSignedS3Url($filename);

                $swapFace = SwapFace::create([
                    'user_id' => auth('web')->id(),
                    'img' => $request['img'],
                    'video' => $request['video'],
                    'result' => $filename  // S3 path
                ]);

                return response()->json([
                    'success' => true,
                    'url' => $url,
                    // 'record' => $record
                ]);
            }

            // Handle error response
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? 'Face swap process failed';

            Log::error('Segmind API error', [
                'status' => $response->status(),
                'error' => $errorBody,
                'headers' => $response->headers()
            ]);

            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], $response->status());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Video face swap error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function historyFaceSwap(Request $request)
    {
        try {
            $user = Auth::user();
            $list = $this->aIVideoService->getHistoriesSwapFace($user);
            return Inertia::render('Client/AIVideo/FaceSwap/History', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getListFaceSwap(Request $request)
    {
        $user = Auth::user();
        $record = $this->aIVideoService->getHistoryDetail($user, $id);
        $historyList = $request->session()->get('historyImg2VideoList', []);

        // Convert Collection toArray
        if ($historyList instanceof \Illuminate\Database\Eloquent\Collection) {
            $historyList = $historyList->toArray();
        }

        // Filter element duplicate with $record
        $filteredHistoryList = array_filter($historyList, function ($item) use ($record) {
            return $item['id'] !== $record['id']; // Giả sử $record có trường 'id'
        });

        // Without duplicate id => get 3 (0,3)
        if (empty($filteredHistoryList)) {
            $filteredHistoryList = array_slice($historyList, 0, 3);
        } else {
            $filteredHistoryList = array_slice($filteredHistoryList, 0, 3);
        }

        $params = ['record' => $record, 'list' => $filteredHistoryList];
        return Inertia::render('Client/AIVideo/ImgToVideoHistoryDetail', $params);
    }

    public function deleteFaceswap(Request $request)
    {
        $id = $request->id;

        try {
            $message = SwapFace::where('id', $id);
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


    public function lipsync(Request $request): Response
    {
        $videoUrl = $request->videoUrl;
        return Inertia::render('Client/Lipsync/Index', ['videoUrl' => $videoUrl]);
    }

    public function generateLipSync(Request $request)
    {
        try {
            $audioUrl = $request->audioUrl;
            $videoUrl = $request->videoUrl;
            $duration = $request->duration;

            $payload = [
                'model' => config('sync.model'),
                'input' => [
                    [
                        'type' => 'video',
                        'url' => $videoUrl
                    ],
                    [
                        'type' => 'audio',
                        'url' => $audioUrl
                    ]
                ]
            ];
            $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => config('sync.api_key')
            ])->post('https://api.sync.so/v2/generate', $payload);

            $syncId = $response->json('id');
            // $syncId = "d1871bf8-8005-4b47-ae64-cb6fb040aa34";
            if (!$syncId) {
                throw new \Exception('Không tìm thấy ID trong phản hồi của Sync API.');
            }

            // Lưu vào DB ngay khi tạo request
            $lipSync = Lipsync::create([
                'user_id' => auth()->id(),
                'audio' => $audioUrl,
                'duration' => $duration,
                'status' => 'PENDING',
                'video' => $videoUrl,
                'result' => "",
                'lip_sync_id' => $syncId
            ]);

            return response()->json([
                'id' => $syncId,
                'lipSync' => $lipSync,
                'message' => 'Đã tạo yêu cầu lipsync thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in createLipSync: ' . $e->getMessage());
            return response()->json([
                'error' => 'Lỗi khi tạo yêu cầu Sync API: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTotalDuration(Request $request)
    {
        try {
            $audioUrl = $request->audioUrl;
            $videoUrl = $request->videoUrl;
            $videoPath =  'video_' . Str::uuid() . '.mp4';
            $audioPath =  'audio_' . Str::uuid() . '.mp3';
            $extension = pathinfo($audioUrl, PATHINFO_EXTENSION);
            try {
                if (str_contains($extension, "webm") || str_contains($extension, "wav") || str_contains($extension, "mp4") || str_contains($extension, "m4a")) {
                    if(str_contains($extension, "webm")) {
                        $extension = "webm";
                    }
                    if(str_contains($extension, "wav")) {
                        $extension = "wav";
                    }
                    if(str_contains($extension, "m4a")) {
                        $extension = "m4a";
                    }
                    if(str_contains($extension, "mp4")) {
                        $extension = "mp4";
                    }
                    $audioPath2 =  'audio_'.Str::uuid() . '.'.$extension;
                    file_put_contents(storage_path('app/public/'.$audioPath2), file_get_contents($audioUrl));
                    FFmpegHelper::convertContentToMp3(storage_path('app/public/'.$audioPath2), storage_path('app/public/'.$audioPath));
                } else {
                    file_put_contents(storage_path('app/public/' . $audioPath), file_get_contents($audioUrl));
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Error converting audio to mp3'], 500);
            }
            file_put_contents(storage_path('app/public/' . $videoPath), file_get_contents($videoUrl));
            $audioContent = file_get_contents(storage_path('app/public/' . $audioPath));
            Storage::disk('s3')->put($audioPath, $audioContent);
            $audioS3 = Helpers::preSignedS3Url($audioPath);
            $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $videoPath));
            $audioDuration = FFmpegHelper::getAudioDuration(storage_path('app/public/' . $audioPath));
            if($audioDuration < 1) {
                $this->cleanupFiles([
                    $videoPath,
                    $audioPath
                ]);
                return response()->json(["success" => false, "message" => "Nội dung chuyển đổi âm thanh không hợp lệ"]);
            }
            $duration = $videoDuration;
            if ($audioDuration > $videoDuration) {
                $duration = $audioDuration;
            }
            $this->cleanupFiles([
                $videoPath,
                $audioPath
            ]);
            return response([
                "success" => true,
                'videoDuration' => (int)$videoDuration,
                'audioDuration' => (int)$audioDuration,
                "duration" => (int)$duration,
                "audioS3" => $audioS3
            ]);
        } catch (\Exception $ex) {
            return response([
                "success" => false,
                "message" => $ex->getMessage()
            ]);
        }
    }

    public function convertFileToMp4(Request $request) {
        $video_file = $request->file('video_file');
        $isMp4 = $request["is_mp4"];
        if ($video_file) {
            if (!file_exists(storage_path('app/public/videos'))) {
                mkdir(storage_path('app/public/videos'), 0777, true);
            }
            $video_path = $video_file->store('videos', 'public');
            Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));
            $videoS3 = Helpers::preSignedS3Url($video_path);
            if($isMp4) {
                $this->cleanupFiles([$video_path]);
                return response()->json(['success' => true, 'video_s3' => $videoS3]);
            } else {
                $video_convert =  'videos/' . uniqid('video_', true) . '.mp4';
                $job = (new ConvertFileToMp4($videoS3, $video_convert))->onQueue('file-to-mp4');
                dispatch($job);
                $jobRecord = DB::table('jobs')->latest('id')->first();
                $uuid = "";
                if($jobRecord) {
                    $payload = $jobRecord->payload;
                    $payloadArr = json_decode($payload, true);
                    $uuid =  $payloadArr["uuid"];
                }
                $this->cleanupFiles([$video_path]);
                return response()->json(['success' => true, 'uuid' => $uuid, 'video_convert' => $video_convert]);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'File của bạn không hợp lệ']);
        }
    }

    public function getFileConvert(Request $request) {
        $video_convert = $request["video_convert"];
        $uuid_job = $request["uuid_job"];
        $videoS3 = Helpers::preSignedS3Url($video_convert);
        $item = DB::table("failed_jobs")->where("uuid", $uuid_job)->first();
        if($item) {
            return response()->json(['success' => false, 'message' => "Video của bạn lỗi. Vui lòng chọn video khác"]);
        }
        if(file_get_contents($videoS3)) {
            return response()->json(['success' => true, 'video_s3' => $videoS3]);
        } else {
            return response()->json(['success' => true, 'message' => "Video đang trong quá trình convert"]);
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
            }
        } catch (Exception $e) {
            Log::error("cleanupFiles: " . $e->getMessage());
        }
    }

    public function checkLipSyncStatus($id, Request $request)
    {
        try {
            // Tìm record trong DB
            $lipSync = Lipsync::where('lip_sync_id', $id)->first();

            // Kiểm tra trạng thái
            $statusResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => config('sync.api_key')
            ])->get("https://api.sync.so/v2/generate/{$id}");
            $status = $statusResponse->json('status');
            // Nếu hoàn thành, xử lý kết quả
            if ($status === 'COMPLETED') {
                $outputUrl = $statusResponse->json('outputUrl');

                // Tải và lưu video
                $videoContents = file_get_contents($outputUrl);
                $filename = 'videos/' . uniqid('video_', true) . '.mp4';

                // Lưu file cục bộ
                $localImagePath = storage_path("app/video/{$filename}");
                Storage::disk('local')->put("video/{$filename}", $videoContents);
                $duration = FFmpegHelper::getVideoDuration(storage_path("app/video/{$filename}"));
                $checkUpdate =  Lipsync::where("id", $lipSync->id)->first();
                if(!empty($checkUpdate->result)) {
                    return response()->json([
                        'url' => Helpers::preSignedS3Url($checkUpdate->result),
                        'duration' => (int)$duration,
                        'status' => $status,
                        'code' => 200,
                    ]);
                }
                $uploadedResultFile = new UploadedFile($localImagePath, $filename);
                $result = $this->storageService->putToS3($uploadedResultFile, folderName: 'Lipsync-src');
                Storage::disk('local')->delete("video/{$filename}");
                Auth::user()->chargeCredit('lipsync', 'lipsync', $lipSync['duration'] ? $lipSync['duration'] : 5, null, null, 16, 16);
                Lipsync::where("id", $lipSync->id)->update(["result" => $result['path'], 'status' => $status]);
                $s3Url = Helpers::preSignedS3Url($result['path']);
                $this->cleanupFiles([
                    $filename,
                    $localImagePath
                ]);
                return response()->json([
                    'url' => $s3Url,
                    'duration' => (int)$duration,
                    'status' => $status,
                    'code' => 200,
                ]);
            } else if ($status === 'FAILED') {
                Lipsync::where("id", $lipSync->id)->update(["result" => "", 'status' => $status]);
                return response()->json([
                    'status' => false,
                    'code' => 500,
                    'message' => 'Video của bạn tạo bị lỗi. Vui lòng thử lại.',
                ]);
            }

            // Trường hợp lỗi
            return response()->json([
                'message' => 'Quá trình lipsync không thành công.',
                'status' => $status,
                'code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'FAILED',
                'code' => 500,
                'message' => 'Hệ thống đang quá tải. Vui lòng thử lại sau',
            ]);
        }
    }

    public function deleteLipsync($id)
    {
        try {
            $lipsync = Lipsync::findOrFail($id);
            $lipsync->delete();
            return [
                'success' => true,
                'message' => 'Video đã được xóa thành công.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình xóa video.'
            ];
        }
    }

    public function historyLipSync(Request $request): Response
    {

        $lipSyncs = Lipsync::where('user_id', auth()->id())->where('result', '!=', '')
        ->select([
            'id',
            'user_id',
            'audio',
            'video',
            'result',
            'transcription_url',
            's3_url_video_audio',
            'created_at',
            'updated_at'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(12); // 12 items per page
        foreach($lipSyncs as $lip) {
            if($lip->transcription_url) {
                $lip->result = $lip->transcription_url;
            }
            if($lip->s3_url_video_audio) {
                $lip->result = $lip->s3_url_video_audio;
            }
        }
        return Inertia::render('Client/Lipsync/History',['list' => $lipSyncs]);
    }

    public function getAllLipSyncs(Request $request)
    {
        try {
            // Log::info('Getting paginated lip sync records for user ID: ' . auth()->id());

            $lipSyncs = Lipsync::where('user_id', auth()->id())->where('result', '!=', '')
                ->select([
                    'id',
                    'user_id',
                    'audio',
                    'video',
                    'result',
                    'created_at',
                    'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(12) // 12 items per page
                ->through(function ($lipSync) {
                    return [
                        'id' => $lipSync->id,
                        'user_id' => $lipSync->user_id,
                        'audio_url' => $lipSync->audio,
                        'video_url' => $lipSync->video,
                        'result' => $lipSync->result,
                        'created_at' => $lipSync->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $lipSync->updated_at->format('Y-m-d H:i:s')
                    ];
                });

            // Log::info('Found ' . $lipSyncs->total() . ' lip sync records');

            $lipSyncs->getCollection()->transform(function ($item) {
                $item['result'] = Helpers::preSignedS3Url($item['result']);
                return $item;
            });
            return response()->json([
                'status' => true,
                'data' => $lipSyncs->items(),
                'pagination' => [
                    'current_page' => $lipSyncs->currentPage(),
                    'per_page' => $lipSyncs->perPage(),
                    'total' => $lipSyncs->total(),
                    'last_page' => $lipSyncs->lastPage(),
                    'from' => $lipSyncs->firstItem(),
                    'to' => $lipSyncs->lastItem(),
                    'links' => [
                        'first' => $lipSyncs->url(1),
                        'last' => $lipSyncs->url($lipSyncs->lastPage()),
                        'prev' => $lipSyncs->previousPageUrl(),
                        'next' => $lipSyncs->nextPageUrl(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getAllLipSyncs: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error retrieving lip sync list',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function downloadFile(Request $request)
    {
        $url = $request["url"];
        $name = $request["name"];
        if (empty($url)) {
            return redirect()->back()->with('success', 'Không tồn tại video');
        }
        try {
            $contents = file_get_contents($url);
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'Không tồn tại video');
        }
        Storage::disk("public")->put($name, $contents);
        $path = storage_path('app/public/' . $name);
        return response()->download($path);
    }

    public function createVideoWithTranscription(Request $request)
    {
        $user = Auth::user();
        $response = $this->aIVideoService->createVideoWithTranscription($user, $request->all());
        return response()->json($response);
    }

    public function generateVideoWithTranscriptionWithSegmind(Request $request)
    {
        $user = Auth::user();
        $response = $this->aIVideoService->generateVideoWithTranscriptionWithSegmind($user, $request->all());
        if($response['success']) {
            $screenId = isset($request['screen_id']) ? $request['screen_id'] : 25;
            Auth::user()->chargeCredit('caption', 'caption', $request['duration'], null, null, $screenId, 26);
        }
        return response()->json($response);
    }

    public function createUrlToVideo(Request $request) {
        $msg_pictory = $request->session()->pull('messageErrorPictory', '');
        return Inertia::render('Client/AIVideo/UrlToVideo/Create', ['message_alert_pictory' => $msg_pictory,]);
    }
    public function UrlToVideo(Request $request)
    {
        $videoId = $request->id;
        $video = PictoryVideo::where("id", $videoId)->first();
        if(empty($video)) {
            return redirect("/");
        }
        $url = $video->article_url;
        $videoDes = $video->video_description;
        $title = '';
        if(!empty($url)) {
            $title = $this->extractTitleFromUrl($url);
        }
        $title = !empty($title) ? $title : 'Video chưa đặt tên';
        $pictoryAccessToken = $this->oauthPictory();
        $storyBoard = $this->createStoryboard($pictoryAccessToken['access_token'], $title, $url, $videoDes);
//        dd($storyBoard);
        $jobId = $storyBoard['jobId'] ?? null;
        if (empty($jobId)) {
            return redirect()->route('ai-video.url-to-video.create')->with('messageErrorPictory', 'Trang web của bạn không cho phép lấy dữ liệu tự động. Hãy nhập 1 trang web cho phép lấy dữ liệu tự động để tạo video.');
        }

        $video = PictoryVideo::create([
            'user_id' => auth('web')->id(),
            'job_id' => $jobId,
            'title' => $title,
        ]);

        $videoPreview = null;

        while (true) {
            $videoPreview = $this->getVideoPreview($pictoryAccessToken['access_token'], $jobId);
            Log::info($videoPreview);
            if ($videoPreview['success'] && isset($videoPreview['data']['status']) && $videoPreview['data']['status'] == 'in-progress') {
                continue;
            }

            if ($videoPreview['success'] && !isset($videoPreview['data']['status'])) {
                break;
            }
            sleep(10);
        }
        $video->update([
            'renderParams' => json_encode($videoPreview['data']['renderParams']),
            'preview_url' => $videoPreview['data']['preview'] ?? '',
        ]);
        return Inertia::render('Client/AIVideo/UrlToVideo/Index', ['video_preview' => $video,]);
    }

    public function createVideoPictory(Request $request) {
        $videoDescription = $request["video_description"];
        $article_url = $request["article_url"];
        $video = PictoryVideo::create([
            'user_id' => auth('web')->id(),
            'video_description' => $videoDescription,
            'article_url' => $article_url,
            'title' => 'Video chưa đặt tên',
        ]);
        return response()->json(['success' => true, 'id' => $video->id]);
    }

    public function editUrlToVideo(Request $request) {
        $videoId = $request->id;
        $video = PictoryVideo::find($videoId);
        return Inertia::render('Client/AIVideo/UrlToVideo/Index', ['video_preview' => $video]);
    }

    private function oauthPictory()
    {
        $url = config('pictory.api.base_url') . '/v1/oauth2/token';
        $client_id = config('pictory.api.client_id');
        $client_secret = config('pictory.api.client_secret');
        $data = [
            'client_id' => $client_id,
            'client_secret' => $client_secret
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function createStoryboard($accessToken, $videoName, $articleUrl, $videoDes)
    {
        $pictoryUserId = config('pictory.api.user_id');
        $url = config('pictory.api.base_url') . '/v1/video/storyboard';
        $scene =  [
            "voiceOver" => true,
            "splitTextOnNewLine" => false,
            "splitTextOnPeriod" => true
        ];
        if(!empty($articleUrl)) {
            $scene["article_url"] = $articleUrl;
        }
        if(!empty($videoDes)) {
            $scene["text"] = $videoDes;
        }
        $data = [
            'videoName' => $videoName,
            'audio' => [
                'autoBackgroundMusic' => true,
                'backGroundMusicVolume' => 1,
            ],
            'scenes' => [
                $scene
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'X-Pictory-User-Id: ' . $pictoryUserId,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function getVideoPreview($accessToken, $jobId)
    {
        $pictoryUserId = config('pictory.api.user_id');
        $url = "https://api.pictory.ai/pictoryapis/v1/jobs/" . $jobId;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'X-Pictory-User-Id: ' . $pictoryUserId,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            return [
                'error' => 'Error fetching video preview: ' . $data['error']
            ];
        }

        return $data;
    }

    private function extractTitleFromUrl($url)
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';

        $segments = explode('/', $path);
        $title = end($segments);

        return ucfirst(str_replace('-', ' ', $title));
    }

    public function historyUrlToVideo(Request $request)
    {
        try {
            $user = Auth::user();
            $per_page = 8;
            $result = PictoryVideo::where('user_id', '=', $user->id)
                ->whereNotNull('s3_url')
                ->select('id', 's3_url', 'final_url')
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);

            return Inertia::render('Client/AIVideo/UrlToVideo/History', ['list' => $result]);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function updateVideo(Request $request)
    {
        try {
            $user = auth()->user();
            $videoId = $request->input('videoId');
            $videoTitle = $request->input('videoName');
            $combinedText = $request->input('combinedText');

            if (!$videoId || !$combinedText) {
                return response()->json(['error' => 'Thiếu videoId hoặc combinedText'], 400);
            }

            $video = PictoryVideo::find($videoId);

            if (!$video) {
                return response()->json(['error' => 'Video không tồn tại'], 404);
            }

            $pictoryAccessToken = $this->oauthPictory();

            $storyBoard = $this->updateStoryboard($pictoryAccessToken['access_token'], $videoTitle, $combinedText);

            $jobId = $storyBoard['data']['job_id'];

            if (!$jobId) {
                return response()->json(['error' => 'Failed to create video job'], 500);
            }

            $videoPreview = null;

            while (true) {
                $videoPreview = $this->getVideoPreview($pictoryAccessToken['access_token'], $jobId);
                Log::info($videoPreview);
                if ($videoPreview['success'] && isset($videoPreview['data']['status']) && $videoPreview['data']['status'] == 'in-progress') {
                    continue;
                }

                if ($videoPreview['success'] && !isset($videoPreview['data']['status'])) {
                    break;
                }
                sleep(10);
            }
            $video->update([
                'renderParams' => json_encode($videoPreview['data']['renderParams']),
                'preview_url' => $videoPreview['data']['preview'] ?? '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật storyboard thành công!',
                'video' => $video,
                'renderParams' => $videoPreview['data']['renderParams'],
            ]);

        } catch (Exception $e) {
            Log::error('Lỗi khi cập nhật storyboard: ' . $e->getMessage());
            return response()->json(['error' => 'Đã xảy ra lỗi trong quá trình cập nhật storyboard'], 500);
        }
    }

    private function updateStoryboard($accessToken, $videoName, $videoContent)
    {
        $pictoryUserId = config('pictory.api.user_id');
        $url = config('pictory.api.base_url') . '/v1/video/storyboard';

        $data = [
            'videoName' => $videoName,
            'audio' => [
                'autoBackgroundMusic' => true,
                'backGroundMusicVolume' => 1,
            ],
            'scenes' => [
                [
                    'text' => $videoContent,
                    'splitTextOnNewLine' => false,
                    'splitTextOnPeriod' => true
                ]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'X-Pictory-User-Id: ' . $pictoryUserId,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function createVideo(Request $request)
    {
        $user = auth()->user();
        $videoName = $request->input('videoName');
        $videoId = $request->input('videoId');
        $summaryContent = $request->input('summaryContent');
        $renderParams = $request->input('renderParams');

        $renderParams['scenes'] = $summaryContent;

        $job = $this->renderVideoWithPictory($renderParams);

        if (isset($job['data']['job_id'])) {
            $response = $this->getPictoryVideoUrl($job['data']['job_id']);

            if ($response['success']) {
                $video = PictoryVideo::where('id', $videoId)->first();
                if ($video) {
                    $video->update([
                        'title' => $videoName,
                        's3_url' =>json_encode($response['data']),
                        'renderParams' => $renderParams,
                    ]);
                    Auth::user()->chargeCredit('pictory_create_video', 'pictory_create_video', $response['data']['videoDuration'], null, null, 23, 24);
                    return response()->json([
                        'success' => true,
                        'message' => 'Video đã được tạo thành công và upload lên S3!',
                        'data' => $response['data'],
                        'video' => $video,
                        'credit' => $user->credit ?? 0
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Video không tìm thấy trong cơ sở dữ liệu.',
                    ]);
                }
            }
        }

        return response()->json([
            'error' => 'Có lỗi trong quá trình tạo video',
        ], 500);
    }

    public function queueMergeAudioToVideo(Request $request){
        $user = Auth::user();
        $videoId = $request->input('videoId');
        $presigned_url = $request->input('presigned_url');
        $video = PictoryVideo::find($videoId);
        $video->is_upload_audio = false;
        if (!$video || !$presigned_url) {
            return response()->json([
                'error' => 'Video hoặc audio không tồn tại',
            ], 404);
        }
        MergeAudioVideo::dispatch($video->id, $presigned_url, 6, "")->onQueue("merge-audio-video");
        $video->save();
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào hàng đợi',
        ]);
    }

    public function checkQueueStatus($videoId){
        $video = PictoryVideo::find($videoId);

        if($video->is_upload_audio) {
            return response()->json([
                'success' => true,
                'message' => 'Video đã được tạo xong',
                'data' => [
                    'final_url' => $video->final_url,
                ],
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Video đang được tạo',
            ]);
        }
    }

    private function renderVideoWithPictory($renderParams)
    {
        $pictoryUserId = config('pictory.api.user_id');
        $url = config('pictory.api.base_url') . "/v1/video/render";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($renderParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->oauthPictory()['access_token'],
            'X-Pictory-User-Id: ' . $pictoryUserId,
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['success' => false, 'error' => curl_error($ch)];
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function getPictoryVideoUrl($jobId)
    {
        $pictoryUserId = config('pictory.api.user_id');
        $url = "https://api.pictory.ai/pictoryapis/v1/jobs/{$jobId}";

        do {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->oauthPictory()['access_token'],
                'X-Pictory-User-Id: ' . $pictoryUserId,
                'Content-Type: application/json',
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return ['success' => false, 'error' => curl_error($ch)];
            }

            curl_close($ch);

            $responseData = json_decode($response, true);
            if (isset($responseData['data']['status']) && $responseData['data']['status'] != 'in-progress') {
                Log::info('Debug response video data: '.json_encode($responseData));
                return ['success' => true, 'data' => $responseData['data']];
            }

            sleep(10);
        } while (true);

        return ['success' => false, 'error' => 'Không thể hoàn thành video sau khi thử lại nhiều lần'];
    }

    public function deleteUrlToVideo(Request $request)
    {
        $id = $request->id;
        try {
            $video = PictoryVideo::find(id: $id);
            if (!$video) {
                return [
                    'message' => 'File không tồn tại.'
                ];
            }
            $video->delete();
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

    public function translateContent(Request $request)
    {
        $content = $request['content'];
        $language = $request['language'];
        try {
            // Loop through content array by reference
            foreach ($content as &$item) {  // Use reference here with `&`
                if (isset($item['subtitles'][0]['text'])) {
                    $originalText = $item['subtitles'][0]['text'];
                    $translateText = $this->chatGPTService->generateTranslate($originalText, $language);
                    $item['subtitles'][0]['text'] = $translateText;
                    // $item['sub_scenes'][0]['text_lines'] = $translateText;
                }
            }
            // Return the updated content
            return $content;
        } catch (Exception $e) {
            return [
                'message' => 'Lỗi khi translate.',
            ];
        }
    }

    public function getDuration($full_video_path) {
        $getID3 = new \getID3;
        $videoPath = storage_path('app/public/video.mp4');
        file_put_contents($videoPath, file_get_contents($full_video_path));
        $file = $getID3->analyze($videoPath);
        $playtime_seconds = $file['playtime_seconds'];
        $second = date('s', $playtime_seconds);
        $minutes = date('i', $playtime_seconds);
        $total = $minutes*60 + $second;
        return $total;
    }

    public function mergeVideo(Request $request) {
        $user = Auth::user();
        $ids = $request->ids;
        $model = $request["is_image"] ? "merge-video-image" : 'merge-video';
        $typeQuery = $request['type_query'];
        if($typeQuery &&  $typeQuery == "all") {
            $model = 'merge-video-all';
        }
        $audioDescription = $request["audioDescription"];
        $ratio = $request["ratio"];
        if($ratio == "1:1") {
            $ratio = "1080:1080";
        }
        if($ratio == "9:16") {
            $ratio = "1080:1920";
        }
        if($ratio == "16:9") {
            $ratio = "1920:1080";
        }
        $audioFile = $request->file('audio_file');

        $audioPath = "";
        if ($audioFile) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audioPath = $audioFile->store('audio', 'public');
            Storage::disk('s3')->put($audioPath, file_get_contents(storage_path('app/public/'.$audioPath)));
        }

        $imageFile = $request->file('image_file');

        $imagePath = "";
        if ($imageFile) {
            if (!file_exists(storage_path('app/public/images'))) {
                mkdir(storage_path('app/public/images'), 0777, true);
            }
            $imagePath = $imageFile->store('images', 'public');
            Storage::disk('s3')->put($imagePath, file_get_contents(storage_path('app/public/'.$imagePath)));
        }
        if(count($ids) > 0) {
            $store = $this->aIVideoService->storeMedia([
                'user_id' => $user->id,
                'description' => "gộp video",
                'model' => $model,
                'duration' => 0,
                's3_url' => "",
                'thumbnail' => "",
                'type' => 'video',
            ]);

            // dispatch(new MergeVideo($ids, $store->id, $audioPath, $imagePath, $audioDescription, $ratio, 1))->onQueue("merge-video");
            $this->aIVideoService->mergeVideo($ids, $store->id, $audioPath, $imagePath, $audioDescription, $ratio);
            $this->cleanupFiles([$audioPath, $imagePath]);
            return response()->json(["success" => true, "id" => $store->id,  "message" => "Video đang trong quá trình ghép"], 200);
        } else {
            return response()->json(["success" => false, "message" => "không tồn tại đường dẫn"], 500);
        }
    }
    public function getAllVideo(Request $request) {
        $user = Auth::user();
        $isImage = $request["is_image"] ? true : false;
        $list = $this->aIVideoService->getAllVideo($user->id, $isImage, $request->type_query);
        if($request->type_query) {
            for($i = 0; $i < count($list); $i++) {
                $list[$i]["type"] = "video";
                $list[$i]["table"] = "ai_media";
                $list[$i]["video_type"] = "video";
                if($list[$i]["model"] === "merge-video-all") {
                    $list[$i]["video_type"] = "video-merge";
                }
                if($list[$i]["model"] === "img-video") {
                    $list[$i]["video_type"] = "image-video";
                }
            }
            $lipsync = Lipsync::where("user_id", $user->id)->where("result", "!=", "")->orderBy('created_at', 'desc')->get();
            foreach($lipsync as $value) {
                if(empty($value->thumbnail)) {
                    $video_url = Helpers::preSignedS3Url($value->result);
                    $video_path = 'videos/' . Str::uuid() . '.mp4';
                    file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));
                    $thumbnailPath = 'images/' . Str::uuid() . '.jpg';
                    FFmpegHelper::createThumbnail(storage_path('app/public/' .$video_path), storage_path('app/public/' .$thumbnailPath));
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
                    }
                    Lipsync::where("id", $value->id)->update(["thumbnail" => $thumbnailPath]);
                    $value->thumbnail = $thumbnailPath;
                    $this->cleanupFiles([
                        $thumbnailPath,
                        $video_path
                    ]);

                }
            }
            $mcVirtuals = McVirtual::where("user_id", $user->id)->where("result_url", "!=", "")->orderBy('created_at', 'desc')->get();
            foreach($lipsync as $value) {
                $list[] = [
                    "thumbnail" =>  Helpers::preSignedS3Url($value->thumbnail),
                    "id" => $value->id,
                    "s3_url" => $value->result,
                    "table" => "lipsync",
                    "video_type" => "lipsync",
                    "type" => "video"
                ];
            }
            foreach($mcVirtuals as $value) {
                if($value->avatar_url != "") {
                    if (str_contains($value->avatar_url, "https://adbox-staging.s3.ap-northeast-1.amazonaws.com")) {
                        $avatarUrl = $value->avatar_url;
                        $avatarUrl = str_replace("https://adbox-staging.s3.ap-northeast-1.amazonaws.com/","",$avatarUrl);
                        $avatarUrl = explode(".jpg", $avatarUrl);
                        if(count($avatarUrl) == 2) {
                            $value->avatar_url =  $avatarUrl[0].".jpg";
                        }
                    }
                    $list[] = [
                        "thumbnail" => $value->avatar_url,
                        "s3_url" => $value->result_url,
                        "id" => $value->id,
                        "table" => "mc_virtual",
                        "video_type" => "mc_virtual",
                        "type" => "video"
                    ];
                }
            }
        }
        return response()->json(["success" => true, "data" => $list]);
    }

    public function mergeImageVideoQueue(Request $request) {
        $imageFile = $request->file('image_file');
        $aiMediaId = $request->ai_media_id;
        $image = "";
        if ($imageFile) {
            if (!file_exists(storage_path('app/public/images'))) {
                mkdir(storage_path('app/public/images'), 0777, true);
            }
            $image_path = $imageFile->store('images', 'public');
            $this->aIVideoService->updateById($aiMediaId, ["is_upload_image" => false]);
            Storage::disk('s3')->put($image_path, file_get_contents(storage_path('app/public/'.$image_path)));
            MergeImageVideo::dispatch($aiMediaId, $image_path)->onQueue("merge-image-video");
            // $this->aIVideoService->mergeImageVideo($aiMediaId, $image_path);
            $this->cleanupFiles([
                $image_path,
            ]);
            return  response()->json(['message' => "Nhạc nền đang trong quá trình tạo", "success" => true]);
        } else {
            return response()->json(['message' => "image không tồn tại", "success" => false], 400);
        }

    }

    public function convertRatioQueue(Request $request) {
        $ratio = $request['ratio'];
        $aiAudioId = $request->ai_media_id;
        if($ratio == "1:1") {
            $ratio = "1080:1080";
        }
        if($ratio == "9:16") {
            $ratio = "1080:1920";
        }
        if($ratio == "16:9") {
            $ratio = "1920:1080";
        }
        if($ratio != "1080:1080" && $ratio != "1920:1080" &&  $ratio != "1080:1920") {
            return response()->json(["success" => false, "message" => "Kích thước video không hợp lệ"], 200);
        }
        $this->aIVideoService->updateById($aiAudioId, ["is_convert_ratio" => false]);
        // $this->aIVideoService->convertRatio($aiAudioId, $ratio);
        MergeAudioVideo::dispatch($aiAudioId, "", "ratio", $ratio)->onQueue("merge-audio-video");
        return  response()->json(['message' => "Nhạc nền đang trong quá trình tạo", "success" => true]);
    }

    public function suggestContentVideo(Request $request) {
        $content = $request->input('content');
        $content =  $content." gợi ý nội dung ngắn gọn chuyển động máy quay.chỉ trả nội dung";
        $prompt = $this->chatGPTService->getResponse($content);
        return response()->json([
            'data' => $prompt,
        ]);
    }

    public function suggestContent(Request $request)
    {
        $content = $request->input('content');
        $content =  $content.' gợi ý nội dung tạo kịch bản cho video 200 từ tiếng việt chỉ trả về nội dung không trả về tiêu đề';
        $prompt = $this->chatGPTService->getResponse($content);
        return response()->json([
            'data' => $prompt,
        ]);
    }
}

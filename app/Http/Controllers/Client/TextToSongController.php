<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;

use App\Services\AIVideoService;
use App\Services\AIMusicService;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\SlideAiComponentService;
use App\Services\SlideAiTemplateService;
use App\Services\StorageService;
use App\Services\TextToSpeechService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\UploadedFile;
use App\Models\Music;
use App\Helper\Helpers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;
use App\Constants\AIModel;


class TextToSongController extends Controller
{

    private $getTopmediaUrl;
    protected $topmediaiApi ;
    private $SUNOAPIUrl;
    protected $SUNOAPIKey ;
    public function __construct(
        protected ChatClaudeService $chatClaudeService,
        protected ChatGPTService $chatGPTService,
        protected SlideAiTemplateService $slideAiTemplateService,
        protected SlideAiComponentService $slideAiComponentService,
        protected StorageService $storageService,
        protected AIVideoService $aiVideoService,
        protected TextToSpeechService $textToSpeechService,
        protected AIMusicService $aIMusicService,
    ) {
        $this->topmediaiApi = env('TOPMEDIAI_API_KEY');
        $this->getTopmediaUrl=env('TOPMEDIAI_API_BASE_URL' , 'https://api.topmediai.com/');
        $this->SUNOAPIKey = env('SUNO_API_KEY');
        $this->SUNOAPIUrl = env('SUNO_API_BASE_URL');
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Client/TextToSong/Index');
    }

    public function history(Request $request): Response
    {
        return Inertia::render('Client/TextToSong/History');
    }

    // Generated Song
    public function generateSong(Request $request)
    {
        try {
            $data = $request->json()->all();
            $content = $data['content'] ?? '';
            $title = $data['musicTitle'] ?? '';
            $lyrics = $data['lyrics'] ?? '';
            $lyrics = str_replace("\n", " ", $data['lyrics'] ?? '');
                $musicData = [
                    'is_auto' => 0 , 
                    'prompt' => $content, 
                    "lyrics" => $lyrics, 
                    "title"=> $title,
                    "instrumental" => 0,
                    "model_version" => "v3.5",
                    "continue_at" => 0, 
                    "continue_song_id"=>"",
                ];
                Log::Info("Music Data => ", [$musicData]);
                // Call api to get song
                $songResponse = Http::withHeaders([
                    'x-api-key' => $this->topmediaiApi,
                    'Content-Type' => 'application/json'
                ])
                ->timeout(1200)
                ->post($this->getTopmediaUrl . 'v2/submit', $musicData);
                Log::Info("songResponse => : ", [$songResponse->json()]);
                
                if (isset($songResponse->json()['status']) && $songResponse->json()['status'] === 500) {
                    return response()->json([
                        'success' => false,
                        'message' => $songResponse->json()['message'],
                    ], 500);
                }

                $resultsData = []; // Mảng kết quả trả về
                if ($songResponse->successful()) {
                    $jsonData = $songResponse->json()['data'] ?? [];
                        
                        foreach($jsonData as $song) {
                            $audioFile = file_get_contents($song['audio']);
                            $imageFile = file_get_contents($song['image']);
                            Log::Info("start 1");
                             // audio
                            $nameAudio = Str::uuid() . '.mp3';
                            $localAudioPath = storage_path("app/audio/{$nameAudio}");
                            Storage::disk('local')->put("audio/{$nameAudio}", $audioFile);
                            $uploadedAudioFile = new UploadedFile($localAudioPath, $nameAudio);

                             // image
                            $nameImage = Str::uuid() . '.png';
                            $localImagePath = storage_path("app/image/{$nameImage}");
                            Storage::disk('local')->put("image/{$nameImage}", $imageFile);
                            $uploadedImageFile = new UploadedFile($localImagePath, $nameImage);

                            // Upload both files to S3
                            $audioResult = $this->storageService->putToS3($uploadedAudioFile, 'audio_generate');
                            $imageResult = $this->storageService->putToS3($uploadedImageFile, 'image_generate');
                            // Log::Info("audioResult S3 : ", [$audioResult]);
                            // Log::Info("imageResult S3 : ", [$imageResult]);

                            // Delete local files
                            Storage::disk('local')->delete("audio/{$nameAudio}");
                            Storage::disk('local')->delete("image/{$nameImage}");
                            Log::Info("start 2");
                            $resultsData[] = [
                                'audio' => $audioResult,
                                'image' => $imageResult,
                                'lyric' => $song['lyric'],
                                'tags' => $song['tags'],
                                'title' => $song['title']
                            ];
                        };
                        
                        Log::Info("Data from api => : ", [$resultsData]);
                        // // Trừ tiền credit user
                        Auth::user()->chargeCredit('music_to_text', null, null, null, null, 10, 10);

                        return response()->json([
                            'success' => true,
                            'message' => 'Generate song successful',
                            'data' => $resultsData,
                        ], 200);
                    } else {
                        Log::Info("Lỗi trong quá trình tạo nhạc");
                        return response()->json([
                            'success' => false,
                            'message' => 'generate song fail'
                        ], 404);
                    }
        } catch (\Exception $e) {
        log::error("Error at generated song : ", [$e]);
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy dữ liệu',
            'error' => $e->getMessage()
        ], 500);
        }
    }

    public function suggestContentMusic(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy tạo ra một đoạn prompt ngắn gọn dùng để sáng tác nhạc dựa trên chủ đề "';
        $prompt .= $content;
        $prompt .= '". Phân tích chủ đề này và xác định các yếu tố cảm xúc, không khí và đặc điểm của nó. Từ đó, tạo ra một đoạn nhạc với giai điệu và nhịp điệu phù hợp. Nếu chủ đề có cảm giác nhẹ nhàng, như thiên nhiên, mùa xuân, hay biển cả, hãy sáng tác một đoạn nhạc du dương, êm dịu và dễ chịu. Nếu chủ đề liên quan đến năng lượng, như lễ hội, cuộc sống đô thị, hay thể thao, hãy tạo ra nhạc với nhịp điệu nhanh, mạnh mẽ và sôi động. Nếu chủ đề buồn, như cô đơn, mùa thu, hay chia ly, hãy tạo ra một giai điệu trầm lắng, u sầu nhưng vẫn dễ cảm nhận. Đảm bảo đoạn nhạc dễ nghe và phù hợp với chủ đề mà người nghe có thể dễ dàng cảm nhận được, chỉ cần đoạn promt ngắn gọn (phải ít hơn 100 kí tự) và tuyệt đối không cần giải thích gì thêm.';
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function summaryPrompt(Request $request) {
        $user = Auth::user();

        $data = $request->json()->all();
        $prompt = $data['promt'] ?? '';
        $musicTypes = $data['musicTypes'] ?? '';
        $dataRequest = 'Hãy tóm tắt nội dung : ' . $prompt . " thể loại âm nhạc : ". $musicTypes . ' sao cho đầy đủ ý (không được chứa chữ ngọt) và tuyệt đối không được vượt quá (100) ký tự kể cả ký tự đặt biệt và dấu khoảng trắng.';
        $model = AIModel::GPT_4_MINI;
        try {
            $generatedText = $this->chatGPTService->gptSendRequest($dataRequest, null, $model);
            if (!empty($generatedText)){
                return response()->json([
                    'success' => true,
                    'message' => 'Đặc tả dữ liệu thành công',
                    'data' => $generatedText,
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặc tả dữ liệu',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Có lỗi xảy ra trong quá trình tóm tắt promt => ', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình tóm tắt nội dung',
                'error' => $e->getMessage()
            ]);
        }

    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'prompt' => 'required|string',
            'data' => 'required|array',
        ]);
        try {
            $user_id = $user->id;
            $prompt = $request->prompt;
            $records = []; 
            Log::Info("prompt log => ". $prompt);
            foreach ($request->data as $song) {
                $audioS3Url = $this->uploadFileFromUrlToS3($song['audioUrl'], 'audio_generate');
                $imageS3Url = $this->uploadFileFromUrlToS3($song['imageUrl'], 'image_generate');

                if (!$audioS3Url || !$imageS3Url) {
                    Log::warning("Bỏ qua bài hát do upload lỗi", ['song' => $song]);
                    continue; // hoặc throw nếu bạn muốn fail luôn
                }

                $data = [
                    'sample_audio' => "Không có",
                    'user_id' => $user_id,
                    'prompt' => $prompt,
                    'result_audio' => $audioS3Url,
                    'lyrics' => $song['prompt'],
                    'image_url' => $imageS3Url,
                    'model' => 'song',
                ];

                Log::Info("data => ", [$data]);
                // Lưu từng bài hát thông qua service
                $record = $this->aIMusicService->storeMedia($data);
                Log::Info("record  : ", [$record]);

                // Lưu lại kết quả để trả về
                $records[] = $record->toArray();
            }
            return response()->json([
                'success' => true,
                'response' => $records, // Trả về danh sách các bản ghi đã lưu
                'credits' => $user->credit ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving data in text-to-song:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình lưu.'
            ]);
        }
    }

    public function getAllMusic()
    {
    try {
        $user_id = auth()->id();
        // Log::info('User ID: ' . $user_id);

        // Lấy records từ bảng music của user hiện tại
        $musicRecords = Music::where('user_id', $user_id)
        ->where('model', 'song')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($record) {
            return [
                'id' => $record->id,
                'sample_audio',
                'prompt' => $record->prompt,
                'lyrics' => $record->lyrics,
                'user_id' => $record->user_id,
                'image_url' => $record->image_url ? Helpers::preSignedS3Url($record->image_url) : null,
                'result_audio' => $record->result_audio ? Helpers::preSignedS3Url($record->result_audio) : null,
            ];
        });

        // Log::info('Music records: ' . $musicRecords);

        // Trả về response dạng JSON
        return response()->json([
            'success' => true,
            'data' => $musicRecords,
            'total' => $musicRecords->count()
        ]);

    } catch (\Exception $e) {
        // Log lỗi nếu có
        // Log::error('Error in getAllMusic: ' . $e->getMessage());

        // Trả về response lỗi
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy dữ liệu',
            'error' => $e->getMessage()
        ], 500);
    }
    }
    private function uploadFileFromUrlToS3(string $url, string $folder): ?string
    {
        try {
            $response = Http::timeout(300)->get($url);
            if (!$response->ok()) {
                Log::error("Không thể tải file từ URL (HTTP lỗi): $url");
                return null;
            }
            $fileContent = $response->body();

            // Tách đuôi file
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            $filename = Str::uuid() . '.' . $extension;

            // Lưu tạm file local
            $localPath = storage_path("app/temp/{$filename}");
            Storage::disk('local')->put("temp/{$filename}", $fileContent);

            // Tạo UploadedFile giả
            $uploadedFile = new UploadedFile($localPath, $filename);

            // Upload lên S3
            $result = $this->storageService->putToS3($uploadedFile, $folder);
            // Xoá local
            Storage::disk('local')->delete("temp/{$filename}");

            return is_array($result) ? ($result['path'] ?? null) : $result;
        } catch (\Exception $e) {
            Log::error("Lỗi khi upload file từ URL: " . $e->getMessage());
            return null;
        }
    }
    public function generateSunoSong(Request $request)
    {
        try {
            $data    = $request->json()->all();
            $prompt       = $data['prompt'] ?? '';
            $title        = $data['title'] ?? 'Music';
            $style        = $data['style'] ?? '';
            $customMode   = isset($data['customMode']) ? (bool)$data['customMode'] : true;
            $instrumental = isset($data['instrumental']) ? (bool)$data['instrumental'] : false;
            $model        = $data['model'] ?? 'V4';
            $callBackUrl  = $data['callBackUrl'] ?? '';

            $musicData = [
                'prompt'       => $prompt,
                'style'        => $style,
                'title'        => $title,
                'customMode'   => $customMode,
                'instrumental' => $instrumental,
                'model'        => $model,
                'callBackUrl'  => $callBackUrl,
            ];

            $songResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->SUNOAPIKey,
                'Content-Type'  => 'application/json'
            ])
            ->timeout(1200)
            ->post($this->SUNOAPIUrl . 'v1/generate', $musicData);

            $responseJson = $songResponse->json();

            $taskId = $responseJson['data']['taskId'] ?? null;
            if (!$taskId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không nhận được task ID từ API: ' . ($responseJson['msg'] ?? 'Unknown error')
                ], 400);
            }

            $maxAttempts = 80;  
            $attempt     = 0;
            $result      = null;
            while ($attempt < $maxAttempts) {
                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->SUNOAPIKey,
                    'Content-Type'  => 'application/json'
                ])
                ->timeout(1200)
                ->get($this->SUNOAPIUrl . "v1/generate/record-info", [
                    'taskId' => $taskId
                ]);

                $statusJson = $statusResponse->json();
                $status = $statusJson['data']['status'] ?? null;

                if (strtolower($status) === 'success') {
                    $result = $statusJson;
                    break;
                }

                if (in_array(strtoupper($status), ['CREATE_TASK_FAILED', 'GENERATE_AUDIO_FAILED', 'CALLBACK_EXCEPTION', 'SENSITIVE_WORD_ERROR'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tạo nhạc thất bại: ' . ($statusJson['data']['response']['errorMessage'] ?? 'Unknown error')
                    ], 500);
                }

                sleep(3);
                $attempt++;
            }

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tác vụ chưa hoàn thành, vui lòng thử lại sau.'
                ], 202);
            }

            $responseData = $result['data']['response'] ?? null;
            if (!$responseData || !is_array($responseData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin kết quả bài nhạc'
                ], 500);
            }

            $sunoData = $responseData['sunoData'] ?? null;
            if (!$sunoData || !is_array($sunoData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu bài nhạc'
                ], 500);
            }
            // // Trừ tiền credit user
            Auth::user()->chargeCredit('music_to_text', null, null, null, null, 10, 10);
            return response()->json([
                'success' => true,
                'message' => 'Tạo nhạc thành công',
                'data'    => [
                    'taskId'   => $result['data']['taskId'] ?? $taskId,
                    'sunoData' => $sunoData  
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error during song generation: ", [$e]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo nhạc',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function convertAudioToVideo(Request $request)
    {
        try {
            $data = $request->json()->all();
            $audioId    = $data['audioId'] ?? null;
            $audioTaskId = $data['taskId'] ?? null; 
            $callBackUrl = $data['callBackUrl'] ?? '';

            if (!$audioId || !$audioTaskId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin audioId hoặc taskId của audio'
                ], 400);
            }

            $videoData = [
                'taskId'   => $audioTaskId,
                'audioId'  => $audioId,
                'callBackUrl' => $callBackUrl,
            ];

            $videoResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->SUNOAPIKey,
                'Content-Type'  => 'application/json'
            ])
            ->timeout(1200)
            ->post($this->SUNOAPIUrl . 'v1/mp4/generate', $videoData);

            $responseJson = $videoResponse->json();

            $videoTaskId = $responseJson['data']['taskId'] ?? null;
            if (!$videoTaskId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không nhận được task ID từ API video: ' . ($responseJson['msg'] ?? 'Unknown error')
                ], 400);
            }

            $maxAttempts = 80;  
            $attempt     = 0;
            $result      = null;
            while ($attempt < $maxAttempts) {
                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->SUNOAPIKey,
                    'Content-Type'  => 'application/json'
                ])
                ->timeout(1200)
                ->get($this->SUNOAPIUrl . "v1/mp4/record-info", [
                    'taskId' => $videoTaskId
                ]);

                $statusJson = $statusResponse->json();
                $status = $statusJson['data']['successFlag'] ?? null;

                if (strtolower($status) === 'success') {
                    $result = $statusJson;
                    break;
                }

                if (in_array(strtoupper($status), ['CREATE_TASK_FAILED', 'GENERATE_MP4_FAILED', 'CALLBACK_EXCEPTION'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Chuyển đổi MP4 thất bại: ' . ($statusJson['data']['errorMessage'] ?? 'Unknown error')
                    ], 500);
                }

                sleep(3);
                $attempt++;
            }

            $videoResponseData = $result['data']['response'] ?? null;
            $videoUrl = $videoResponseData['videoUrl'] ?? null;
            if (!$videoUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy kết quả video'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Chuyển đổi video thành công',
                'data'    => [
                    'taskId'   => $videoTaskId,
                    'videoUrl' => $videoUrl
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error during video conversion: ", [$e]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chuyển đổi video',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
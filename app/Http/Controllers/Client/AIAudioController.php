<?php

namespace App\Http\Controllers\Client;

use App\Helper\AudioReaderService;
use App\Helper\FFmpegHelper;
use App\Http\Controllers\Controller;
use App\Services\AIAssistantsService;
use App\Services\AIAudioService;
use App\Services\AIMediaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helper\Helpers;
use App\Services\AudioService;
use App\Services\DocumentReaderService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use App\Services\StorageService;
use App\Models\VoiceType;
use App\Repositories\VoiceTypeRepository;
use App\Traits\ApiResponses;
use Exception;

class AIAudioController extends Controller
{
    use ApiResponses;
    private $aIAssistantsService;
    private $aIAudioService;
    private $aIMediaService;
    private $audioService;
    private $audioReaderService;

    private $getElevenlabUrl;
    protected $elevenlabApi;
    private $voiceTypeRepository;
    private $sample_text = '"Buổi sáng, ánh nắng nhẹ nhàng chiếu qua cửa sổ, mang theo sự ấm áp của ngày mới. Ngoài đường, tiếng xe cộ và tiếng nói cười của mọi người làm không gian trở nên sống động. Những hàng cây bên đường đung đưa theo gió, như đang hòa mình vào bản nhạc của thiên nhiên. Cuộc sống là những chuỗi ngày đầy sắc màu, và mỗi ngày mới là một cơ hội để ta trải nghiệm, học hỏi và tận hưởng những điều giản dị nhưng ý nghĩa."';
    public function __construct(
        AIAssistantsService $aIAssistantsService,
        AIAudioService $aIAudioService,
        AIMediaService $aIMediaService,
        AudioService $audioService,
        AudioReaderService $audioReaderService,
        protected StorageService $storageService,
        VoiceTypeRepository $voiceTypeRepository
    ) {
        $this->aIAssistantsService = $aIAssistantsService;
        $this->aIAudioService = $aIAudioService;
        $this->aIMediaService = $aIMediaService;
        $this->audioService = $audioService;
        $this->audioReaderService = $audioReaderService;

        $this->elevenlabApi = env('ELEVENLAB_API_KEY');
        $this->getElevenlabUrl=env('ELEVENLAB_API_BASE_URL' , 'https://api.elevenlabs.io/');
        $this->voiceTypeRepository = $voiceTypeRepository;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // lấy thông tin về quyền tạo clone_voice.
        $isActiveCloneVoice = $this->aIAudioService->authorCloneVoice($user->id);
        $listAllVoice = $this->aIAudioService->getListVoice($user->id);
        return Inertia::render('Client/AIAudio/Index', [
            'isActiveCloneVoice'=>$isActiveCloneVoice,
            'listAllVoice'=>$listAllVoice
        ]);
    }
    public function audioToText(Request $request)
    {
        return Inertia::render('Client/AIAudio/AudioToText');
    }
    public function listVoices(Request $request)
    {
        $user = Auth::user();
        $listAllVoice = $this->aIAudioService->getListVoice($user->id);
        return $this->okResponse($listAllVoice);
    }

    public function send_data(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'text' => 'required|string',
            'voice' => 'required|string',
        ]);
        $text = $request->text;
        $voice = $request->voice;
        $url = $this->aIAudioService->text_to_audio($text, $voice);
        if (!is_array($url) || !isset($url['path'])) {
            Log::error('Text to audio conversion failed or invalid response: ', ['response' => $url]);
            return response()->json(['message' => 'Failed to convert text to speech'], 500);
        }
        $data = [
            'user_id' => auth('web')->id(),
            's3_url' => $url['path'],
            'description' => $text,
            'type' => 'audio'
        ];
        $record = $this->aIMediaService->storeMedia($data);

        $signedUrl = Helpers::preSignedS3Url($url['path']);
        return response()->json([
            'success' => true,
            'data' => [
                'description' => $text,
                'voice' => $voice,
                's3_url' => $signedUrl,
                'credits' => $user->credits ?? 0,
            ]
        ], 200);
    }
    public function upload(Request $request)
    {
        try {
            $file = $request->file('file');
            
            $language = $this->convertLanguage($request->language ?? 'vi-VN');
            $extension = $file->getClientOriginalExtension();
            
            $audioName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $convertedFilePath = $this->audioService->convertWebmToMp3($file->getRealPath(), $extension, $audioName);
            $content = $this->audioReaderService->extractAudioToText($convertedFilePath, $language);
            
            // $fileUrl = $file->store(config('services.google_text_to_speech.folder'), 's3');
            $fileUrl = Storage::disk('s3')->putFileAs(
                config('services.google_text_to_speech.folder'), // Folder trong S3
                new \Illuminate\Http\File($convertedFilePath), // File cần upload
                uniqid($audioName) . '.mp3', // Tên file sau khi upload
            );
            $data = [
                'user_id' => auth('web')->id(),
                'description' => $content,
                'type' => 'text',
                's3_url' => $fileUrl,
            ];
            $record = $this->aIMediaService->storeMedia($data);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $record->id,
                    'description' => $content,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lên file. Vui lòng thử lại.',
            ], 500);
        }
    }
    public function uploadV2(Request $request)
    {
        $request->validate([
            'file' => [
                'required_without:message',
                'file',
                'mimes:mp3,wav,webm',
            ],
        ]);
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                //upload file to s3
                $audioContent = file_get_contents($file);
                $extension = $file->extension();
                Log::info('Audio extension: ' . $extension);
                if ($extension == 'webm') {
                    $audioName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $convertedFilePath = $this->audioService->convertWebmToMp3($file->getRealPath(), $extension, $audioName);
                    $audioContent = file_get_contents($convertedFilePath);
                }
                $filename = 'ai_audio/' . uniqid('audio_', true) . $file->getClientOriginalExtension();
                Storage::disk('s3')->put($filename, $audioContent);

                $url = Helpers::preSignedS3Url($filename);
                $data = [
                    'user_id' => auth('web')->id(),
                    'description' => $file->getClientOriginalName(),
                    'type' => 'audio',
                    's3_url' => $url,
                ];

                return response()->json([
                    'success' => true,
                    'data' => $data
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        }
    }

    public function textToSpeech(Request $request)
    {
        $user = Auth::user();
        // Validate input
        $request->validate([
            'text' => 'required|string',
            'voice' => 'required|string',
            'screen_id' => 'nullable|integer',
            'feature_id' => 'nullable|integer',
        ]);

        $text = $request->input('text');
        $voice = $request->input('voice');
        $screen_id = $request->input('screen_id') ?? 12;
        $feature_id = $request->input('feature_id') ?? 12;

        $data = [
            'app_id' => env('VBEE_APP_ID'),
            'response_type' => 'direct',
            'callback_url' => route('ai-audio.vbee.callback'),
            'input_text' => $text,
            'voice_code' => $voice,
            'audio_type' => 'mp3',
            'bitrate' => 128,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('VBEE_API_TOKEN')
        ])->post('https://vbee.vn/api/v1/tts', $data);

        if ($response->successful()) {
            $result = $response->json();

            $get_url = $result['result']['audio_link'];
            $audioContent = file_get_contents($get_url);
            $filename = 'ai_audio/' . uniqid('audio_', true) . '.mp3';
            Storage::disk('s3')->put($filename, $audioContent);

            $url = Helpers::preSignedS3Url($filename);
            Log::info('S3 url generate audio: ' . $url);

            // Lưu dữ liệu vào database
            $data = [
                'user_id' => auth('web')->id(),
                's3_url' => $url,
                'description' => $text,
                'voice_type' => $voice,
                'type' => 'audio'
            ];
            $record = $this->aIMediaService->storeMedia($data);

            // Trừ tiền credit user
            Auth::user()->chargeCredit('ai-audio', null, null, null, null, $screen_id, $feature_id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $record->id,
                    'description' => $text,
                    'voice_type' => $voice,
                    's3_url' => $url,
                    'credits' => $user->credits ?? 0,
                ],
            ]);
        } else {
            Log::error('Text to speech conversion failed: ', ['response' => $response->body()]);
            return response()->json(['message' => 'Failed to convert text to speech'], 500);
        }
    }
    public function textToSpeechV2(Request $request)
    {
        $user = Auth::user();
        // Validate input
        $request->validate([
            'text' => 'required|string',
            'voice' => 'required|string',
            'feature_id' => 'nullable|integer',
            'screen_id' => 'nullable|integer',
        ]);
        $screen_id = $request->input('screen_id');
        $feature_id = $request->input('feature_id');

        $text = $request->input('text');
        $voice = $request->input('voice');

        $data = [
            'app_id' => env('VBEE_APP_ID'),
            'response_type' => 'direct',
            'input_text' => $text,
            'voice_code' => $voice,
            'audio_type' => 'mp3',
            'bitrate' => 128,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('VBEE_API_TOKEN')
        ])->post('https://vbee.vn/api/v1/tts', $data);

        if ($response->successful()) {
            $result = $response->json();
            $get_url = $result['result']['audio_link'];
            // Trừ tiền credit user
            Auth::user()->chargeCredit('ai-audio', null, null, null, null, $screen_id, $feature_id);
            return response()->json([
                'success' => true,
                'data' => [
                    'description' => $text,
                    'voice' => $voice,
                    's3_url' => $get_url,
                    'credits' => $user->credits ?? 0,
                ]
            ]);
        } else {
            Log::error('Text to speech conversion failed: ', ['response' => $response->body()]);
            return response()->json(['message' => 'Failed to convert text to speech'], 500);
        }
    }

    public function loadMore(Request $request)
    {
        $userId = auth('web')->id();
        $perPage = 5;
        $type = $request->type;
        $messages = $this->aIAudioService->getListAudioByUserId($userId, $type, $perPage);
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }
    public function deleteVoice(int $id)
    {
        try {
            Log::info('Delete voice id: ' . $id);
            $voice = $this->voiceTypeRepository->find($id);
            if (!$voice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voice không tồn tại.'
                ], 404);
            }

            // Call Eleven Labs API to delete the voice
            $response = Http::withHeaders([
                'xi-api-key' => $this->elevenlabApi,
            ])->delete($this->getElevenlabUrl . 'v1/voices/' . $voice->type);

            if ($response->successful()) {
                // Delete from local database
                $this->voiceTypeRepository->deleteVoiceType($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Voice đã được xóa thành công.'
                ]);
            } else {
                Log::error('Failed to delete voice from Eleven Labs:', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa voice từ hệ thống.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error deleting voice:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa voice.'
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:ai_generated_medias,id',
        ]);
        try {
            $message_id = $request->id;
            $response = $this->aIAudioService->deleteMessageById($message_id);

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được xóa thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting message:', ['error' => $e->getMessage()]); // Ghi log lỗi
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa tin nhắn.'
            ], 500);
        }
    }

    public function convertLanguage($fullLanguageCode)
    {
        $languageParts = explode('-', $fullLanguageCode);
        return strtolower($languageParts[0]); // Lấy phần đầu tiên của mã ngôn ngữ
    }
    public function cloneVoice(Request $request) {
        $user = Auth::user();
        // Validate input
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            // 'files'=> 'required|file|mimes:mp3,wav,audio/wav,audio/mp3'
        ]);

        $name = $request->input('name');
        $description = $request->input('description');
        $files = $request->file('files');

        $labels= json_encode([
            "accent" =>"standard",
            "age"=>"middle_aged",
            "language"=>"vi",
        ]);

        try {
            // Convert audio file to mp3 format
            $extension = $files->extension();
            Log::info('Audio extension: '.$extension);
            try {
                if (in_array($extension, ['webm', 'mp4','m4a'])) {
                    Log::info('Bắt đầu chuyển đổi audio: ' . $extension);
                    $content = FFmpegHelper::convertToMp3Content2($files);
                    Log::info('Chuyển đổi audio thành công');
                } else {
                    $content = file_get_contents($files->getRealPath());
                    if (!$content) {
                        throw new Exception("Không thể đọc nội dung file âm thanh");
                    }
                }
            } catch (Exception $e) {
                Log::error('Lỗi khi chuyển đổi âm thanh: ' . $e->getMessage());
                return response()->json(['error' => 'Error converting audio to mp3'], 500);
            }
            $data = [
                'name' => $name,
                'description' => $description,
                'remove_background_noise'=>true,
                'labels'=>$labels,
            ];
            $response = Http::withHeaders([
                'xi-api-key' => $this->elevenlabApi,
            ])
            ->attach('files', $content, $files->getClientOriginalName())
            ->post($this->getElevenlabUrl . 'v1/voices/add', $data);

            if ($response->successful()) {
                $result = $response->json();
                $voice_id = $result['voice_id'];

                if ($voice_id) {
                    $voice_generated = $this->textToSpeechWithVoiceId(
                        new Request([
                            'voice_id' => $voice_id,
                            'language' => 'vi',
                            'text' => $this->sample_text,
                            'isSaveDb' => false,
                        ]),
                    );

                    $voice_generated = $voice_generated->getData();
                    // save data to database
                    $result = $this->voiceTypeRepository->create([
                        'name' => $name,
                        'type' => $voice_id,
                        'demo' => $voice_generated->path,
                        'user_id' => $user->id,
                        'model'=>'cloned'
                    ]);
                    //get voice by id
                    $voice = $this->voiceTypeRepository->find($result->id);
                    return response()->json([
                        'status' => true,
                        'data' => $voice
                    ], 200);
                }
                // Trừ tiền credit user
                // Auth::user()->chargeCredit('ai-audio', null, null, null, null, 12, 12);
            }
            else{
                Log::error('Error: Received HTTP status code {$response->status()}');
                Log::error('Response: ' . $response->body());
                return response()->json(['success' => false, 'message' => 'Failed to generate voice'], $response->status());
            }
        } catch (\Throwable $th) {
            Log::error('Error message:', ['error' => $th->getMessage()]); // Ghi log lỗi
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình clone voice.'
            ], 500);
        }
    }

    public function textToSpeechWithVoiceId(Request $request) {
        try {
            $user = auth()->user();
            $request->validate([
                'voice_id' => 'required|string',
                'language' => 'required|string',
                'text' => 'required|string',
                'isSaveDb' => 'nullable|boolean',
                'screen_id' => 'nullable|integer',
                'feature_id' => 'nullable|integer',
            ]);
            $isSaveDb = $request->input('isSaveDb', true);
            $voice_id = $request->input('voice_id');
            $language = $request->input('language');
            $text = $request->input('text');
            $screen_id = $request->input('screen_id') ?? 5;
            $feature_id = $request->input('feature_id') ?? 19;
            $dataRequest = [
                "text" => $text,
                "model_id" => "eleven_turbo_v2_5",
                "language_code" => $language ?? "vi",
                "voice_settings" => [
                    "stability" => 0.5,
                    "similarity_boost" => 0.75,
                    "style" => 0,
                ],
                "pronunciation_dictionary_locators" => [],
                "seed" => 1,
                "previous_text" => "",
                "next_text" => "",
                "previous_request_ids" => [],
                "next_request_ids" => [],
                "use_pvc_as_ivc" => false,
                "apply_text_normalization" => "auto",
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'xi-api-key' => $this->elevenlabApi,
            ])->post($this->getElevenlabUrl . "v1/text-to-speech/$voice_id", $dataRequest);

            if ($response->failed()) {
                Log::error("Error: Received HTTP status code {$response->status()}");
                Log::error("Response: " . $response->body());
                return response()->json(['success' => false, 'message' => 'Failed to generate speech'], $response->status());
            }

            // Upload to S3
            $audioContent = $response->body();
            $fileName = uniqid('audio_', true) . '.mp3';
            $s3Path = 'ai_audio/' . $fileName;
            Storage::disk('s3')->put($s3Path, $audioContent);

            // Get the URL of the uploaded file
            $s3Url = Helpers::preSignedS3Url($s3Path);

            if ($isSaveDb) {
                $data = [
                    'user_id' => auth('web')->id(),
                    's3_url' => $s3Path,
                    'description' => $text,
                    'voice_type' => $voice_id,
                    'type' => 'audio'
                ];
                $this->aIMediaService->storeMedia($data);
            }
            Auth::user()->chargeCredit('elevenlab', 'Elevenlab', strlen($text), null, null, $screen_id, $feature_id);
            return response()->json([
                'success' => true,
                'message' => 'Text-to-speech successful',
                'data' => $s3Url,
                'path' => $s3Path,
                'credit' => $user->credit ?? 0,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error message:', ['error' => $e->getMessage()]); // Ghi log lỗi
            return response()->json([
                'success' => false,
                'message' => 'Error occurred during text-to-speech conversion'
            ], 500);
        }
    }
    public function textToSpeechWithGoogle(Request $request){
        $request->validate([
            'text' => 'required|string',
        ]);
        $text = $request->input('text');
        $apiKey = config('services.google_text_to_speech.api_key');
        $url = config('services.google_text_to_speech.url');
        // post api
        $resp = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Goog-Api-Key' => $apiKey,
        ])->post($url, [
                    'input' => [
                        'text' => $text
                    ],
                    'voice' => [
                        'languageCode' => 'vi-VN',
                        'name' => 'vi-VN-Neural2-A'
                    ],
                    'audioConfig' => [
                        'audioEncoding' => 'MP3',
                        'pitch' => 0,
                        'speakingRate' => 1,
                    ]
                ]);

        if ($resp->failed()) {
            Log::error($resp->json());
            return false;
        }

        $audio = base64_decode($resp->json()['audioContent']);

        $path = config('services.google_text_to_speech.folder') . "/" . "answers/" . Str::random(40) . time() . '.mp3';

        Storage::disk('s3')->put($path, $audio);
        $path = Helpers::preSignedS3Url($path);

        return response()->json([
            'success' => true,
            'data' => $path
        ]);
    }

    public function getVoiceByID($voice_id) {
        $user = Auth::user();
        $response  = Http::withHeaders([
            'xi-api-key'=> $this->elevenlabApi,
        ])->get("{$this->getElevenlabUrl}v1/voices/{$voice_id}" , [
            "with_settings" => true,
        ]);
        if($response->successful()){
            $voiceData = $response->json();
            return $voiceData;
        } else {
            throw new \Exception('Failed to get voice_id');
        }
    }

    public function getAllVoices() {
        $user = Auth::user();
        $response  = Http::withHeaders([
            'xi-api-key'=> $this->elevenlabApi,
        ])->get("https://api.elevenlabs.io/v1/voices" , [
            "with_settings" => true,
        ]);
        if($response->successful()){
            $voiceData = $response->json();
            return response()->json([
                "status"=>"success",
                "data"=>$voiceData
            ], 200);
        } else {
            return response()->json(['message' => 'Failed to get voice_id'], 500);
        }
    }

    public function uploadToS3(Request $request) {
        try {
            $audio_file = $request->file('audio_file');
            if ($audio_file) {
                if (!file_exists(storage_path('app/public/audio'))) {
                    mkdir(storage_path('app/public/audio'), 0777, true);
                }
                $audio_file = $audio_file->store('audio', 'public');
                Storage::disk('s3')->put($audio_file, Storage::disk('public')->get($audio_file));
                return response()->json(['success' => true, 's3_url' =>  Helpers::preSignedS3Url($audio_file)]);
            }
        } catch(\Exception $ex) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện']);
        }
    }
}

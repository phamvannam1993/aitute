<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Constants\Sender;
use App\Exceptions\UsageLimitExceededException;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Services\ChatClaudeService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\AIVideoService;
use App\Jobs\GenerateVideoAudio;
use App\Services\AIChatService;
use App\Services\AIVirtualService;
use App\Services\ChatGPTService;
use App\Services\DocumentReaderService;
use App\Services\StorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\CreditHistoryService;
use App\Services\AIAudioService;
use App\Jobs\MergeAudioVideoMC;
use Exception;

class AIVirtualController extends Controller
{
    private $storageService;
    private $aIVirtualService;
    private $creditHistoryService;
    private $aIVideoService;
    private $aIAudioService;
    private $aiAudioController;

    public function __construct(StorageService $storageService, AIVirtualService $aIVirtualService, CreditHistoryService $creditHistoryService, AIVideoService $aIVideoService , AIAudioService $aIAudioService, AIAudioController $aiAudioController)
    {
        $this->storageService = $storageService;
        $this->aIVirtualService = $aIVirtualService;
        $this->creditHistoryService = $creditHistoryService;
        $this->aIVideoService = $aIVideoService;
        $this->aIAudioService = $aIAudioService;
        $this->aiAudioController = $aiAudioController;
    }
    public function index(Request $request)
    {
        $listVoiceClone = $this->aIAudioService->getListVoiceClones();
        $user = auth()->user();
        $listAllVoice = $this->aIAudioService->getListVoice($user->id);
        return Inertia::render('Client/AIVirtual/Index', ['listVoiceClone' => $listVoiceClone,
            'user' => $user,
            'listAllVoice' => $listAllVoice
        ]);
    }
    public function historyView(Request $request)
    {
        $list = $this->aIVirtualService->getHistories();
        return Inertia::render('Client/AIVirtual/History', ['list' => $list]);
    }
    public function historyDetail(Request $request, $id)
    {
        $history = $this->aIVirtualService->findOne($id);
        if(!empty($history->transcription_url)) {
            $history->result_url = $history->transcription_url;
        }
        if(!empty($history->s3_url_video_audio)) {
            $history->result_url = $history->s3_url_video_audio;
        }
        $list = $this->aIVirtualService->getHistories(4);
        return Inertia::render('Client/AIVirtual/HistoryDetail', ['record' => $history, 'list' => $list]);
    }
    public function getVideoProcessing(Request $request)
    {
        $item = $this->aIVirtualService->getVideoProcessing();
        return response()->json($item);
    }

    public function detail($id)
    {
        $item = $this->aIVirtualService->findOne($id);
        if(!empty($item->transcription_url)) {
            $item->result_url = $item->transcription_url;
        }
        if(!empty($item->s3_url_video_audio)) {
            $item->result_url = $item->s3_url_video_audio;
        }
        if($item) {
            return response()->json(["data" => $item, "success" => true]);
        } else {
            return response()->json(["message" => "Không tồn tại dữ liệu", "success" => false]);
        }
    }

    public function generateVideo(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'content' => 'required|string',
                'voice' => 'required|string',
                'avatar' => 'required|file|mimes:jpg,jpeg,png|max:10240'
            ], [
                'content.required' => 'Mô tả nội dung video là bắt buộc',
                'content.string' => 'Mô tả nội dung video phải là một chuỗi ký tự',
                'voice.required' => 'Giọng nói là bắt buộc',
                'voice.string' => 'Giọng nói phải là một chuỗi ký tự',
                'avatar.required' => 'Ảnh đại diện là bắt buộc',
                'avatar.file' => 'Ảnh đại diện phải là một tệp',
                'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, hoặc png',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ], 400);
            }
            $user = auth()->user();
            $user->decrementUsage(config('constant.assistant_types.mc'));
            $apiKey = env('D_ID_API_KEY');


            $file = $request->file('avatar');
            if ($file) {
                $result = $this->storageService->putToS3($file, folderName: 'virtual-image');
                $request->validate([
                    'content' => 'required|string',
                ]);

                $content = $params['content'];
                $params['avatar_url'] = $result['path'];

                $data = json_encode([
                    "source_url" => $result['url'],
                    "script" => [
                        "type" => "text",
                        "input" => $content,
                        "provider" => [
                            "type" => "microsoft",
                            "voice_id" => $params['voice']
                        ]
                    ]
                ]);

                $ch = curl_init('https://api.d-id.com/talks');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Basic ' . $apiKey,
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $didResponse = curl_exec($ch);

                if (curl_errno($ch)) {
                    return response()->json([
                        'success' => false,
                        'error' => curl_error($ch)
                    ]);
                }

                curl_close($ch);

                $didData = json_decode($didResponse, true);
                if (!isset($didData['id'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Video creation failed. No ID returned.'
                    ]);
                }

                 // Lấy thông tin talk ngay sau khi tạo
                $talkInfo = $this->getTalkInfo($didData['id']);
                Log::info('talkInfo ' . json_encode($talkInfo));
                Log::info(message: $didData);

                $params['mc_virtual_id'] = $didData['id'];
                $params['created_by'] = $didData['created_by'];
                $params['status'] = $didData['status'];
                $params['duration'] = $talkInfo['duration'] ?? null;
                $params['info_json'] = json_encode($talkInfo); // Lưu toàn bộ thông tin

                $record = $this->aIVirtualService->store($params);
//                $this->handleUsageCredit();
                Auth::user()->chargeCredit('mc_virtual', 'd-id', $talkInfo['duration'] ?? 1, null, null, 5, 5);
                return response()->json([
                    'message' => 'Video is generating...',
                    'credit' => $user->credit ?? 0 ,
                    'talkInfo' => $talkInfo
                    ]);
            }
            return response()->json(['error' => 'No file uploaded'], 400);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error generating generateVideo mc-virtual: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function getTalkInfo($talkId)
    {
        try {
            $apiKey = env('D_ID_API_KEY');

            $ch = curl_init("https://api.d-id.com/talks/{$talkId}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $apiKey,
                'Content-Type: application/json',
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                Log::error('Error getting talk info: ' . curl_error($ch));
                return null;
            }

            curl_close($ch);

            return json_decode($response, true);

        } catch (\Exception $e) {
            Log::error('Error in getTalkInfo: ' . $e->getMessage());
            return null;
        }
    }

    private function getClipInfo($clipId)
    {
        try {
            $apiKey = env('D_ID_API_KEY');
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.d-id.com/clips/{$clipId}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    'Authorization: Basic ' . $apiKey
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("Error getting clip info: " . $err);
                return null;
            }

            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::error("Exception getting clip info: " . $e->getMessage());
            return null;
        }
    }

    public function generateClips(Request $request)
    {
        try {
            $user = auth()->user();
            $user->decrementUsage(config('constant.assistant_types.mc'));
            // $request->validate([
            //     'script' => [
            //         'required',
            //     ],
            //     'presenter' => [
            //         'required',
            //     ],
            //     'voice' => ['required'],
            //     'background_style' => ['required']
            // ]);
            $params = $request->all();

            $validator = Validator::make($request->all(), [
                'script' => 'required|string',
                'presenter' => 'required|string',
                'voice' => 'required|string',
                'background_style' => 'required|string',
                'presenter_url' => 'required|string',
            ], [
                'script.required' => 'Kịch bản là bắt buộc.',
                'presenter.required' => 'Người trình bày là bắt buộc.',
                'voice.required' => 'Giọng nói là bắt buộc.',
                'background_style.required' => 'Kiểu nền là bắt buộc.',
                'presenter_url.required' => 'Người trình bày là bắt buộc.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ], 400);
            }

            $apiKey = env('D_ID_API_KEY');

            $file = $request->file('background_url');
            if ($file) {
                $result = $this->storageService->putToS3($file, folderName: 'virtual-image');
                Log::info('debug abcedf: ' . json_encode($result));
                $params['background_url'] = $result['path'];
            }

            $config = [
                'result_format' => 'mp4' // Mặc định là mp4
            ];
            $background = [];

            if ($params['background_style'] === 'transparent' || $params['background_style'] === 'custom') {
                $background['color'] = '#FFFFFF';
            } elseif ($params['background_style'] === 'color') {
                $background['color'] = $params['background_color'];
            }

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.d-id.com/clips",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'presenter_id' => $params['presenter'],
                    'script' => [
                        'type' => 'text',
                        'subtitles' => 'false',
                        'provider' => [
                            'type' => 'microsoft',
                            'voice_id' => $params['voice']
                        ],
                        'input' => $params['script'],
                        'ssml' => 'false'
                    ],
                    'config' => $config,
                    'background' => $background,
                    'presenter_config' => [
                        'crop' => [
                            'type' => 'wide'
                        ]
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    'Authorization: Basic ' . $apiKey,
                    "content-type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {

                Log::error("cURL Error #:" . $err);
                return response()->json([
                    'success' => false,
                    'error' => 'Video creation failed. No ID returned.'
                ]);
            }
            $data = json_decode($response, true);
            if (!isset($data['id'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Video creation failed. No ID returned.'
                ]);
            }
            // Lấy thông tin talk ngay sau khi tạo
            $clipInfo = $this->getClipInfo($data['id']);
            Log::info('clipInfo ' . json_encode($clipInfo));

            $presenterFile = 'virtual-image/'. uniqid('presenter_') . '.jpg';
            Storage::disk('s3')->put($presenterFile, file_get_contents($params["presenter_url"]));

            $mcVirtualData = [
                'avatar_url' => $presenterFile,
                'content' => $params['script'] ?? null,
                'voice' => $params['voice'] ?? null,
                'type' => 'clips',
                'mc_virtual_id' => $data['id'],
                'status' => $data['status'] ?? 'pending',
                'audio_id' => $params['audio_id'] ?? null,
                'background_url' => $params['background_url'],
                'duration'  => $clipInfo['duration'] ?? null,
                'info_json' => json_encode($clipInfo) ?? null,
            ];
            $record = $this->aIVirtualService->store($mcVirtualData);
//            $this->handleUsageCredit();
            Auth::user()->chargeCredit('mc_virtual', 'd-id', $clipInfo['duration'] ?? 1, null, null, 5, 5);
            return response()->json([
                'message' => 'Video is generating...',
                'credit' => $user->credit ?? 0,
                'clipInfo' => $clipInfo
            ]);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error generating generateClips mc-virtual: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generateClipsHeyGen(Request $request)
    {
        try {
            $user = auth()->user();
            $user->decrementUsage(config('constant.assistant_types.mc'));
            $params = $request->all();

            $validator = Validator::make($request->all(), [
                'voice' => 'required|string',
                'background_style' => 'required|string',
                'background_url' => 'sometimes|nullable|file|mimes:jpg,jpeg,png|max:10240',
                'aspect_ratio' => 'required|string|in:1:1,16:9,9:16',
            ], [
                'voice.required' => 'Giọng nói là bắt buộc.',
                'background_style.required' => 'Kiểu nền là bắt buộc.',
                'background_url'=>"Hình ảnh nền phải thuộc định dạng: jpg, jpeg, png",
                'aspect_ratio.required' => 'Tỷ lệ khung hình là bắt buộc.',
                'aspect_ratio.in' => 'Tỷ lệ khung hình không hợp lệ.',
            ]);
            if($params['type_image'] == 'custom'){
                $avatar = $request->file('avatar');
                if(!$avatar){
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'avatar' => ['Hình ảnh là bắt buộc']
                        ]
                    ], 400);
                }
            }
            else if($params['type_image'] == 'ai'){
                $avatar_id = $params['avatar_id'] ?? null;
                $talking_photo_id = $params['talking_photo_id'] ?? null;
                if(!$avatar_id & !$talking_photo_id){
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'avatar_id' => ['Hình ảnh là bắt buộc']
                        ]
                    ], 400);
                }
            }
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ], 400);
            }

            $file = $request->file('background_url');
            // if file is gif the throw error
            if ($file) {
                $result = $this->storageService->putToS3($file, folderName: 'virtual-image');
                $params['background_url'] = $result['path'];
            }

            $background = [];

            if ($params['background_style'] === 'transparent' || $params['background_style'] === 'custom') {
                $background['color'] = '#FFFFFF';
            } elseif ($params['background_style'] === 'color') {
                $background['color'] = $params['background_color'];
            }
            $file = $request->file('background_url');
            $assetId = null;
            if ($file) {
                $result = $this->aIVideoService->uploadAssetToHeyGen($file);
                $assetId = $result->data->id;
            }
            $audio_url = $params['audio_url'] ?? null;
            //Tỷ lệ 1:1 (Vuông): 1080 x 1080 (Instagram) Tỷ lệ 16:9 (Ngang): 1920 x 1080 (Full HD) Tỷ lệ 9:16 (Dọc): 1080 x 1920 (Full HD dọc) xin hãy sửa lại code. Không thay đổi aspect_ratio nhưgn thay đổi dimension theo độ phân giải trên
            $dimension = [
                "width" => 1920,
                "height" => 1080
            ];
            if($params['aspect_ratio'] == '1:1'){
                $dimension = [
                    "width" => 1080,
                    "height" => 1080
                ];
            }
            else if($params['aspect_ratio'] == '9:16'){
                $dimension = [
                    "width" => 1080,
                    "height" => 1920
                ];
            }
            else if($params['aspect_ratio'] == '16:9'){
                $dimension = [
                    "width" => 1920,
                    "height" => 1080
                ];
            }
            $baseData = [
                "caption" => filter_var($params['caption'] ?? false, FILTER_VALIDATE_BOOLEAN),
                "dimension" => $dimension,
            ];

            $type_image = $params['type_image'] ?? null;
            //case: Chọn hình của bạn
            $talking_photo_id = null;
            $avatarFile = $request->file('avatar');
            if($type_image == 'custom' && $avatarFile){
                try {
                    $result_talking_photo = $this->aIVideoService->uploadTaklingPhotoHeyGen($avatarFile);
                    $talking_photo_id = $result_talking_photo->data->talking_photo_id;
                    $baseData['video_inputs'] = [
                        [
                            "character" => [
                                "type" => "talking_photo",
                                "talking_photo_id" => $talking_photo_id
                            ],
                        ]
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'avatar' => ['Nội dung ảnh không phù hợp. Vui lòng chọn ảnh khác có khuôn mặt rõ ràng.']
                        ]
                    ], 422);
                }
            }
            else{
                $baseData['video_inputs'] = [
                    [
                        "character" => [
                            "type" => isset($params['avatar_id']) && $params['avatar_id'] ? "avatar" : "talking_photo",
                            // Dynamically set key and value
                            (isset($params['avatar_id']) && $params['avatar_id'] ? "avatar_id" : "talking_photo_id") =>
                                $params['avatar_id'] ?? $params['talking_photo_id'] ?? null
                        ],

                    ]
                ];
            }
            $voice = $params['voice'];
            if(!$audio_url){
                $audio_url = $this->handleTextToSpeech($voice, $params['script'],5,27);
            }
            if($audio_url){
                $baseData['video_inputs'][0]['voice'] = [
                    "type" => "audio",
                    "audio_url" => $audio_url,
                ];
            }else{
                $baseData['video_inputs'][0]['voice'] = [
                    "type" => "text",
                    "input_text" => $params['script'],
                    "voice_id" => $voice
                ];
            }
            // Add background if specified
            if ($assetId) {
                $baseData['video_inputs'][0]['background'] = [
                    'type' => 'image',
                    'image_asset_id' => $assetId
                ];
            } elseif (!empty($background['color'])) {
                $baseData['video_inputs'][0]['background'] = [
                    'type' => 'color',
                    'value' => $background['color']
                ];
            }
            $retryTime = 3;
            $countRetry = 0;
            $responseVideo = null;
            Log::info('baseData: ' . json_encode($baseData));
            do {
                try {
                    $responseVideo = $this->aIVideoService->createAnAvatarVideo(json_encode($baseData));
                    if (isset($responseVideo->data->video_id)) {
                        break; // Exit loop if video creation is successful
                    }
                } catch (\Throwable $th) {
                    Log::error('Error creating video heygen: ' . $th->getMessage());
                    sleep(20);
                    $countRetry++;
                }
            } while ($countRetry < $retryTime);

            if (!isset($responseVideo->data->video_id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Có lỗi xảy ra trong quá trình tạo video. Vui lòng thử lại sau.'
                ]);
            }
            $resTalkInfo = $this->aIVideoService->retrieveVideo($responseVideo->data->video_id);
            $talkInfo = $resTalkInfo->data;
            $status = Helpers::mapStatusFromHeygenToDiD($talkInfo->status);
            $modelHeygen = isset($params['avatar_id']) ? AIModel::HeygenVideoAvatar : AIModel::HeygenPhotoAvatar;
            $mcVirtualData = [
                'content' => $params['script'] ?? "",
                'voice' => $params['voice'] ?? null,
                'type' => 'clips',
                'mc_virtual_id' => $talkInfo->id,
                'status' => $status,
                'audio_id' => $params['audio_id'] ?? null,
                'background_url' => $params['background_url'] ?? null,
                'duration'  => $talkInfo->duration ?? null,
                'info_json' => json_encode($baseData) ?? null,
                'model' => $modelHeygen
            ];
            $record = $this->aIVirtualService->store($mcVirtualData);
            return response()->json([
                'message' => 'Video created',
                'credit' => $user->credit ?? 0,
                'data' => $record
            ],200);
        } catch (\Exception $e) {
            Log::error('Error generating generateClips mc-virtual: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra trong quá trình tạo video. Vui lòng thử lại sau.'], 500);
        }
    }
    public function historyApi(Request $request): JsonResponse
    {
        $list = $this->aIVirtualService->getHistoriesV2();
        $listNotDone =  $this->aIVirtualService->getAllNotDone();
        $itemIdNotDones = [];
        foreach ($listNotDone as $index => $item) {
            if($item->status != "fail") {
                if(strtotime($item->created_at) < (time() - 180*60)) {
                   $this->aIVirtualService->updateBy($item->id, ["status" => 'fail']);
                }
            }
            if ($item->status != 'done') {
                $itemIdNotDones[] = $item->id;
            }
        }

        foreach($list as $index => $item) {
            if (str_contains($item->avatar_url, "https://adbox-staging.s3.ap-northeast-1.amazonaws.com")) {
                $avatarUrl = $item->avatar_url;
                $avatarUrl = str_replace("https://adbox-staging.s3.ap-northeast-1.amazonaws.com/","",$avatarUrl);
                $avatarUrl = explode(".jpg", $avatarUrl);
                if(count($avatarUrl) == 2) {
                    $item->avatar_url =  $avatarUrl[0].".jpg";
                }
            }
        }
        return response()->json(['list' => $list, 'itemIdNotDones' => $itemIdNotDones], 200);
    }
    public function handleTextToSpeech($voice_id, $text, $screen_id = 0, $feature_id = 0) {
        $voice = $this->aIAudioService->getVoiceTypeByType($voice_id);
        if(!$voice){
            return null;
        }
        if($voice['model'] == 'cloned'){
            $result = $this->aiAudioController->textToSpeechWithVoiceId(new Request([
                'voice_id' => $voice_id,
                'language' => 'vi',
                'text' => $text,
                'isSaveDb' => false,
                'screen_id' => $screen_id,
                'feature_id' => $feature_id,
            ]));
            return $result->getData()->data;
        }
        else{
            $result = $this->aiAudioController->textToSpeechV2(new Request([
                'voice' => $voice_id,
                'text' => $text,
                'screen_id' => $screen_id,
                'feature_id' => $feature_id,
            ]));
            return $result->getData()->data->s3_url;
        }

    }
    public function ajaxFetchVideo($id) {
        $result = $this->aIVirtualService->findOne($id);
        if($result && $result->status != "done" && $result->status != "fail") {
            if($result->model == AIModel::getModel('HeygenVideoAvatar') || $result->model == AIModel::getModel('HeygenPhotoAvatar') || $result->model == 'heygen') {
                $this->aIVirtualService->fetchVideoHeyGen($result);
            } else {
                $this->aIVirtualService->fetchVideo($result);
            }
        }
        return response()->json(['result' => $result], 200);
    }

    public function history(Request $request)
    {
        $list = $this->aIVirtualService->getHistories();
        return Inertia::render('Client/AIVirtual/History', ['list' => $list]);
    }

    public function getHistories()
    {
        try {
            $list = $this->aIVirtualService->getHistories();
            return response()->json(['data' => $list], 200);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mergeVoice(Request $request)
    {
        Log::info('Merge video step 1: start');
        $request->validate([
            'id' => [
                'required',
            ],
            'ai_generated_media_id' => [
                'required',
            ]
        ]);
        $params = $request->all();
        Log::info('Merge video step 2: validate successfully - ' . json_encode($params));
        $record = $this->aIVirtualService->mergeVideo($params);
        if ($record) {
            return response()->json(['message' => 'Áp dụng thành công...']);
        }
        return response()->json(['error' => 'Đã có lỗi xảy ra.'], 400);
    }

    public function delete($id)
    {
        return $this->aIVirtualService->delete($id);
    }

    public function suggestContent(Request $request)
    {
        $prompt = $request->input('content');
        $prompt .= "\nHãy tạo nội dung phù hợp cho các phân cảnh video dựa trên yêu cầu được cung cấp. Vui lòng trả về kết quả và không giải thích thêm.";
        Log::info($prompt);
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function suggestContentImageAndVideo(Request $request)
    {
        $prompt = $request->input('content');
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $prompt .= 'Vui lòng trả về kết quả ngắn gọn dưới 30 từ phù hợp với prompt tạo ảnh hoặc video và không giải thích thêm.';
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function suggestContentImage(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy tạo ra một đoạn prompt ngắn gọn để tạo ra hình ảnh hoặc tranh minh họa dựa trên chủ đề "';
        $prompt .= $content;
        $prompt .= '". Phân tích chủ đề này và xác định các yếu tố như màu sắc, không gian, cảm xúc và bối cảnh. Dựa vào đó, tạo ra một ý tưởng hình ảnh hoặc tranh minh họa mà người xem có thể dễ dàng hình dung, phù hợp với chủ đề đã cho. Đảm bảo rằng kết quả chỉ gồm ý tưởng về hình ảnh mà không cần giải thích thêm.';
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt,   1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function suggestContentAudio(Request $request)
    {
        $prompt = 'Vui lòng trả về một đoạn văn ngắn gọn về ';
        $prompt .= $request->input('content');
        $prompt .= ' dưới 10000 từ, tuyệt đối không được vượt quá 10000 từ, đoạn văn này được dùng để chuyển đổi thành âm thanh nên phải dễ nghe và không giải thích thêm.';
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
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

    public function suggestContentFilm(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy tạo ra một ý tưởng hoặc đoạn prompt ngắn gọn cho một đoạn video dựa trên chủ đề "';
        $prompt .= $content;
        $prompt .= '". Phân tích chủ đề này và xác định các yếu tố như cảm xúc, thể loại (hài hước, tình cảm, hành động, kịch tính), bối cảnh. Nếu chủ đề không đề cập đến con người, hãy tập trung mô tả phong cảnh hoặc bối cảnh mà không cần thêm nhân vật. Nếu chủ đề liên quan đến con người, hãy mô tả ý tưởng bao gồm nhân vật và cảm xúc liên quan. Đảm bảo kết quả ngắn gọn (phải ít hơn 255 kí tự và tuyệt đối không được thêm bất kì câu từ nào ngoài prompt ý tưởng), chỉ bao gồm ý tưởng video và không cần giải thích thêm.';
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function suggestNarrativeFilmContent(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy tạo một đoạn thuyết minh ngắn gọn cho một đoạn video dựa trên chủ đề "';
        $prompt .= $content;
        $prompt .= '". Đoạn thuyết minh này nên mô tả tổng quan về câu chuyện của đoạn video, bao gồm bối cảnh. Nếu chủ đề không đề cập đến con người, hãy tập trung mô tả cảnh vật và không thêm nhân vật. Nếu chủ đề liên quan đến con người, hãy mô tả thuyết minh bao gồm nhân vật và câu chuyện của họ. Đảm bảo đoạn thuyết minh ngắn gọn (phải ít hơn 255 kí tự và tuyệt đối không được thêm bất kì câu từ nào ngoài prompt ý tưởng) và hấp dẫn mà không cần giải thích chi tiết thêm.';
        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function suggestVirtualMC(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy viết một đoạn lời thoại cho một MC chuyên nghiệp dựa trên chủ đề "';
        $prompt .= $content;
        $prompt .= '". Lời thoại nên ngắn gọn, dễ hiểu, dễ nghe, và mang phong cách của một MC chuyên nghiệp. Đảm bảo lời thoại không quá 8000 ký tự và phù hợp với bối cảnh. Nếu chủ đề thiên về cảm xúc như lễ kỷ niệm, lời thoại nên truyền tải sự ấm áp và kết nối. Nếu là một sự kiện lớn như hội nghị hay giải trí, lời thoại cần truyền đạt sự phấn khích và năng lượng. Nếu là một sự kiện trang trọng, lời thoại cần trang nhã và lịch sự. Đoạn văn chỉ bao gồm lời thoại MC, tuyệt đối chỉ bao gồm lời thoại của MC và không được giải thích thêm.';

        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    private function handleUsageCredit() {
        $user = auth()->user();
        $totalCredit = Helpers::calculateCredit('d-id', 0, 0, 60, 0);

        if ($user->credit >= $totalCredit) {
            $user->credit -= $totalCredit;
        } else {
            $user->credit = 0;
        }
        $user->save();
        $this->creditHistoryService->create(
            [
                "user_id" => $user->id,
                "screen_id" => 5,
                "credit" => $totalCredit,
                'feature_id' => 5,
            ]
        );
    }

    public function getHeygenAvatars()
    {
        try {
            $avatars = $this->aIVideoService->getHeygenAvatars();
            return response()->json(['success' => true, 'data' => $avatars]);
        } catch (\Exception $e) {
            Log::error('Error fetching HeyGen avatars: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to fetch avatars'], 500);
        }
    }

    public function getHeygenTalkingPhotos()
    {
        try {
            $talkingPhotos = $this->aIVideoService->getHeygenTalkingPhotos();
            return response()->json(['success' => true, 'data' => $talkingPhotos]);
        } catch (\Exception $e) {
            Log::error('Error fetching HeyGen talking photos: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to fetch talking photos'], 500);
        }
    }

    public function suggestContentForCreatomate(Request $request)
    {
        $content = $request->input('content');
        $prompt = 'Hãy viết lại một văn bản ngắn cho chủ đề sau: "';
        $prompt .= $content;
        $prompt .= '". Hãy viết ngắn gọn, dễ hiểu, tuyệt đối chỉ bao gồm văn bản tối đa không quá 30 từ và không được giải thích thêm.';

        $screen_id = isset($request['screen_id']) ? $request['screen_id'] : 0;
        $response = app(ChatClaudeService::class)->sendMessageCustom($prompt, 1, $screen_id);
        $body = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return response()->json([
            'data' => $body['content'][0]['text'] ?? ''
        ]);
    }

    public function generateVideoWithTranscriptionWithSegmind(Request $request)
    {
        $response = $this->aIVirtualService->generateVideoWithTranscriptionWithSegmindForMC($request->all());
        return response()->json($response);
    }

    public function mergeAudioVideoQueue(Request $request)
    {
        $audio_file = $request->file('audio_file');
        $mcId = $request->mc_id;
        if ($audio_file) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_path = $audio_file->store('audio', 'public');
            $this->aIVideoService->updateById($mcId, ["is_upload_audio" => false]);
            Storage::disk('s3')->put($audio_path, file_get_contents(storage_path('app/public/'.$audio_path)));
            MergeAudioVideoMC::dispatch($mcId, $audio_path)->onQueue("merge-audio-video-mc");
            // $this->aIVideoService->mergeAudioVideo($aiAudioId, $audio_path);
            $this->cleanupFiles([
                $audio_path,
            ]);
            return  response()->json(['message' => "Nhạc nền đang trong quá trình tạo", "success" => true]);
        } else {
            return response()->json(['message' => "audio không tồn tại", "success" => false], 400);
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
}

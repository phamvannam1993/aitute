<?php

namespace App\Http\Controllers\Client;

use App\Constant\Activities;
use App\Enums\SlideTypeEnum;
use App\Exceptions\UsageLimitExceededException;
use App\Helper\FFmpegHelper;
use App\Helper\GoogleTextToSpeechHelper;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TextToVideo\StoreSlideAiRequest;
use App\Models\Activity;
use App\Models\Activityable;
use App\Models\Admin;
use App\Models\AIPresenter;
use App\Models\HistoryCreateAISlide;
use App\Models\SlideCloud;
use App\Rules\MaxFilePageRule;
use App\Services\AIVideoService;
use App\Services\AIVirtualService;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\DocumentReaderService;
use App\Services\OneDriveService;
use App\Services\PHPPresentationService;
use App\Services\SlideAiComponentService;
use App\Services\SlideAiTemplateService;
use App\Services\StorageService;
use App\Services\TextToSpeechService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\Video;
use App\Models\ShortVideo;
use Inertia\Response;
use Illuminate\Http\UploadedFile;
use DevDojo\GoogleImageSearch\ImageSearch;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Services\CreditHistoryService;

class TextToVideoController extends Controller
{
    protected $apiToken;

    public function __construct(
        protected ChatClaudeService $chatClaudeService,
        // protected PHPPresentationService $pHPPresentationService,
        // protected OneDriveService $oneDriveService,
        protected ChatGPTService $chatGPTService,
        protected SlideAiTemplateService $slideAiTemplateService,
        protected SlideAiComponentService $slideAiComponentService,
        protected StorageService $storageService,
        protected AIVideoService $aiVideoService,
        protected TextToSpeechService $textToSpeechService,
        protected AIVirtualService $aIVirtualService,
        protected CreditHistoryService $creditHistoryService,
    ) {
        $this->apiToken = env('REPLICATE_API_TOKEN');
    }

    public function index(Request $request): Response
    {
        $imageUrl = $request->imageUrl;
        $xFades = Video::xFades();
        $positionCamera = ShortVideo::positionCamera();
        $xFadeArr = [];
        for($i = 0; $i < count($xFades); $i++) {
            if(isset($xFades[$i]["isDisabled"]) && $xFades[$i]["isDisabled"]) {
               continue;
            } else {
                $xFadeArr[] = $xFades[$i];
            }
        }
        return Inertia::render('Client/TextToVideo/Index', ['imageUrl' => $imageUrl, 'xFades' => $xFadeArr, 'positionCameras' => $positionCamera]);
    }

    private function send($event, $data)
    {
        try {
            echo "event: {$event}\n";
            echo 'data: ' . $data;
            echo "\n\n";
            ob_flush();
            flush();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    private $maxRetries = 3;
    private $retryDelay = 1000; // 1 giây
    public function createSlideStream(Request $request)
    {
        $fileName = $request->get('file_name');
        $topic = file_get_contents(storage_path('app/public/slides/' . $fileName));
        $numberOfSlides = $request->numberOfSlides ?? 5;

        // Trừ tiền credit user
        $res = Auth::user()->chargeCredit('text_to_video', null, $numberOfSlides, null, null, 4, 4);
        return response()->stream(
            function () use ($topic, $numberOfSlides) {
                $result_text = "";
                $retryCount = 0;

                while ($retryCount < $this->maxRetries) {
                    try {
                        $stream = $this->chatClaudeService->sendMessageStreamVideo($topic, $numberOfSlides);
                        foreach ($stream as $response) {
                            $text = $response->choices[0]->delta->content;
                            if (connection_aborted()) {
                                break 2;
                            }
                            $data = ['text' => $text];
                            $this->send("update", json_encode($data));
                            $result_text .= $text;
                            $last_stream_response = $response;
                        }
                        $this->send("update", "<END_STREAMING_SSE>");
                        logger($last_stream_response->usage->toArray());
                        break; // Thoát khỏi vòng lặp nếu thành công
                    } catch (RequestException $e) {
                        if ($e->getCode() == 529) {
                            $retryCount++;
                            if ($retryCount >= $this->maxRetries) {
                                $this->send("error", "Failed to generate slide stream after {$this->maxRetries} attempts due to rate limiting.");
                            } else {
                                $this->send("retry", "Rate limit exceeded. Retrying in " . ($this->retryDelay / 1000) . " seconds...");
                                usleep($this->retryDelay * 1000); // Đợi trước khi thử lại
                                $this->retryDelay *= 2; // Tăng thời gian đợi theo cấp số nhân
                            }
                        } else {
                            // Xử lý các lỗi khác
                            $this->send("error", "An error occurred: " . $e->getMessage());
                            break;
                        }
                    }
                }
            },
            200,
            [
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]
        );
    }

    public function createSlideStreamV2(Request $request)
    {
        $topic = $request->topic;
        $numberOfSlides = $request->numberOfSlides ?? 5;
        $totalPage = ceil($numberOfSlides/5) + 2;
        $user = Auth::user();
        $dataSlide = [];
        // Trừ tiền credit user
        $textCredit = "";

        for($i = 0; $i < $totalPage; $i++) {
            if(count($dataSlide) >= $numberOfSlides) {
                continue;
            }
            $slideNumber = ($i*5) + 1;
            $totalSlide = 5;
            if($i == $totalPage - 1) {
                $totalSlide = $numberOfSlides - $slideNumber;
            }
            $originalPrompt = 'Bạn đóng vai trò là 1 trợ lý AI thông minh có khả năng viết kịch bản video và tạo ra các phân cảnh bằng tiếng Việt.
                    Người dùng sẽ cung cấp một đoạn nội dung liên quan đến kịch bản video, hãy điều chỉnh bổ sung các thông tin liên quan.
                    Hãy trả về theo format JSON array, trong đó mỗi phần tử là một object đại diện cho một phân cảnh với cấu trúc như sau:
                    {
                        "title": string,
                        "descriptions": [
                            {
                                "sub_title": nội dung phân cảnh gồm 200 từ,
                                "description": nội dung thuyết minh khớp với phân cảnh tạo ra khoảng 16 từ
                            }
                        ]
                    }
                    Mỗi phân cảnh chỉ có 1 trong mảng "descriptions". Nội dung người dùng cần tạo phân cảnh là: '.$topic.' với '.$totalSlide.' phân cảnh từ phân cảnh '.$slideNumber.'.
                    Hãy trả về kết quả theo đúng format JSON đã yêu cầu và không cần mô tả gì thêm.';
            Log::info("Origin prompt: " . $originalPrompt);

            $improvePromptRequest = "Hãy cải thiện câu prompt sau bằng cách:
                            - Làm cho kịch bản có storytelling hấp dẫn hơn.
                            - Đưa ra hướng dẫn giúp phân cảnh có điểm nhấn thú vị, như cao trào, xung đột, hoặc bất ngờ.
                            - Đề xuất cách tạo sự thu hút từ góc quay, hiệu ứng hình ảnh hoặc âm thanh.
                            - Giữ nguyên format JSON nhưng có thể bổ sung hướng dẫn giúp AI hiểu rõ hơn.
                            - **QUAN TRỌNG: Không được thay đổi hay tóm gọn nội dung đầu vào của người dùng. Hãy giữ nguyên toàn bộ phần nội dung người dùng cung cấp.**
                            - **Không được thay thế nội dung gốc bằng các đoạn như '(với toàn bộ nội dung chi tiết như được cung cấp)'.**
                            - **Hãy tối ưu prompt mà không làm mất dữ liệu gốc.**

                            Prompt hiện tại: $originalPrompt";

            // Gửi yêu cầu cải thiện prompt đến o3-mini
            // $improvedPrompt = $this->chatGPTService->getResponse3($improvePromptRequest, 'o3-mini');
            Log::info("ImprovePrompt o3-mini: " . $originalPrompt);
            $responseData = $this->chatGPTService->getResponse2(
                $originalPrompt,
                null,
                'gpt-4o-mini',
                $user
            );
            // $responseDataArr = json_decode($responseData, true);
            $dataStr = str_replace("```json","", $responseData);
            $dataStr = str_replace("```","", $dataStr);
            $dataArr = json_decode($dataStr, true);
            if(!empty($dataArr)) {
                for($idx = 0; $idx < count($dataArr); $idx ++) {
                    if(count($dataSlide) < $numberOfSlides) {
                        $sub_title = $dataArr[$idx]["descriptions"][0]["sub_title"];
                        $protm = 'căn cứ vào nội dung kịch bản : '.$sub_title.'.Hãy tạo ra cho tôi NỘI DUNG THUYẾT MINH độ dài không được vượt quá 18 từ phù hợp với nội dung trên. ChỈ trả nội dung thuyết minh';
                        $responseData = $this->chatGPTService->getResponse2(
                            $protm,
                            null,
                            'gpt-4o',
                            $user
                        );
                        $dataArr[$idx]["descriptions"][0]["description"] = $responseData;
                        $dataSlide[] = $dataArr[$idx];
                    }
                }
            }
            $textCredit = $textCredit.$originalPrompt;
        }
        $res =  Auth::user()->chargeCredit('text', 'GPT-4o mini', null, 'ai-text', $textCredit, 4, 29);
        return response()->json(["success" => true, "data" => $dataSlide]);
    }

    public function findImages(Request $request)
    {
        $ch = curl_init();
        $data = $request->all();
        $keyword = $data['keyword'];
        $images = [];

        $response = Http::withHeaders([
            'Authorization' => config('common.pexels_api_key')
        ])->get('https://api.pexels.com/v1/search', [
            'query' => $keyword,
            'per_page' => 3,
        ]);

        $results = $response->json();
        Log::info("Pexels Result: " . json_encode($results));

        if (isset($results['photos']) && !empty($results['photos'])) {
            foreach ($results['photos'] as $item) {
                $images[] = $item['src']['original'];
            }
        }

        if (empty($images)) { // Nếu không có ảnh từ Pexels, thử Pixabay
            $response = Http::get('https://pixabay.com/api/', [
                'key' => config('common.pixabay_api_key'),
                'q' => $keyword,
                'page' => 1,
                'per_page' => 3,
                'image_type' => 'all',
            ]);

            $results = $response->json();
            Log::info("Pixabay Result: " . json_encode($results));
            if (isset($results['hits']) && !empty($results['hits'])) {
                foreach ($results['hits'] as $item) {
                    $images[] = $item['largeImageURL'];
                }
            }
        }

        if (empty($images)) {
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
        }

        return response()->json([
            'images' => $images
        ]);
    }

    public function getSlideData(array $request)
    {
        $slide_contents = $request['slide_contents'];
        $slides = [];
        $templateId = $this->slideAiTemplateService->getRandomTemplateId();
        $slideTypes = $this->slideAiComponentService->getSlideComponentsNameByTemplateId($templateId, 'body');
        $slideStart = $this->slideAiComponentService->getSlideComponentsNameByTemplateId($templateId, 'start');
        $slideEnd = $this->slideAiComponentService->getSlideComponentsNameByTemplateId($templateId, 'end');
        shuffle($slideTypes);
        $templateIndex = 0;
        foreach ($slide_contents as $index => $slide) {
            $slide['images'] = []; // Khởi tạo mảng images rỗng cho mỗi slide
            if (isset($request['theme'])) {
                $slide['theme'] = $request['theme'];
            }
            $templateIndex++;
            $slide["key"] = "Template" . $templateIndex;
            if ($templateIndex == 3) {
                $templateIndex = 0;
            }
            $slide['icons'] = [
                '/assets/images/slide_ai/icon/star.png',
                '/assets/images/slide_ai/icon/star.png'
            ];

            $voiceType = $slide['voice_type'] ?? 'young_female';
            $slide_audio = GoogleTextToSpeechHelper::getAudio($slide['descriptions'][0]['description'], $voiceType, 'vi-VN', $index);

            $slide['audio'] = $slide_audio;

            $imgs = $this->textToSpeechService->findImageForVideo($slide['keyword']);

            // 2 images default
            $slide['components'] = [
                [
                    'name' => 'EditImage',
                    'src' =>  empty($imgs[0]) ? '' : $imgs[0],
                    'style' => [
                        'width' => 250,
                        'height' => 250,
                        'dragStyle' => [
                            'top' => 130,
                            'left' => 190
                        ],
                        'delayTime' => 6000,
                        'animation' => 'ani-bottom-to-top'
                    ]
                ],

                [
                    'name' => 'EditImage',
                    'src' =>  empty($imgs[1]) ? '' : $imgs[1],
                    'style' => [
                        'width' => 250,
                        'height' => 250,
                        'dragStyle' => [
                            'top' => -130,
                            'left' => 190
                        ],
                        'delayTime' => 1000,
                        'animation' => 'ani-right-to-left'
                    ]
                ],
            ];
            // if(!isset($slide["mc_virtual"])) {
            //     $slide['mc_virtual'] = $mc_virtual_result;
            // }
            $slides[] = $slide;
        }

        // Thêm slide kết
        $slides[] = [
            'title' => 'Cảm ơn đã theo dõi',
            'descriptions' => '',
            'keyword' => 'thank you',
            'key' => $slideEnd[0],
            'theme' => $request['theme'] ?? '',
            'images' => [],
            'style' => [
                'dragStyle' => [
                    'top' => 0,
                    'left' => 0,
                    'width' => 500,
                    'height' => 80
                ],
            ]
        ];
        return $slides;
    }

    public function mapping(StoreSlideAiRequest $request)
    {
        $theme = $request->validated()['theme'];
        $result = $this->getSlideData($request->validated());
        $fileName = $request->validated('file_name');
        if ($fileName) {
            $topic = file_get_contents(storage_path('app/public/slides/' . $fileName));
        } else {
            $topic = $request->validated('topic');
        }
        // store video
        if (strlen($topic) > 50) {
            $topic = $this->chatGPTService->summary($topic);
        }
        $rs = $this->aiVideoService->storeTextToVideo($topic, $result, $theme);

        return redirect()->route('text-to-video.edit', ['id' => $rs->id]);
        // return Inertia::render('Client/TextToVideo/Video', ['slideSuggestions' => $result, 'theme' => $theme, 'topic' => $topic, 'video_id' => $rs->id]);
    }

    // tạo danh sách preview-video
    public function previewMapping(StoreSlideAiRequest $request)
    {
        try {
            Log::info($request->all()); // Ghi nhật ký dữ liệu yêu cầu đến để phục vụ gỡ lỗi
            $theme = $request->validated()['theme'];
            $result = $this->getSlideData($request->validated());
            $fileName = $request->validated('file_name');
            $topic = file_get_contents(storage_path('app/public/slides/' . $fileName));
            return response()->json([
                'slideSuggestions' => $result,
                'theme' => $theme,
                'topic' => $topic,
            ]);
        } catch (\Exception $e) {
            // Trả về lỗi
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function previewTheme(StoreSlideAiRequest $request)
    {
        try {
            $result = $request->validated();
            return response()->json([
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getHistories()
    {
        $lists = $this->aiVideoService->getTexToVideoHistories();
        return Inertia::render('Client/TextToVideo/History', ['list' => $lists]);
    }

    public function editVideo($id)
    {
        try {
            $record = $this->aiVideoService->getVideoById($id);
            if (!$record) {
                return response()->json(['error' => 'Video not found'], 404);
            }

            $theme = json_decode($record->theme);
            $result = json_decode($record->content);
            $topic = $record->title;

            if (is_null($result)) {
                Log::warning("Content for video ID $id is not a valid JSON string.");
                return response()->json(['error' => 'Invalid video content format'], 400);
            }

            if (strlen($topic) > 50) {
                $summarizedTopic = $this->chatGPTService->summary($topic);
                if ($summarizedTopic) {
                    $topic = $summarizedTopic;
                } else {
                    Log::warning("Failed to summarize topic for video ID $id.");
                }
            }

            return Inertia::render('Client/TextToVideo/Edit', [
                'slideSuggestions' => $result,
                'theme' => $theme,
                'topic' => $topic,
                'video_id' => $id
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Video not found'], 404);
        } catch (Exception $e) {
            Log::error("Error in editVideo for video ID $id: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function updateVideo(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'video_id' => 'required',
            'slides' => 'required',
            'theme' => 'required',
            'title' => 'required',
        ], [
            'video_id.required' => "Video id là bắt buộc",
            'slides.required' => 'Dữ liệu video là bắt buộc',
            'theme.required' => 'Kiểu video là bắt buộc',
            'title.required' => 'Tiêu đề là bắt buộc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 400);
        }

        try {
            $result = $this->aiVideoService->createOrUpdateVideo($params);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật video thành công!',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật video thất bại!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteVideo($id)
    {
        try {
            $this->aiVideoService->deleteVideoById($id);

            return response()->json([
                'success' => true,
                'message' => 'Xóa video thành công!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa video thất bại!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateMcByImage(Request $request)
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

                Log::info(message: $didData);

                $params['mc_virtual_id'] = $didData['id'];
                $params['created_by'] = $didData['created_by'];
                $params['status'] = $didData['status'];

                $record = $this->aIVirtualService->store($params);
//                $this->handleUsageCredit();
                  Auth::user()->chargeCredit('mc_virtual', 'd-id', null, null, null, 4, 4);
                return response()->json(['message' => 'Video is generating...', 'credit' => $user->credit ?? 0, 'mc_virtual_id' => $didData['id']]);
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

    private function handleUsageCredit()
    {
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
                "model_type" => 'd-id',
                "credit" => $totalCredit,
            ]
        );
    }

    public function getVirtualById($id)
    {
        $apiKey = env('D_ID_API_KEY');
        $apiUrl = "https://api.d-id.com/talks/" . $id;
        $maxAttempts = 10;
        $attempt = 0;
        $waitTime = 5;

        try {
            while ($attempt < $maxAttempts) {
                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json',
                    'Authorization: Basic ' . $apiKey,
                ]);

                $statusResponse = curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new \Exception(curl_error($ch));
                }

                curl_close($ch);

                $statusData = json_decode($statusResponse, true);
                Log::info('Debug: ' . $statusResponse);

                if (isset($statusData['status']) && $statusData['status'] === 'done') {
                    $fileContent = file_get_contents($statusData['result_url']);
                    if ($fileContent === false) {
                        throw new \Exception('Failed to download video file from ' . $statusData['result_url']);
                    }

                    $tempMp4Path = storage_path('app/temp_video.mp4');
                    file_put_contents($tempMp4Path, $fileContent);

                    $palettePath = storage_path('app/palette.png');
                    $outputGifPath = storage_path('app/output.gif');

                    exec("ffmpeg -i {$tempMp4Path} -vf \"fps=15,scale=320:-1:flags=lanczos,palettegen\" {$palettePath}");
                    exec("ffmpeg -i {$tempMp4Path} -i {$palettePath} -filter_complex \"[0:v]fps=15,scale=320:-1:flags=lanczos,paletteuse\" {$outputGifPath}");

                    $outputUrl = $this->removeBackground(new Request(['imageUrl' => asset('storage/app/output.gif')]));

                    if ($outputUrl) {
                        $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual_', true) . '.gif';
                        Storage::disk('s3')->put($filename, file_get_contents($outputUrl));

                        $url = Helpers::preSignedS3Url($filename);
                    } else {
                        $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual_', true) . '.gif';
                        Storage::disk('s3')->put($filename, file_get_contents($outputGifPath));
                        $url = Helpers::preSignedS3Url($filename);
                    }
                    unlink($tempMp4Path);
                    unlink($palettePath);
                    unlink($outputGifPath);

                    AIPresenter::create([
                        'user_id' => auth('web')->id(),
                        'name' => 'AI Presenter',
                        's3_url' => $filename
                    ]);

                    return response()->json([
                        'success' => true,
                        'gif_url' => $url
                    ]);
                } else {
                    sleep($waitTime);
                    $attempt++;
                }
            }

            throw new \Exception('Video processing is not complete after maximum attempts');
        } catch (\Exception $e) {
            Log::error('Error in fetchVideo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getListPresenters()
    {
        $userId = auth('web')->id();
        $per_page = 8;
        $presenters = AIPresenter::where('user_id', $userId)->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $presenters
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function readFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'imageUrl' => 'nullable|url',
            ]);

            if ($request->has('imageUrl')) {
                $imageUrl = $request->imageUrl;
                $imageFile = file_get_contents($imageUrl);
                $tempFile = storage_path('app/public/image' . uniqid('img_') . 'png');
                file_put_contents($tempFile, $imageFile);
                $sourceImage = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    'image_template.png',
                    mime_content_type($tempFile),
                    null,
                    true
                );
                $documentReader = app(DocumentReaderService::class);
                $content = $documentReader->readContent($sourceImage);
                unlink($tempFile);
            } else {
                $request->validate([
                    'content' => 'required_without:file|nullable|string',
                    'file' => [
                        'required_without:content',
                        'file',
                        'mimes:doc,docx,pdf,png,jpg,jpeg,webp',
                        new MaxFilePageRule()
                    ],
                ]);

                $content = $request->input('content');
                if ($request->hasFile('file')) {
                    /** @var DocumentReaderService $documentReader */
                    $documentReader = app(DocumentReaderService::class);
                    $content = $documentReader->readContent($request->file('file'));
                    echo 11;die;
                }
            }


            if (!Storage::disk('public')->exists('slides')) {
                Storage::disk('public')->makeDirectory('slides');
            }
            $fileName = uniqid('', true) . '.txt';
            $path = storage_path('app/public/slides/' . $fileName);
            file_put_contents($path, $content);
            return response()->json([
                'file_name' => $fileName,
                'type' => 'txt',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại hoặc sử dụng định dạng nội dung khác',
            ], 400);
        }
    }

    public function removeBackground(Request $request)
    {
        $imageUrl = $request->input('imageUrl');

        Log::info('Dữ liệu url là:', ['type' => gettype($imageUrl), 'value' => $imageUrl]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->apiToken}",
        ])->post('https://api.replicate.com/v1/predictions', [
            'version' => '95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1',
            'input' => [
                'image' => $imageUrl,
            ],
        ]);

        Sleep(1);

        $responseData = $response->json();

        Log::info('Dữ liệu là:', $responseData);

        $get_url = $responseData['urls']['get'];

        do {
            sleep(1);

            $responseImg = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->apiToken}",
            ])->get($get_url);

            $responseImgData = $responseImg->json();

            $output = $responseImgData['output'];

            Log::info('Response from API:', $responseImgData);
        } while (is_null($output) && $responseImgData['status'] !== 'failed');

        if (!is_null($output)) {
            Log::info('Link after remove:', ['output' => $output]);
            return $output;
        } else {
            Log::error('Failed to remove background image.');
        }
    }
}

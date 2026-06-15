<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Exceptions\UsageLimitExceededException;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\AiTextConversation;
use App\Models\Music;
use App\Services\AIAssistantsService;
use App\Services\AITextService;
use App\Services\CalendarService;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\AIChatService;
use App\Services\AIChatMessageItemService;
use App\Services\ChatGPTEmbeddingService;
use App\Services\StorageService;
use App\Services\DocumentReaderService;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\ChatGPTStreamingService;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Constants\Sender;


class AiTextController extends Controller
{
    private $chatGPTService;
    private $aITextService;
    private $aIAssistantsService;
    private $chatClaudeService;
    private $aiChatService;
    private $chatGPTStreamingService;
    private $chatGPTEmbeddingService;
    private $aiChatMessageService;

    public function __construct(
        ChatGPTService $chatGPTService,
        AIChatMessageItemService $aiChatMessageService,
        AIChatService $aiChatService,
        AITextService $aITextService,
        AIAssistantsService $aIAssistantsService,
        ChatClaudeService $chatClaudeService,
        ChatGPTStreamingService $chatGPTStreamingService,
        private CalendarService $calendarService,
        ChatGPTEmbeddingService $chatGPTEmbeddingService
    ) {
        $this->chatGPTService = $chatGPTService;
        $this->aiChatMessageService = $aiChatMessageService;
        $this->aITextService = $aITextService;
        $this->aiChatService = $aiChatService;
        $this->aIAssistantsService = $aIAssistantsService;
        $this->chatClaudeService = $chatClaudeService;
        $this->chatGPTStreamingService = $chatGPTStreamingService;
        $this->chatGPTEmbeddingService = $chatGPTEmbeddingService;
    }

    public function index(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        if (!$ai_assistant) {
            return redirect()->route('home.index');
        }
        $job = $this->aIAssistantsService->getOccupationById($ai_assistant->occupation_id);
        $operation = $this->aIAssistantsService->getOperationById($ai_assistant->operation_id);
        $facebooks = $this->calendarService->getFacebooksFanpagesUser();
        return Inertia::render('Client/AIText/Index', [
            'ai_assistant' => $ai_assistant,
            'job' => $job,
            'operation' => $operation,
            'fanpages'  => $facebooks['fanpages'],
            'facebooks'  => $facebooks['facebooks']
        ]);
    }

    public function detail($assistantId)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($assistantId);
        if (!$ai_assistant) {
            return redirect()->route('home.index');
        }
        $job = $this->aIAssistantsService->getOccupationById($ai_assistant->occupation_id);
        $operation = $this->aIAssistantsService->getOperationById($ai_assistant->operation_id);
        $facebooks = $this->calendarService->getFacebooksFanpagesUser();
        $params = [
            'ai_assistant' => $ai_assistant,
            'job' => $job,
            'operation' => $operation
        ];

        $params = array_merge($params,$facebooks);

        return response()->json($params);
    }

    public function loadMore(Request $request, $id)
    {
        $userId = auth('web')->id();
        $ai_assistant_id = $id;
        $perPage = 5;
        $messages = $this->aITextService->getListMessageByUserId($userId, $ai_assistant_id, $perPage);

        if ($messages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No messages found.'
            ]);
        }

        foreach ($messages as $message) {
            $message->media_link = json_decode($message->media_link);
            foreach ($message->media_link as $path) {
                // Kiểm tra $path->path trước khi truyền vào hàm
                if (isset($path->path) && !empty($path->path)) {
                    $path->url = Helpers::preSignedS3Url($path->path);
                } else {
                    $path->url = null; // Gán null nếu $path->path không tồn tại hoặc rỗng
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function send_text(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'limit' => 'nullable|integer',
            'model' => 'nullable|string',
            'language' => 'required|string',
            'description' => 'required|string',
        ]);
        $limit = $request->limit;
        $message = $request->message;
        $inputModel = $request->model;
        $lang = $request->language;
        $description = $request->description;
        $field = $request->field;

        $model = AIModel::getModel($inputModel);

        $promptTemplate = config('chatgpt.ai-text-2');

        $safeMessage = e($message);
        $safeLang = e($lang);
        $safeLimit = e($limit);

        $prompt = str_replace(
            [':field', ':message', ':lang', ':limit', ':desciption'],
            [$field, $safeMessage, $safeLang, $safeLimit, $description],
            $promptTemplate
        );
        $user = auth()->user();
        try {
            $user->decrementUsage(config('constant.assistant_types.text'));
            $response = $this->chatGPTService->getResponse($prompt, null, $model);
            if ($response) {
                $cleanResponse = preg_replace('/(\*\*|###)(.*?)\1/', '', $response);

                $data = [
                    'response' => $cleanResponse,
                    'prompt' => $prompt,
                    'ai_assistant' => $request->ai_assistant
                ];
                Log::info('ChatGPT PROMPT: ', ['prompt' => $prompt]);
                // $record = $this->aITextService->storeMessage($data);
                return response()->json([
                    'success' => true,
                    'response' => [
                        // 'id' => $record->id,
                        'message' => $cleanResponse
                    ]
                ]);
            }

            Log::info('prompt ai nghiep vu : ', $prompt);

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra vui lòng liên hệ với quản trị viên.'
            ], 500);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Throwable $e) {
            Log::error('ChatGPT API error', ['error' => $e->getMessage()]); // Ghi log lỗi
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function claude(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'limit' => 'nullable|integer',
            'model' => 'nullable|string',
            'language' => 'required|string',
            'ai_assistant_id' => 'required|integer|exists:ai_assistants,id',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        $limit = $request->limit;
        $message = $request->message;
        $lang = $request->language;
        $description = $request->description;
        $field = $request->field;
        $ai_assistant_id = $request->ai_assistant_id;
        $description = preg_replace('/\[[^\]]*\]/', $message, $description);
        $contentUserSupplier = '';
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    if ($documentReader->checkPageLimit($file, $pageLimit)) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['file' => "File PDF không được vượt quá $pageLimit trang."]
                        ], 422);
                    }
                    $content = $documentReader->readContentPdfLimitPage($file, 10, 'vi');
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        } else {
            $content = AiAssistant::find($ai_assistant_id)->pdf_content;
            $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
        }

        $refer = '';
        if ($request->has('criteria')) {
            $criteria = json_decode($request->input('criteria'), true);
            foreach ($criteria as $item) {
                // Lấy tên của tiêu chí và giá trị của nó
                $name = $item['name'] ?? '';

                if ($item['type'] === 'input') {
                    $value = $item['value'] ?? '';
                    $refer .= "Liên quan đến $name: $value\n";
                } elseif ($item['type'] === 'select') {
                    $parsedValue = is_array($item['value']) ? $item['value'] : json_decode($item['value'], true);

                    // Kiểm tra trường hợp multi-select
                    if (isset($item['multiple']) && $item['multiple'] == 1 && isset($item['selectedValues']) && is_array($item['selectedValues'])) {

                        $selectedTexts = [];
                        foreach ($item['selectedValues'] as $selectedValue) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedTexts[] = $option['text'];
                                    break;
                                }
                            }
                        }
                        // Gộp các giá trị đã chọn thành một chuỗi và thêm vào kết quả
                        $refer .= "$name: " . implode(', ', $selectedTexts) . "\n";
                    } else {
                        // Trường hợp select đơn
                        $selectedValue = $item['selectedValue'] ?? '';
                        $selectedText = '';
                        if (is_array($parsedValue)) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedText = $option['text'];
                                    break;
                                }
                            }
                        }
                        $refer .= "$name: $selectedText\n";
                    }
                }
            }
        }

        $promt = $description . "sử dụng cho sản phẩm : $message $refer $contentUserSupplier .
                Dựa trên nội dung được cung cấp về $message, tôi sẽ tạo một bảng $description. Đây là JSON kết quả:
                Trả về format html + css đẹp và tách riêng html và css ở phần html chỉ cần trả về các để bao bọc ,
                không cần trả nguyên cấu trúc 1 trang html, css thì khi tạo class có thể nối thêm 1 dãy số không trùng lặp để khỏi ảnh hưởng tới những layout khác
                bắt buộc trả về dạng json(array object) và bỏ qua mô tả trước khi trả về và chú ý escape \\ trong những class css   :
                yêu cầu chỉ trả về mỗi json
                    [
                        {
                            html: \"html\",
                            css: \"css\"
                        }
                    ];
                ";
        $maxAttempts = 5;
        $attempts = 0;
        $user = auth()->user();
        while ($attempts < $maxAttempts) {
            try {
                $user->decrementUsage(config('constant.assistant_types.text'));
                $generatedText = $this->chatClaudeService->sendMessageCustom($promt);
                $steps = json_decode($generatedText, true);
                if (!empty($steps) && is_array($steps) && isset($steps['content'][0]['text'])) {
                    if (is_string($steps['content'][0]['text'])) {
                        $jsonString = str_replace("\n", '', $steps['content'][0]['text']);
                        $rs = json_decode($jsonString, true);
                    } else {
                        $rs = $steps['content'][0]['text'];
                    }
                    if (is_array($rs) && !empty($rs)) {
                        $isValidFormat = true;
                        foreach ($rs as $step) {
                            if (!isset($step['html']) || !isset($step['css'])) {
                                $isValidFormat = false;
                                break;
                            }
                        }

                        if ($isValidFormat) {
                            return response()->json([
                                'success' => true,
                                'steps' => $rs,
                                'credits' => $user->credit
                            ]);
                        }
                    }
                } else {
                    Log::warning('Failed to decode JSON response claude: ' . $generatedText);
                }
            } catch (UsageLimitExceededException $e) {
                // Xử lý khi người dùng đã hết lượt sử dụng trợ lý ảo
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            } catch (\Exception $e) {
                Log::error('Failed to send client message ' . $e->getMessage());
            }
            $attempts++;
        }
        return response()->json([
            'success' => false,
            'steps' => [],
            'credits' => $user->credit
        ]);
    }

    private $maxRetries = 3;
    private $retryDelay = 1000; // 1 giây
    public function claudeStream(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'limit' => 'nullable|integer',
            'model' => 'nullable|string',
            'language' => 'required|string',
            'ai_assistant_id' => 'required|integer|exists:ai_assistants,id',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        $limit = $request->limit;
        $message = $request->message;
        $lang = $request->language;
        $description = $request->description;
        $field = $request->field;
        $ai_assistant_id = $request->ai_assistant_id;
        $description = preg_replace('/\[[^\]]*\]/', $message, $description);
        $contentUserSupplier = '';
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    if ($documentReader->checkPageLimit($file, $pageLimit)) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['file' => "File PDF không được vượt quá $pageLimit trang."]
                        ], 422);
                    }
                    $content = $documentReader->readContentPdfLimitPage($file, 10, 'vi');
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        } else {
            $content = AiAssistant::find($ai_assistant_id)->pdf_content;
            $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
        }
        $refer = '';
        if ($request->has('criteria')) {
            $criteria = json_decode($request->input('criteria'), true);
            foreach ($criteria as $item) {
                // Lấy tên của tiêu chí và giá trị của nó
                $name = $item['name'] ?? '';

                if ($item['type'] === 'input') {
                    $value = $item['value'] ?? '';
                    $refer .= "Liên quan đến $name: $value\n";
                } elseif ($item['type'] === 'select') {
                    $parsedValue = is_array($item['value']) ? $item['value'] : json_decode($item['value'], true);

                    // Kiểm tra trường hợp multi-select
                    if (isset($item['multiple']) && $item['multiple'] == 1 && isset($item['selectedValues']) && is_array($item['selectedValues'])) {

                        $selectedTexts = [];
                        foreach ($item['selectedValues'] as $selectedValue) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedTexts[] = $option['text'];
                                    break;
                                }
                            }
                        }
                        // Gộp các giá trị đã chọn thành một chuỗi và thêm vào kết quả
                        $refer .= "$name: " . implode(', ', $selectedTexts) . "\n";
                    } else {
                        // Trường hợp select đơn
                        $selectedValue = $item['selectedValue'] ?? '';
                        $selectedText = '';
                        if (is_array($parsedValue)) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedText = $option['text'];
                                    break;
                                }
                            }
                        }
                        $refer .= "$name: $selectedText\n";
                    }
                }
            }
        }

        $prompt = $description . " sử dụng cho sản phẩm : $message $refer $contentUserSupplier.
                        Dựa trên nội dung được cung cấp về $message, tôi sẽ tạo một bảng $description. Đây là JSON kết quả:

                        Yêu cầu chỉ trả về JSON đúng định dạng dưới đây, và bỏ qua mọi mô tả khác:
                        [
                            {
                                \"html\": \"html content\",
                                \"css\": \"css content\"
                            }
                        ]

                    Nhấn mạnh lại: chỉ trả về JSON đúng định dạng mà không cần mô tả thêm.";


        return response()->stream(function () use ($prompt) {
            $retryCount = 0;
            $maxRetries = 3;
            $user = auth()->user();
            $result_text = '';
            while ($retryCount < $maxRetries) {
                try {
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $generatedText = $this->chatClaudeService->streamMessage($prompt);
                    foreach ($generatedText as $response) {
                        $text = $response->choices[0]->delta->content ?? '';
                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2; // Thoát khỏi vòng lặp nếu kết nối bị hủy
                        }
                        $data = ['text' => $text];
                        $this->send("update", json_encode($data));
                        $result_text .= $text;
                        $last_stream_response = $response;
                    }
                    $this->send("update", "<END_STREAMING_SSE>");
                    logger($last_stream_response->usage->toArray());
                    break;
                } catch (UsageLimitExceededException $e) {
                    // Xử lý khi người dùng đã hết lượt sử dụng trợ lý ảo
                    switch ($e->getCode()) {
                        case 529:
                            $retryCount++;
                            if ($retryCount >= $this->maxRetries) {
                                $this->send("error", "Failed to generate slide stream after {$this->maxRetries} attempts due to rate limiting.");
                            } else {
                                $this->send("retry", "Rate limit exceeded. Retrying in " . ($this->retryDelay / 1000) . " seconds...");
                                usleep($this->retryDelay * 1000);
                                $this->retryDelay *= 2;
                            }
                            break;
                        default:
                            $this->send("error", "An error occurred: " . $e->getMessage());
                            break;
                    }
                }
            }
            return response()->json([
                'success' => false,
                'steps' => [],
                'credits' => $user->credit
            ]);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function generateMusic(Request $request)
    {
        // Validate input
        $request->validate([
            'content' => 'required|string',
            'duration' => 'required|integer',
            'audioFile' => 'nullable|file',
            'continuation' => 'nullable|boolean',
        ]);

        // dd($request->all());
        // Get input data
        $content = $request->input('content');
        $duration = $request->input('duration');
        $audioFile = $request->file('audioFile');
        $continuation = $request->input('continuation', false);

        // Send input to Claude AI and get response
        $prompt = "Viết lyrics cho một bài hát dựa trên chủ đề: \"$content\".
                       Yêu cầu:
                       - Luôn là tiếng Việt
                       - Thời lượng khoảng $duration giây
                       - Cấu trúc bài hát gồm: verse, chorus, và bridge
                       - Nội dung phải mang ý nghĩa và liên quan đến chủ đề";

        $replicatePrompt = $this->chatWithClaude($prompt, $duration);



        // Call Replicate API to generate audio
        $replicateResponse = $this->callReplicateApi($content, $duration, $audioFile, $continuation);


        $audioSample = Music::create([
            'sample_audio' => $replicateResponse['sample_audio_url'] , // lưu đường dẫn file nếu có
            'result_audio' => $replicateResponse['path'], // giả sử đây là đường dẫn hoặc URL đến file audio kết quả
            'prompt' => $content,
            'lyrics' => $replicatePrompt,
            'user_id' => auth()->id(), // lưu ID của người dùng đang đăng nhập
        ]);

        // Trừ tiền credit user
        Auth::user()->chargeCredit('text_to_music', null, null, null, null, 9, 9);
        // Return generated audio to client
        return response()->json([
            'lyrics' => $replicatePrompt,
            'audio' => $replicateResponse,
            'music_id' => $audioSample->id,
        ]);
    }


    private function chatWithClaude($prompt, $duration)
    {

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => config('claude.api_key'),
            'anthropic-version' => '2023-06-01' // Thêm header này
        ])->post(config('claude.url'), [
            'model' => config('claude.chat_model'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "generate lyrics related \"$prompt\" in \"$duration\" seconds, consider to include those parts: Intro, verse, chorus, drop.
                    You don't need to point out how long for each part, just make sure the total duration is $duration seconds. JUST GIVE THE LYRICS AND STATE OUT WHAT PART IT IS.
                    . No excuses, no reason, no explain, you CAN DO THAT.",
                ],
            ],
            'max_tokens' => 300,
        ]);

        $responseData = json_encode($response->json());


        // Log::info('Claude API full response', ['responseData' => json_encode($responseData)]);
        Log::info('Claude API response', ['responseData' => $responseData]);
        $responseArray = json_decode($responseData, true);
        // dd($responseArray['content'][0]['text']);
        // Log::info('Claude API response array', ['responseArray' => $responseArray]);
        // Log::info('Claude API response array content', ['responseArray' => $responseArray['content']]);
        // Log::info('Claude API response array content 0', ['responseArray' => $responseArray['content'][0]]);

        // Kiểm tra chi tiết cấu trúc của responseData và lấy giá trị mong muốn

        return $responseArray['content'][0]['text'];

    }

    private function callReplicateApi($prompt, $duration, $audioFile, $continuation)
    {
        // dd($audioFile);
        // Call Replicate API to generate audio
        if ($audioFile != null) {

        $imageContent0 = file_get_contents($audioFile);

        // Tạo tên file tạm thời
        $tempFile0 = tempnam(sys_get_temp_dir(), 'music_');
        file_put_contents($tempFile0, $imageContent0);

        // Tạo đối tượng UploadedFile
        $uploadedFile0 = new \Illuminate\Http\UploadedFile(
            $tempFile0,
            'music.wav', // Tên file, bạn có thể thay đổi phần mở rộng nếu cần
            mime_content_type($tempFile0),
            null,
            true // Chỉ định rằng đây là file tạm thời
        );

        // Upload lên S3
        $storageService0 = app(StorageService::class);
        $s3Result0 = $storageService0->putToS3($uploadedFile0, 'musicSample');

        Log::info('S3 result', ['s3Result' => $s3Result0]);
        // Xóa file tạm
        unlink($tempFile0);}

        $replicateResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
        ])->post('https://api.replicate.com/v1/predictions', [
            'version' => '96af46316252ddea4c6614e31861876183b59dce84bad765f38424e87919dd85',
            'input' => array_merge([
                'prompt' => $prompt,
                'duration' => intval($duration),
                'continuation' => $continuation,
            ], $audioFile ? ['input_audio' => $s3Result0['url']] : [])
        ]);


        Log::info('Replicate API response', ['replicateResponse' => $replicateResponse->json()]);


        $get_url = $replicateResponse['urls']['get'];

        do {
            sleep(2);

            $responseImg = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
            ])->get($get_url);

            $responseImgData = $responseImg->json();
            $output = $responseImgData['output'];

            Log::info('Response from DIFF API Img:', $responseImgData);
        } while (is_null($output) && $responseImgData['status'] !== 'failed');

        if (!is_null($output)) {
            // Tải hình ảnh về

            Log::info('ouput 0:', [$output]);
            $imageContent = file_get_contents($output);

            // Tạo tên file tạm thời
            $tempFile = tempnam(sys_get_temp_dir(), 'music_');
            file_put_contents($tempFile, $imageContent);

            // Tạo đối tượng UploadedFile
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'music.wav', // Tên file, bạn có thể thay đổi phần mở rộng nếu cần
                mime_content_type($tempFile),
                null,
                true // Chỉ định rằng đây là file tạm thời
            );

            // Upload lên S3
            $storageService = app(StorageService::class);
            $s3Result = $storageService->putToS3($uploadedFile, 'music');

            Log::info('S3 result', ['s3Result' => $s3Result]);
            // Xóa file tạm
            unlink($tempFile);

            // // Lưu vào database

            // AIResource::create([
            //     'prompt' => $prompt,
            //     's3_path' => $s3Result['path'],
            //     'type' => 'diff'
            // ]);

            return [
                'path' => $s3Result['path'],
                'url' => $s3Result['url'],
                'sample_audio_url' => $audioFile ? $s3Result0['path'] : "không có"
            ];
        } else {
            Log::error('Failed to generate diff.');
            return response()->json(['error' => 'Failed to generate diff'], 500);
        }
    }

    public function getAllMusic()
    {
    try {
        $user_id = auth()->id();
        // Log::info('User ID: ' . $user_id);

        // Lấy records từ bảng music của user hiện tại
        $musicRecords = Music::where('user_id', $user_id)
        ->where(function ($query) {
            $query->where('model', '!=', 'song')
                  ->orWhereNull('model');
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($record) {
            return [
                'id' => $record->id,
                'sample_audio' => $record->sample_audio ? Helpers::preSignedS3Url($record->sample_audio) : null,
                'result_audio' => $record->result_audio ? Helpers::preSignedS3Url($record->result_audio) : null,
                'prompt' => $record->prompt,
                'lyrics' => $record->lyrics,
                'user_id' => $record->user_id,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at
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
    public function send_gpt(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'limit' => 'nullable|integer',
            'model' => 'nullable|string',
            'language' => 'required|string',
            'ai_assistant_id' => 'required|integer|exists:ai_assistants,id',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        $limit = $request->limit;
        $message = $request->message;
        $inputModel = $request->model;
        $lang = $request->language;
        $description = $request->description;
        $ai_assistant_id = $request->ai_assistant_id;
        $field = $request->field;
        $description = preg_replace('/\[[^\]]*\]/', $message, $description);
        $model = AIModel::getModel($inputModel);
        $contentUserSupplier = '';
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    if ($documentReader->checkPageLimit($file, $pageLimit)) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['file' => "File PDF không được vượt quá $pageLimit trang."]
                        ], 422);
                    }
                    $content = $documentReader->readContentPdfLimitPage($file, 10, 'vi');
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        } else {
            $content = AiAssistant::find($ai_assistant_id)->pdf_content;
            $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
        }

        $refer = '';
        if ($request->has('criteria')) {
            $criteria = json_decode($request->input('criteria'), true);
            foreach ($criteria as $item) {
                // Lấy tên của tiêu chí và giá trị của nó
                $name = $item['name'] ?? '';

                if ($item['type'] === 'input') {
                    $value = $item['value'] ?? '';
                    $refer .= "Liên quan đến $name: $value\n";
                } elseif ($item['type'] === 'select') {
                    $parsedValue = is_array($item['value']) ? $item['value'] : json_decode($item['value'], true);

                    // Kiểm tra trường hợp multi-select
                    if (isset($item['multiple']) && $item['multiple'] == 1 && isset($item['selectedValues']) && is_array($item['selectedValues'])) {

                        $selectedTexts = [];
                        foreach ($item['selectedValues'] as $selectedValue) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedTexts[] = $option['text'];
                                    break;
                                }
                            }
                        }
                        // Gộp các giá trị đã chọn thành một chuỗi và thêm vào kết quả
                        $refer .= "$name: " . implode(', ', $selectedTexts) . "\n";
                    } else {
                        // Trường hợp select đơn
                        $selectedValue = $item['selectedValue'] ?? '';
                        $selectedText = '';
                        if (is_array($parsedValue)) {
                            foreach ($parsedValue as $option) {
                                if ($option['value'] == $selectedValue) {
                                    $selectedText = $option['text'];
                                    break;
                                }
                            }
                        }
                        $refer .= "$name: $selectedText\n";
                    }
                }
            }
        }

        $promt = $description . "sử dụng cho sản phẩm : $message $refer $contentUserSupplier .
                Dựa trên nội dung được cung cấp về $message, tôi sẽ tạo một bảng $description. Đây là JSON kết quả:
                Trả về format html + css đẹp và tách riêng html và css ở phần html chỉ cần trả về các để bao bọc ,
                không cần trả nguyên cấu trúc 1 trang html, css thì khi tạo class có thể nối thêm 1 dãy số không trùng lặp để khỏi ảnh hưởng tới những layout khác
                bắt buộc chỉ cần trả về dạng  format json và chỉ trả về 1  html chứa tất cả các thông tin của promt , 1 css ,
                và bỏ qua mô tả trước khi trả về
                    Hãy trả về một array object JSON (array object) dưới định dạng như sau mà không thêm bất kỳ kí hiệu nào khác (như ```json):
                    [
                        {
                            \"html\": \"<html-content>\",
                            \"css\": \"<css-content>\"
                        }
                    ]
                    Chỉ cần trả về định dạng như yêu cầu.
                ";
        $maxAttempts = 5;
        $attempts = 0;
        $user = auth()->user();
        while ($attempts < $maxAttempts) {
            try {
                $user->decrementUsage(config('constant.assistant_types.text'));
                $generatedText = $this->chatGPTService->getResponse($promt, null, $model);
                $steps = json_decode($generatedText, true);
                if (!empty($steps) && is_array($steps)) {
                    $rs = $steps;
                    if (is_array($rs) && !empty($rs)) {
                        $isValidFormat = true;
                        foreach ($rs as $step) {
                            if (!isset($step['html']) || !isset($step['css'])) {
                                $isValidFormat = false;
                                break;
                            }
                        }

                        if ($isValidFormat) {
                            return response()->json([
                                'success' => true,
                                'steps' => $rs,
                                'credits' => $user->credit
                            ]);
                        }
                    }
                } else {
                    Log::warning('Failed to decode JSON response claude: ' . $generatedText);
                }
            } catch (UsageLimitExceededException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            } catch (\Exception $e) {
                Log::error('Failed to send client message ' . $e->getMessage());
            }
            $attempts++;
        }

        return response()->json([
            'success' => false,
            'steps' => [],
            'credits' => $user->credit
        ]);
    }

    public function stream_gpt(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'limit' => 'nullable|integer',
            'model' => 'nullable|string',
            'language' => 'required|string',
            'ai_assistant_id' => 'required|integer|exists:ai_assistants,id',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        $limit = $request->limit;
        $message = $request->message;
        $inputModel = $request->model;
        $lang = $request->language;
        $description = $request->description;
        $ai_assistant_id = $request->ai_assistant_id;
        $field = $request->field;
        $description = preg_replace('/\[[^\]]*\]/', $message, $description);
        $model = AIModel::getModel($inputModel);
        $contentUserSupplier = '';
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    if ($documentReader->checkPageLimit($file, $pageLimit)) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['file' => "File PDF không được vượt quá $pageLimit trang."]
                        ], 422);
                    }
                    $content = $documentReader->readContentPdfLimitPage($file, 10, 'vi');
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        } else {
            $content = AiAssistant::find($ai_assistant_id)->pdf_content;
            $contentUserSupplier = ". Yêu cầu ưu tiên lấy th��ng tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
        }

        $prompt = $description . " sử dụng cho sản phẩm : $message $contentUserSupplier.
                        Dựa trên nội dung được cung cấp về $message, tôi sẽ tạo một bảng $description. Đây là JSON kết quả:

                        Yêu cầu chỉ trả về JSON đúng định dạng dưới đây, và bỏ qua mọi mô tả khác:
                        [
                            {
                                \"html\": \"html content\",
                                \"css\": \"css content\"
                            }
                        ]

                    Nhấn mạnh lại: chỉ trả về JSON đúng định dạng mà không cần mô tả thêm.";
        return response()->stream(function () use ($prompt, $model) {
            $retryCount = 0;
            $maxRetries = 3;
            $result_text = '';

            while ($retryCount < $maxRetries) {
                try {
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $generatedText = $this->chatGPTStreamingService->stream($prompt, $model);

                    foreach ($generatedText as $response) {
                        $text = $response->choices[0]->delta->content ?? '';

                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2;
                        }

                        $data = ['text' => $text];
                        $this->send("update", json_encode($data));
                        $result_text .= $text;
                    }

                    $this->send("update", "<END_STREAMING_SSE>");
                    break;
                } catch (UsageLimitExceededException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 403);
                } catch (\Exception $e) {
                    Log::error('Unexpected Exception: ' . $e->getMessage());
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        $this->send("error", "Failed after {$maxRetries} attempts");
                        break;
                    }
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function stream(Request $request)
    {
        // Khởi động bộ đệm đầu ra
        ob_start();

        $request->validate([
            'message' => 'required|string',
            'field' => 'required|string',
            'voice' => 'nullable|string',
            'limit' => 'nullable|integer',
            'language' => 'required|string',
            'description' => 'required|string',
        ]);

        $message = $request->message;
        $field = $request->field;
        $limit = $request->limit;
        $language = $request->language;
        $description = $request->description;

        return response()->stream(function () use ($message, $description, $field, $language, $limit) {
            $retryCount = 0;
            $maxRetries = 3;
            $retryDelay = 1000; // 1 giây
            $result_text = ""; // Biến lưu trữ văn bản tổng hợp

            while ($retryCount < $maxRetries) {
                try {
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $stream = $this->chatClaudeService->sendMessageStream($message, $description, $field, $language, $limit);

                    foreach ($stream as $response) {
                        $text = $response->choices[0]->delta->content ?? '';
                        Log::info('Nhận được văn bản: ' . $text);

                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2;
                        }
                        $data = ['text' => $text];
                        $this->send("update", json_encode($data));
                        $result_text .= $text;
                        $last_stream_response = $response;
                        if (ob_get_level() > 0) {
                            ob_flush();
                            flush();
                        }
                    }

                    $this->send("update", "<END_STREAMING_SSE>");
                    if (ob_get_level() > 0) {
                        ob_flush();
                        flush();
                    }
                    break;
                } catch (RequestException $e) {
                    Log::error('RequestException: ' . $e->getMessage());
                    if ($e->getCode() == 529) {
                        $retryCount++;
                        if ($retryCount >= $maxRetries) {
                            echo "data: " . json_encode(['error' => "Không thể lấy dữ liệu sau {$maxRetries} lần thử do giới hạn tỉ lệ."]) . "\n\n";
                        } else {
                            echo "data: " . json_encode(['retry' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."]) . "\n\n";
                            usleep($retryDelay * 1000);
                            $retryDelay *= 2;
                        }
                        ob_flush();
                        flush();
                    }
                } catch (UsageLimitExceededException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 403);
                } catch (\Exception $e) {
                    Log::error('Unexpected Exception: ' . $e->getMessage());
                    if (ob_get_level() > 0) {
                        echo "data: " . json_encode(['error' => 'Đã xảy ra lỗi không mong muốn.']) . "\n\n";
                        ob_flush();
                        flush();
                    }
                    break;
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    private function send($event, $data)
    {
        try {
            if (ob_get_level() == 0) {
                ob_start();
            }
            echo "event: {$event}\n";
            echo 'data: ' . $data;
            echo "\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
            'prompt' => 'required|string',
            'media_link' => 'nullable|array',
        ]);

        try {
            $message_id = $request->id;
            $message = $request->message;
            $ai_assistant = $request->ai_assistant;
            $media_link = $request->media_link;
            $prompt = $request->prompt;
            $css = $request->css;
            $conversationId = $request->conversationId ?? '';
            $step_ais = $request->step_ais ?? '[]';
            $data = [
                'id' => $message_id,
                'response' => $message,
                'prompt' => $prompt,
                'ai_assistant' => $ai_assistant,
                'media_link' => json_encode($media_link),
                'css_style' => $css,
                'ai_text_conversation_id' => $conversationId,
                'step_ais' => $step_ais,
            ];
            $record = $this->aITextService->storeMessage($data);
            return response()->json([
                'success' => true,
                'response' => $record->getData(true),
                'credits' => $user->credit ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting message:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình lưu tin nhắn'
            ]);
        }
    }

    public function deleteText(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:ai_assistant_histories,id',
        ]);
        try {
            $message_id = $request->id;
            $response = $this->aITextService->deleteMessageById($message_id);

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

    public function getConversation(Request $request) {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['error' => 'Conversation ID is required'], 404);
        }

        $conversation = AiTextConversation::find($id);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        return response()->json([
            'messages' => $conversation->messages
        ]);
    }

    public function handleConversation(Request $request)
    {
        // Lấy ID từ request (nếu có)
        $id = $request->input('id');
        $type = $request->input('type');
        $isActiveGdth = $request->boolean('isActiveGdth') ?? false;
        $selectedTone = $request->input('selectedTone') ?? '';
        // Nếu có ID, tìm kiếm và cập nhật hội thoại hiện tại
        if ($id) {
            $conversation = AiTextConversation::find($id);

            if (!$conversation) {
                return response()->json(['error' => 'Conversation not found'], 404);
            }

            // Lấy messages hiện tại và thêm message mới với role từ request
            $messages = $conversation->messages;
            if ($isActiveGdth) {
                $content = "Trả lời bằng giọng điệu là " . $selectedTone . " " . $request->input('content');
            } else {
                $content = $request->input('content');
            }
            $messages[] = [
                "role" => $request->input('role', 'user'), // Lấy role từ request, mặc định là 'user'
                "content" => $content
            ];

            // Cập nhật hội thoại với messages mới
            $conversation->update(['messages' => $messages]);

            return response()->json($conversation);
        } else {
            // Nếu không có ID, tạo hội thoại mới với message đầu tiên và role
            // Goi api de tao 1 mo bai ton tac noi dung
            $contentInput = $request->input('content') ?? '';
            $filterInput = $request->input('filter') ?? '';
            if ($isActiveGdth) {
                $content = "Trả lời bằng giọng điệu là " . $selectedTone . " " .  $request->input('content') . ' sử dụng cho sản phẩm ' . $contentInput . " yêu cầu tạo tiêu chí đã chon " . $filterInput;
            } else {
                $content = 'sử dụng cho sản phẩm ' . $contentInput . " yêu cầu tạo tiêu chí đã chon " . $filterInput;
            }

            $messages = [
                [
                    "role" => $request->input('role', 'user'),
                    "content" => $content
                ],
            ];

            $conversation = AiTextConversation::create(['messages' => $messages]);

            return response()->json($conversation);
        }
    }

    public function handleAskClaudeStream(Request $request) {
        $request->validate([
            'messages' => 'required|json',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt',
        ]);

        $messages = json_decode($request->input('messages'), true);

        if ($request->hasFile('file')) {
            try {
                $content = '';
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'txt') {
                    $content = file_get_contents($file->path());
                }
                if ($extension === 'pdf') {
                    $content = $documentReader->readContent($file);
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                array_unshift($messages, [
                    'role' => 'user',
                    'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        }

        return response()->stream(function () use ($messages) {
            $retryCount = 0;
            $maxRetries = 3;
            $retryDelay = 1000; // 1 giây
            $result_text = ""; // Biến lưu trữ văn bản tổng hợp
            $messageInputCharge = []; // Khởi tạo mảng charge
            // Thêm user messages vào messageInputCharge
            foreach ($messages as $message) {
                if ($message['role'] === 'user') {
                    $messageInputCharge[] = $message;
                }
            }
            while ($retryCount < $maxRetries) {
                try {
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $stream = $this->chatClaudeService->streamChat($messages);

                    foreach ($stream as $response) {
                        $text = $response->choices[0]->delta->content ?? '';
                        Log::info('Nhận được văn bản: ' . $text);

                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2;
                        }
                        $data = ['text' => $text];
                        $this->send("update", json_encode($data));
                        $result_text .= $text;
                        $last_stream_response = $response;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }

                    // Sau khi nhận được toàn bộ response, thêm assistant message vào messageInputCharge
                    if (!empty($result_text)) {
                        $messageInputCharge[] = [
                            "role" => "assistant",
                            "content" => $result_text
                        ];

                        // Charge credit
                        if (!empty($messageInputCharge) && is_array($messageInputCharge)) {
                            $contents = array_column($messageInputCharge, 'content');
                            $combinedContent = trim(implode(' ', $contents));
                            if (!empty($combinedContent)) {
                                try {
                                    // Trừ tiền credit user
                                    Auth::user()->chargeCredit('text', 'Claude 3.5 Sonnet', null, 'ai-text', $combinedContent, 13, 13);
                                } catch (\Exception $e) {
                                    Log::error("Error charging credit: " . $e->getMessage());
                                }
                            }
                        }
                    }

                    $this->send("update", "<END_STREAMING_SSE>");
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    break;
                } catch (RequestException $e) {
                    Log::error('RequestException: ' . $e->getMessage());
                    if ($e->getCode() == 529) {
                        $retryCount++;
                        if ($retryCount >= $maxRetries) {
                            echo "data: " . json_encode(['error' => "Không thể lấy dữ liệu sau {$maxRetries} lần thử do giới hạn tỉ lệ."]) . "\n\n";
                        } else {
                            echo "data: " . json_encode(['retry' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."]) . "\n\n";
                            usleep($retryDelay * 1000);
                            $retryDelay *= 2;
                        }
                        ob_flush();
                        flush();
                    }
                } catch (UsageLimitExceededException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 403);
                } catch (\Exception $e) {
                    Log::error('Unexpected Exception: ' . $e->getMessage());
                    if (ob_get_level() > 0) {
                        echo "data: " . json_encode(['error' => 'Đã xảy ra lỗi không mong muốn.']) . "\n\n";
                        ob_flush();
                        flush();
                    }
                    break;
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function handleMultiStepProcess(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'steps' => 'required|json',
            'model' => 'required|string',
            'language' => 'required|string',
            'limit' => 'required|integer',
            'criteria' => 'required|json',
            'conversationId' => 'required',
        ]);
        $conversationId = $request->conversationId ?? '';
        $conversation = AiTextConversation::find($conversationId);
        $isActiveGdth = $request->boolean('isActiveGdthLeft') ?? false;
        $selectedTone = $request->input('selectedToneLeft') ?? '';
        $messageSearch = $request->message ?? '';
        return response()->stream(function () use ($request, $conversationId, $conversation, $isActiveGdth, $selectedTone, $messageSearch) {
            $newSteps = json_decode($request->steps, true);
            $ai_assistant_id = $request->ai_assistant_id ?? '';

            $messages = $messages_search = [];
            $fileContent = '';

            // Lấy messages cũ từ database nếu có conversationId
            if ($conversationId) {
                $conversation = AiTextConversation::find($conversationId);
                if ($conversation && !empty($conversation->messages)) {
                    $messages = $conversation->messages;
                    $messages_search = $conversation->messages;
                }
            }

            // Merge với steps mới
            $steps = $newSteps;
            // Xử lý file upload nếu có
            if ($request->hasFile('file')) {
                try {
                    $uploadedFiles = $request->file('file');
                    $documentReader = app(DocumentReaderService::class);
                    if (!empty($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $fileContent = '';
                            $extension = $file->getClientOriginalExtension();
                            switch ($extension) {
                                case 'txt':
                                    $content = file_get_contents($file->path());
                                    break;
                                case 'pdf':
                                    $content = $documentReader->readContent($file);
                                    break;
                                case 'doc':
                                case 'docx':
                                $content = $documentReader->readContent($file, 'vi');
                                    break;
                            }

                            if ($content) {
                                $messages[] = [
                                    'role' => 'user',
                                    'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->sendError('Không thể tải lên file. Vui lòng thử lại.');
                    return;
                }
            } else {
                $content = AiAssistant::find($ai_assistant_id)->pdf_content;
                if (!empty($content)) {
                    $messages[] = [
                        'role' => 'user',
                        'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                    ];
                }
            }


            $isEmbeddingCall = false;
            foreach ($steps as $index => $step) {
                try {
                    $messageInputCharge = [];
                    // Log để kiểm tra
                    Log::info("Processing step: " . $index);
                    Log::info("Step data:", $step);

                    // Thông báo bắt đầu step
                    $this->sendEvent('stepStart', [
                        'stepIndex' => $index,
                        'stepName' => $step['name'],
                        'type' => "new"
                    ]);

                    // Kiểm tra step trước
                    if ($index > 0 && !$this->isStepSuccessful($index - 1)) {
                        throw new Exception("Step trước phải hoàn thành trước");
                    }

                    // Kiểm tra usage limit
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));

                    // Tạo message cho user input
                    if ($isActiveGdth) {
                        $content = "Please answer me in Tiếng Việt language and also respond in Tiếng Việt language  with " . $selectedTone . " voice style. " . $step['description'];
                        $userMessage = [
                            "role" => "user",
                            "content" => $content
                        ];
                    } else {
                        $userMessage = [
                            "role" => "user",
                            "content" => $step['description']
                        ];
                    }

                    // Lưu user message vào DB
                    if (!$conversationId) {
                        // Tạo conversation mới nếu chưa có
                        $conversation = AiTextConversation::create([
                            'messages' => [$userMessage]
                        ]);
                        $conversationId = $conversation->id;
                    } else {
                        $currentMessages = $conversation->messages;
                        $currentMessages[] = $userMessage;
                        $conversation->update(['messages' => $currentMessages]);
                    }

                    $messages[] = $userMessage;
                    $messageInputCharge[] = $userMessage;
                    // Xử lý stream response
                    $retryCount = 0;
                    $maxRetries = 3;
                    $retryDelay = 1000;
                    $result_text = "";

                    while ($retryCount < $maxRetries) {
                        try {
                            try {
                                $stream = $this->chatClaudeService->streamChat($messages);
                            } catch (\Exception $e) {
                                if (!empty($content) && !$isEmbeddingCall) {
                                    // Tìm kiếm
                                    $results = $this->chatGPTEmbeddingService->search($messageSearch, $content);
                                    // Lấy text của kết quả đầu tiên (gần nhất)
                                    $mostRelevantText = $this->cleanText($results[0]['text'] ?? '');
                                    // Tạo message cho GPT
                                    $messages_search[] = [
                                        'role' => 'user',
                                        'content' => "Đây là đoạn văn bản liên quan:\n\n" . $mostRelevantText . "Hãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                                    ];
                                    $isEmbeddingCall = true;
                                    //print_r($messages_search);
                                    //exit();

                                }
                                $messages_search[] = $userMessage;
                                Log::warning("Lỗi max token, chuyển sang tìm đoạn văn bản gần nhất.");
                                $stream = $this->chatClaudeService->streamChat($messages_search);
                            }

                            foreach ($stream as $response) {
                                if (connection_aborted()) {
                                    throw new Exception('Kết nối đã bị hủy');
                                }

                                $text = $response->choices[0]->delta->content ?? '';
                                if (!empty($text)) {
                                    $this->sendEvent('stepProgress', ['text' => $text]);
                                    $result_text .= $text;
                                }
                            }

                            // Tạo assistant message
                            $assistantMessage = [
                                "role" => "assistant",
                                "content" => $result_text
                            ];

                            // Lưu assistant message vào DB
                            $currentMessages = $conversation->messages;
                            $currentMessages[] = $assistantMessage;
                            $conversation->update(['messages' => $currentMessages]);

                            // Thêm vào messages array
                            $messages[] = $assistantMessage;
                            $messageInputCharge[] = $assistantMessage;

                            // Thông báo hoàn thành step
                            $this->sendEvent('stepComplete', [
                                'stepIndex' => $index,
                                'conversations' => $assistantMessage
                            ]);
                            sleep(1);
                            // Charge một lần cho cả user message và assistant message
                            if (!empty($messageInputCharge) && is_array($messageInputCharge)) {
                                $contents = array_column($messageInputCharge, 'content');
                                $combinedContent = trim(implode(' ', $contents));
                                if (!empty($combinedContent)) {
                                    try {
                                        Log::info("Step : " . $index . $combinedContent);
                                        // Trừ tiền credit user
                                        Auth::user()->chargeCredit('text', 'Claude 3.5 Sonnet', null, 'ai-text', $combinedContent, 13, $ai_assistant_id);
                                    } catch (\Exception $e) {
                                        Log::error("Error charging credit: " . $e->getMessage());
                                    }
                                }
                            }
                            break;

                        } catch (RequestException $e) {
                            if ($e->getCode() == 529 && $retryCount < $maxRetries - 1) {
                                $retryCount++;
                                $this->sendEvent('retry', [
                                    'message' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."
                                ]);
                                usleep($retryDelay * 1000);
                                $retryDelay *= 2;
                                continue;
                            }
                            throw $e;
                        }
                    }

                } catch (UsageLimitExceededException $e) {
                    $this->sendError($e->getMessage());
                    break;
                } catch (\Exception $e) {
                    $this->sendEvent('stepError', [
                        'stepIndex' => $index,
                        'error' => $e->getMessage()
                    ]);
                    break;
                }
            }

            $this->sendEvent('complete', []);

        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function handleMultiStepProcessGpt(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'steps' => 'required|json',
            'model' => 'required|string',
            'language' => 'required|string',
            'limit' => 'required|integer',
            'criteria' => 'required|json',
            'conversationId' => 'required',
        ]);
        $inputModel = $request->model;
        $model = AIModel::getModel($inputModel);
        $conversationId = $request->conversationId ?? '';
        $conversation = AiTextConversation::find($conversationId);
        $isActiveGdth = $request->boolean('isActiveGdthLeft') ?? false;
        $selectedTone = $request->input('selectedToneLeft') ?? '';
        $ai_assistant_id = $request->ai_assistant_id ?? '';
        $messageSearch = $request->message ?? '';
        return response()->stream(function () use ($request, $conversationId, $conversation, $model, $inputModel, $isActiveGdth, $selectedTone, $messageSearch) {
            $newSteps = json_decode($request->steps, true);
            $ai_assistant_id = $request->ai_assistant_id ?? '';
            $messages = $messages_search = [];
            $fileContent = '';

            // Lấy messages cũ từ database
            if ($conversationId) {
                $conversation = AiTextConversation::find($conversationId);
                if ($conversation && !empty($conversation->messages)) {
                    $messages = $conversation->messages;
                    $messages_search = $conversation->messages;
                }
            }
            // Xử lý file upload
            if ($request->hasFile('file')) {
                try {
                    $uploadedFiles = $request->file('file');
                    $documentReader = app(DocumentReaderService::class);
                    if (!empty($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $content = '';
                            $extension = $file->getClientOriginalExtension();
                            switch ($extension) {
                                case 'txt':
                                    $content = file_get_contents($file->path());
                                    break;
                                case 'pdf':
                                    $content = $documentReader->readContent($file);
                                    break;
                                case 'doc':
                                case 'docx':
                                $content = $documentReader->readContent($file, 'vi');
                                    break;
                            }

                            if ($content) {
                                $messages[] = [
                                    'role' => 'user',
                                    'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->sendError('Không thể tải lên file. Vui lòng thử lại.');
                    return;
                }
            } else if ($ai_assistant_id) {
                $content = AiAssistant::find($ai_assistant_id)->pdf_content;
                if (!empty($content)) {
                    $messages[] = [
                        'role' => 'user',
                        'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                    ];
                }
            }
            $isEmbeddingCall = false;
            foreach ($newSteps as $index => $step) {
                try {
                    $messageInputCharge = [];
                    Log::info("Processing step: " . $index);
                    Log::info("Step data:", $step);

                    $this->sendEvent('stepStart', [
                        'stepIndex' => $index,
                        'stepName' => $step['name'],
                        'type' => "new"
                    ]);

                    if ($index > 0 && !$this->isStepSuccessful($index - 1)) {
                        throw new Exception("Step trước phải hoàn thành trước");
                    }

                    // Kiểm tra usage limit
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));

                    if ($isActiveGdth) {
                        $content = "Please answer me in Tiếng Việt language and also respond in Tiếng Việt language  with " . $selectedTone . " voice style. " . $step['description'];
                        $userMessage = [
                            "role" => "user",
                            "content" => $content
                        ];
                    } else {
                        $userMessage = [
                            "role" => "user",
                            "content" => $step['description']
                        ];
                    }

                    // Lưu user message
                    if (!$conversationId) {
                        $conversation = AiTextConversation::create([
                            'messages' => [$userMessage]
                        ]);
                        $conversationId = $conversation->id;
                    } else {
                        $currentMessages = $conversation->messages;
                        $currentMessages[] = $userMessage;
                        $conversation->update(['messages' => $currentMessages]);
                    }

                    $messages[] = $userMessage;
                    $messages_search[] = $userMessage;
                    $messageInputCharge[] = $userMessage;
                    $retryCount = 0;
                    $maxRetries = 3;
                    $retryDelay = 1000;

                    while ($retryCount < $maxRetries) {
                        try {
                            try {
                                $stream = $this->chatGPTService->streamResponse($messages, $model);
                            } catch (\Exception $e) {
                                if (str_contains($e->getMessage(), 'maximum context length') || str_contains($e->getMessage(), 'tokens')) {
//                                    print_r($messages_search);
//                                    exit();
                                    if (!empty($content) && !$isEmbeddingCall) {
                                        Log::warning("[" . now()->format('Y-m-d H:i:s') . "] Có nội dung user upload or admin upload nên bắt đầu tạo embedding");
                                        $results = $this->chatGPTEmbeddingService->search($messageSearch, $content);
                                        // Lấy text của kết quả đầu tiên (gần nhất)
                                        $mostRelevantText = $this->cleanText($results[0]['text'] ?? '');
                                        // Tạo message cho GPT
                                        $messages_search[] = [
                                            'role' => 'user',
                                            'content' => "Đây là đoạn văn bản liên quan:\n\n" . $mostRelevantText . "Hãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                                        ];
                                        Log::warning("[" . now()->format('Y-m-d H:i:s') . "] Có nội dung user upload or admin upload kết thúc tạo embedding");
                                        $isEmbeddingCall = true;
                                    }

                                    $messages_search[] = $userMessage;
                                    Log::warning("Lỗi max token, chuyển sang tìm đoạn văn bản gần nhất.");
                                    $stream = $this->chatGPTService->streamResponse($messages_search, $model);
                                } else {
                                    throw $e;
                                }
                            }
                            $result_text = '';
                            foreach ($stream as $response) {
                                if (connection_aborted()) {
                                    break;
                                }

                                if ($content = $response->choices[0]->delta->content) {
                                    $result_text .= $content;

                                    $this->sendEvent('stepProgress', [
                                        'stepIndex' => 1,
                                        'text' => $content
                                    ]);
                                }
                            }

                            // Tạo và lưu assistant message
                            $assistantMessage = [
                                "role" => "assistant",
                                "content" => $result_text
                            ];

                            $currentMessages = $conversation->messages;
                            $currentMessages[] = $assistantMessage;
                            $messageInputCharge[] = $assistantMessage;
                            $conversation->update(['messages' => $currentMessages]);
                            $messages[] = $assistantMessage;

                            // Charge một lần cho cả user message và assistant message
                            if (!empty($messageInputCharge) && is_array($messageInputCharge)) {
                                $contents = array_column($messageInputCharge, 'content');
                                $combinedContent = trim(implode(' ', $contents));
                                if (!empty($combinedContent)) {
                                    try {
                                        Log::info("Step : " . $index . $combinedContent);
                                        // Trừ tiền credit user
                                        Auth::user()->chargeCredit('text', $inputModel, null, 'ai-text', $combinedContent, 13 ,$ai_assistant_id);
                                    } catch (\Exception $e) {
                                        Log::error("Error charging credit: " . $e->getMessage());
                                    }
                                }
                            }

                            $this->sendEvent('stepComplete', [
                                'stepIndex' => $index,
                                'conversations' => $assistantMessage
                            ]);
                            sleep(1);
                            break;

                        } catch (RequestException $e) {
                            if ($e->getCode() == 529 && $retryCount < $maxRetries - 1) {
                                $retryCount++;
                                $this->sendEvent('retry', [
                                    'message' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."
                                ]);
                                usleep($retryDelay * 1000);
                                $retryDelay *= 2;
                                continue;
                            }
                            throw $e;
                        }
                    }

                } catch (UsageLimitExceededException $e) {
                    $this->sendError($e->getMessage());
                    break;
                } catch (\Exception $e) {
                    $this->sendEvent('stepError', [
                        'stepIndex' => $index,
                        'error' => $e->getMessage()
                    ]);
                    break;
                }
            }

            // Tinh tien

            $this->sendEvent('complete', []);

        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);

    }

    public function handleAskGptStream(Request $request) {
        $request->validate([
            'messages' => 'required|json',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt',
        ]);
        $inputModel = $request->model;
        $model = AIModel::getModel($inputModel);
        $messages = json_decode($request->input('messages'), true);

        if ($request->hasFile('file')) {
            try {
                $content = '';
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'txt') {
                    $content = file_get_contents($file->path());
                }
                if ($extension === 'pdf') {
                    $content = $documentReader->readContent($file);
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                array_unshift($messages, [
                    'role' => 'user',
                    'content' => "Đây là nội dung file được upload: \n\n" . $content . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        }

        return response()->stream(function () use ($messages, $model, $inputModel) {
            $retryCount = 0;
            $maxRetries = 3;
            $retryDelay = 1000; // 1 giây
            $result_text = "";
            $messageInputCharge = []; // Khởi tạo mảng charge
            // Thêm user messages vào messageInputCharge
            foreach ($messages as $message) {
                if ($message['role'] === 'user') {
                    $messageInputCharge[] = $message;
                }
            }
            while ($retryCount < $maxRetries) {
                try {
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $stream = $this->chatGPTService->streamResponse($messages, $model);
                    foreach ($stream as $response) {
                        $text = $response->choices[0]->delta->content ?? '';
                        Log::info('Nhận được văn bản: ' . $text);

                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2;
                        }

                        $data = ['text' => $text];
                        $this->send("update", json_encode($data));

                        $result_text .= $text;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }

                    // Sau khi nhận được toàn bộ response, thêm assistant message vào messageInputCharge
                    if (!empty($result_text)) {
                        $messageInputCharge[] = [
                            "role" => "assistant",
                            "content" => $result_text
                        ];

                        // Charge credit
                        if (!empty($messageInputCharge) && is_array($messageInputCharge)) {
                            $contents = array_column($messageInputCharge, 'content');
                            $combinedContent = trim(implode(' ', $contents));
                            if (!empty($combinedContent)) {
                                try {
                                    Auth::user()->chargeCredit('text', $inputModel, null, 'ai-text', $combinedContent, 13, 13);
                                } catch (\Exception $e) {
                                    Log::error("Error charging credit: " . $e->getMessage());
                                }
                            }
                        }
                    }

                    $this->send("update", "<END_STREAMING_SSE>");
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    break;

                } catch (RequestException $e) {
                    Log::error('RequestException: ' . $e->getMessage());
                    if ($e->getCode() == 529) {
                        $retryCount++;
                        if ($retryCount >= $maxRetries) {
                            echo "data: " . json_encode(['error' => "Không thể lấy dữ liệu sau {$maxRetries} lần thử do giới hạn tỉ lệ."]) . "\n\n";
                        } else {
                            echo "data: " . json_encode(['retry' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."]) . "\n\n";
                            usleep($retryDelay * 1000);
                            $retryDelay *= 2;
                        }
                        ob_flush();
                        flush();
                    }
                } catch (UsageLimitExceededException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 403);
                } catch (\Exception $e) {
                    Log::error('Unexpected Exception: ' . $e->getMessage());
                    if (ob_get_level() > 0) {
                        echo "data: " . json_encode(['error' => 'Đã xảy ra lỗi không mong muốn.']) . "\n\n";
                        ob_flush();
                        flush();
                    }
                    break;
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function handleSendChat(Request $request)
    {
        $message = $request->message. ".Trả về đẩy đủ những thông tin liên quan như(nếu có): tiêu đề, biểu tượng của tiêu đề, các mục ... Please answer me in Tiếng Việt language and also respond in Tiếng Việt language.";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'Bạn là một trợ lý AI.'],
                ['role' => 'user', 'content' => $message]
            ],
        ]);
        $inputModel = $request->model;
        Auth::user()->chargeCredit('text', $inputModel, null, 'ai-chat', $message, 11, 11);
        $res =  $response->json();
        $content = $res["choices"][0]["message"]["content"];
        $chatId = $request["chat_id"] ? $request["chat_id"] : 0;
        $dataChat = [
            "content" => $request->message,
            "model" => "gpt-4o",
            "user_id" => Auth::user()->id,
            "sender" => "guru",
            "content_type" => "text",
            "ai_chat_session_id" => 0
        ];
        if($chatId) {
            $chat = $this->aiChatService->getMessageById($chatId);
            if(!$chat) {
                $chat = $this->aiChatService->storeMessage($dataChat);
                $chatId = $chat->id;
            }
        } else {
            $chat = $this->aiChatService->storeMessage($dataChat);
            $chatId = $chat->id;
        }
        $dataMessageUser = [
            "chat_message_id" => $chatId,
            "user_id" => Auth::user()->id,
            "sender" => "user",
            "content" => $request->message
        ];
        $dataMessageAi = [
            "chat_message_id" => $chatId,
            "user_id" => Auth::user()->id,
            "sender" => "ai",
            "content" => $content
        ];
        $this->aiChatMessageService->storeMessageItem($dataMessageUser);
        $this->aiChatMessageService->storeMessageItem($dataMessageAi);
        return response()->json(["data" => $response->json(), "chat_id" => $chatId]);
    }

    public function getListChat(Request $request) {
        $user = Auth::user();
        $data = $this->aiChatService->getListChat($user->id, $request->all());
        return response()->json(["data" => $data]);
    }

    public function deleteChat(Request $request) {
        try {
            $this->aiChatService->deleteMessageById($request->id);
            $this->aiChatMessageService->deleteMessageByChatId($request->id);
            return response()->json(["success" => true, "message" => "Xóa đoạn chat thành công"]);
        } catch(Exception $ex) {
            return response()->json(["success" => false, "message" => "Có lỗi xảy ra trongq quá trình thực hiện"]);
        } 
    }

    public function getListChatItem(Request $request) {
        $user = Auth::user();
        $data = $this->aiChatMessageService->getListMessageItem($user->id, $request->all());
        return response()->json(["data" => $data]);
    }

    private function sendEvent($event, $data)
    {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        if (ob_get_level() > 0) {
            ob_flush();
            flush();
        }
        flush();
    }

    private function sendError($message)
    {
        $this->sendEvent('error', ['message' => $message]);
    }

    private function isStepSuccessful($stepIndex)
    {
        // Implement logic để kiểm tra step có thành công không
        return true;
    }

    private function cleanText($text) {
        $text = str_replace("\f", "", $text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = trim($text);

        return $text;
    }
}

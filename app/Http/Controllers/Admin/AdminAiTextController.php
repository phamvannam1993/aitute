<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AIModel;
use App\Exceptions\UsageLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\AiTextConversation;
use App\Models\AiTextConversationAdmin;
use App\Models\Operation;
use App\Services\AiAssistantService;
use App\Services\AIAssistantsService;
use App\Services\AITextService;
use App\Services\CalendarService;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\ChatGPTEmbeddingService;
use App\Services\ChatGPTStreamingService;
use App\Services\DocumentReaderService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminAiTextController extends Controller
{
    protected $chatGPTService;
    protected $chatClaudeService;
    private $chatGPTEmbeddingService;
    public function __construct( ChatGPTService $chatGPTService, ChatClaudeService $chatClaudeService, ChatGPTStreamingService $chatGPTStreamingService, ChatGPTEmbeddingService $chatGPTEmbeddingService) {
        $this->chatGPTService = $chatGPTService;
        $this->chatClaudeService = $chatClaudeService;
        $this->chatGPTEmbeddingService = $chatGPTEmbeddingService;
    }

    public function getConversation(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['error' => 'Conversation ID is required'], 404);
        }

        $conversation = AiTextConversationAdmin::find($id);
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
            $conversation = AiTextConversationAdmin::find($id);

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
                $content = "Trả lời bằng giọng điệu là " . $selectedTone . " " . $request->input('content') . ' sử dụng cho sản phẩm ' . $contentInput . " yêu cầu tạo tiêu chí đã chon " . $filterInput;
            } else {
                $content = 'sử dụng cho sản phẩm ' . $contentInput . " yêu cầu tạo tiêu chí đã chon " . $filterInput;
            }

            $messages = [
                [
                    "role" => $request->input('role', 'user'),
                    "content" => $content
                ],
            ];

            $conversation = AiTextConversationAdmin::create(['messages' => $messages]);

            return response()->json($conversation);
        }
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
        $conversation = AiTextConversationAdmin::find($conversationId);
        $isActiveGdth = $request->boolean('isActiveGdthLeft') ?? false;
        $selectedTone = $request->input('selectedToneLeft') ?? '';
        $messageSearch = $request->message ?? '';
        return response()->stream(function () use ($request, $conversationId, $conversation, $isActiveGdth, $selectedTone, $messageSearch) {
            $newSteps = json_decode($request->steps, true);
            $ai_assistant_id = $request->ai_assistant_id ?? '';

            $messages = [];
            $fileContent = '';

            // Lấy messages cũ từ database nếu có conversationId
            if ($conversationId) {
                $conversation = AiTextConversationAdmin::find($conversationId);
                if ($conversation && !empty($conversation->messages)) {
                    $messages = $conversation->messages;
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
                                    $fileContent = file_get_contents($file->path());
                                    break;
                                case 'pdf':
                                    $fileContent = $documentReader->readContent($file);
                                    break;
                                case 'doc':
                                case 'docx':
                                    $fileContent = $documentReader->readContent($file, 'vi');
                                    break;
                            }

                            if ($fileContent) {
                                $messages[] = [
                                    'role' => 'user',
                                    'content' => "Đây là nội dung file được upload: \n\n" . $fileContent . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
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
                        $conversation = AiTextConversationAdmin::create([
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
        $conversation = AiTextConversationAdmin::find($conversationId);
        $isActiveGdth = $request->boolean('isActiveGdthLeft') ?? false;
        $selectedTone = $request->input('selectedToneLeft') ?? '';
        $messageSearch = $request->message ?? '';
        return response()->stream(function () use ($request, $conversationId, $conversation, $model, $inputModel, $isActiveGdth, $selectedTone, $messageSearch) {
            $newSteps = json_decode($request->steps, true);
            $ai_assistant_id = $request->ai_assistant_id ?? '';
            $messages = [];
            $fileContent = '';

            // Lấy messages cũ từ database
            if ($conversationId) {
                $conversation = AiTextConversationAdmin::find($conversationId);
                if ($conversation && !empty($conversation->messages)) {
                    $messages = $conversation->messages;
                }
            }
            // Xử lý file upload
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
                                    $fileContent = file_get_contents($file->path());
                                    break;
                                case 'pdf':
                                    $fileContent = $documentReader->readContent($file);
                                    break;
                                case 'doc':
                                case 'docx':
                                    $fileContent = $documentReader->readContent($file, 'vi');
                                    break;
                            }

                            if ($fileContent) {
                                $messages[] = [
                                    'role' => 'user',
                                    'content' => "Đây là nội dung file được upload: \n\n" . $fileContent . "\n\nHãy sử dụng thông tin từ file này để trả lời câu hỏi tiếp theo."
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
                        $conversation = AiTextConversationAdmin::create([
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

                $this->sendEvent('complete', []);
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);

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

    private function cleanText($text) {
        $text = str_replace("\f", "", $text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = trim($text);

        return $text;
    }

}

<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Constants\Sender;
use App\Http\Controllers\Controller;
use App\Models\MessageHistories;
use App\Services\AIChatService;
use App\Services\ChatGPTService;
use App\Services\DocumentReaderService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AIChatController extends Controller
{
    private $aIChatService;
    private $chatGPTService;
    public function __construct(ChatGPTService $chatGPTService, AIChatService $aIChatService)
    {
        $this->chatGPTService = $chatGPTService;
        $this->aIChatService = $aIChatService;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $per_page = 8;

        // Get unique user_page_ids with their latest created_at
        $userPageIds = MessageHistories::where('user_id', $user->id)
            ->select('user_page_id', DB::raw('MAX(created_at) as latest_message'))
            ->groupBy('user_page_id')
            ->orderBy('latest_message', 'desc')
            ->paginate($per_page);

        // Get all messages for these user_page_ids
        $histories = MessageHistories::where('user_id', $user->id)
            ->whereIn('user_page_id', $userPageIds->pluck('user_page_id'))
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('user_page_id');

        return Inertia::render('Client/AIChat/Index', [
            'data' => [
                'conversations' => $histories,
                'pagination' => $userPageIds
            ]
        ]);
    }
    public function getSessionId(Request $request)
    {
        $sessionId = $request->id;

        if (!$sessionId) {
            $latestSession = $this->aIChatService->getLatestSession();

            if ($latestSession) {
                $hasMessages = $this->aIChatService->checkSessionHasMessages($latestSession->id);

                if (!$hasMessages) {
                    $sessionId = $latestSession->id;
                }
            }

            if (!$sessionId) {
                $sessionId = $this->aIChatService->createSessions();
            }
        }
        return response()->json([
            'success' => true,
            'session_id' => $sessionId
        ]);
    }

    public function messageHistories($sessionId)
    {
        $histories = $this->aIChatService->getHistories($sessionId);
        return response()->json([
            'success' => true,
            'data' => $histories
        ]);
    }
    public function chat(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file' => [
                'required_without:message',
                'file',
                'mimes:doc,docx,pdf,png,jpg,jpeg,webp,mp3,wav,webm',
            ],
        ]);

        $content = '';
        $fileUrl = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileType = $file->getClientMimeType();
                $languageKey = $request->languageKey;
                $fileUrl = $file->store(config('constants.s3_folder.ai_chat'), 's3');
                $documentReader = app(DocumentReaderService::class);
                $content = $documentReader->readContent($file, $languageKey);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        }
        $message = $request->message;
        $inputModel = $request->model;
        $lang = $request->lang;
        $file = $request->file;
        $session_id = $request->session_id;

        $model = AIModel::getModel($inputModel);

        $dataUserMessage = [
            'ai_chat_session_id' => $session_id,
            'sender' => Sender::USER,
            'content_type' => $file ? 'file' : 'text',
            'file_url' => $fileUrl,
            'content' => $message,
            'model' => $model,
            'language' => $lang,
            'file_type' => $fileType
        ];
        $userMessage = $this->aIChatService->storeMessage($dataUserMessage);
        $promptTemplate = config('chatgpt.simple-message');
        $prompt = str_replace([':mesage', ':lang', ':file_content'], [$message, $lang, $content], $promptTemplate);

        // Check credit
        $creditController = new CreditController();
        $request->merge([
            'text' => $prompt,
            'action' => 'ai-chat',
            'type' => 'text'
        ]);
        $result = $creditController->check_credit($request);
        $success = $result->getData()->success;
        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => ['Vui lòng nạp thêm credit']
            ], 403);
        }
        $response = $this->chatGPTService->getResponseChatBot($prompt, null, $model, null, true);

        if ($response) {
            $dataAIMessage = [
                'ai_chat_session_id' => $session_id,
                'sender' => Sender::AI,
                'content_type' => 'text',
                'file_url' => null,
                'content' => $response,
                'model' => $model,
                'language' => $lang
            ];
            $record = $this->aIChatService->storeMessage($dataAIMessage);
            // Trừ tiền credit user
            Auth::user()->chargeCredit('text', $inputModel, null, 'ai-chat', $prompt, 11, 11);
            return response()->json([
                'success' => true,
                'response' => $response,
                'credits' => $user->credit ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Đã có lỗi xảy ra vui lòng liên hệ với quản trị viên.'
        ], 500);
    }
    public function deleteSession(Request $request)
    {
        $sessionId = $request->id;
        $response = $this->aIChatService->deleteSessionById($sessionId);
        return $response;
    }
    public function deleteMessage(Request $request)
    {
        $messageId = $request->id;
        $response = $this->aIChatService->deleteMessageById($messageId);
        return $response;
    }

    public function getChatHistory(Request $request)
    {
        $histories = $this->aIChatService->getSessionChatHistories();
        return response()->json([
            'success' => true,
            'data' => $histories
        ]);
    }

    public function getMessageHistories()
    {
        $user = Auth::user();
        $per_page = 8;

        // Get unique user_page_ids with their latest created_at
        $userPageIds = MessageHistories::where('user_id', $user->id)
            ->select('user_page_id', 'page_id', DB::raw('MAX(created_at) as latest_message'))
            ->groupBy('user_page_id', 'page_id')
            ->orderBy('latest_message', 'desc')
            ->paginate($per_page);

        // Get all messages for these user_page_ids
        $messages = MessageHistories::where('user_id', $user->id)
            ->whereIn('user_page_id', $userPageIds->pluck('user_page_id'))
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Get Facebook page info for all page_ids
        $pageIds = $messages->pluck('page_id')->unique()->filter()->toArray();
        
        if (!empty($pageIds)) {
            $facebookConfigsQuery = \App\Models\FacebookConfigs::whereIn('page_id', $pageIds)
                ->select('page_id', 'page_name', 'page_url');
            
            $facebookConfigs = $facebookConfigsQuery->get()->keyBy('page_id');
            
            // Add page_name and page_url to each message
            foreach ($messages as $message) {
                if (isset($message->page_id) && $facebookConfigs->has($message->page_id)) {
                    $pageInfo = $facebookConfigs[$message->page_id];
                    $message->page_name = $pageInfo->page_name ?: 'Unnamed Page';
                    $message->page_url = $pageInfo->page_url;
                } else {
                    $message->page_name = '';
                    $message->page_url = null;
                }
            }
        } else {
            // Set default values if no page_ids
            foreach ($messages as $message) {
                $message->page_name = '';
                $message->page_url = null;
            }
        }
        
        // Group by user_page_id for the response format
        $histories = $messages->groupBy('user_page_id');

        return Inertia::render('Client/AIChat/History', [
            'data' => [
                'conversations' => $histories,
                'pagination' => $userPageIds
            ]
        ]);
    }

    public function checkAllMessagesStatus(Request $request)
    {
        try {
            $user_page_ids = $request->input('user_page_ids', []);
            $statuses = [];
            $user = Auth::user();
            $inputModel = 'o3-mini';

            foreach ($user_page_ids as $user_page_id) {
                // Lấy tất cả tin nhắn trong đoạn chat
                $messages = MessageHistories::where('user_page_id', $user_page_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($messages->count() > 0) {
                    // Tạo prompt để phân tích đoạn chat
                    $chatContent = $messages->map(function ($msg) {
                        return ($msg->role == 'user' ? 'User: ' : 'AI: ') . $msg->text_content;
                    })->join("\n");

                    $promptTemplate = "Dưới đây là một đoạn chat giữa user và AI. Hãy phân tích và cho biết đoạn chat này có phải là khách hàng có thể sử mua hàng hay không.

                    Khách hàng có thể sử mua hàng là những đoạn chat:
                    - Qua nội dung tin nhắn có thể thấy user đang có nhu cầu mua hàng
                    - Qua nội dung tin nhắn có thể thấy user đang có nhu cầu tìm hiểu thông tin sản phẩm
                    - Qua nội dung tin nhắn có thể thấy user đang có nhu cầu tìm hiểu về các câu hỏi khác liên quan đến sản phẩm

                    Bắt buộc trả về cấu trúc JSON chứa:
                    1. 'comment': một câu nhận xét về đoạn chat đó (chỉ một câu nhận xét)
                    2. 'status': một giá trị boolean
                    - true: nếu khách hàng là tiềm năng 'nóng' (có ý định mua hàng rõ ràng hoặc quan tâm cao đến sản phẩm)
                    - false: nếu khách hàng là tiềm năng 'lạnh' (chỉ hỏi thông tin chung, ít quan tâm hoặc không có ý định mua)

                    Trả về kết quả theo định dạng: 
                    {\"comment\": \"Nhận xét của bạn\", \"status\": true hoặc false}

                    Đoạn chat cần phân tích:
                    ----------------
                    {$chatContent}
                    ----------------";

                    Auth::user()->chargeCredit(
                        'text',
                        $inputModel,
                        null,
                        'ai-text',
                        $promptTemplate,
                        36,
                        36
                    );
                    $response = $this->chatGPTService->getResponse($promptTemplate, null, $inputModel);

                    // Parse JSON response
                    try {
                        $responseData = json_decode($response, true);
                        
                        // Ensure status is boolean
                        if (isset($responseData['status']) && is_string($responseData['status'])) {
                            // Convert string representations to boolean if needed
                            if (strtolower($responseData['status']) === 'true' || $responseData['status'] === 'nóng') {
                                $responseData['status'] = true;
                            } elseif (strtolower($responseData['status']) === 'false' || $responseData['status'] === 'lạnh') {
                                $responseData['status'] = false;
                            }
                        }
                        
                        $statuses[$user_page_id] = $responseData;
                    } catch (\Exception $e) {
                        // If JSON parsing fails, use the whole response as comment and set status as null
                        $statuses[$user_page_id] = [
                            'comment' => $response,
                            'status' => null
                        ];
                    }
                }
            }

            // save comment and status to database by user_page_id
            foreach ($statuses as $user_page_id => $data) {
                $updateData = [];
                
                if (is_array($data)) {
                    if (isset($data['comment'])) {
                        $updateData['comment'] = $data['comment'];
                    }
                    if (isset($data['status'])) {
                        $updateData['status'] = $data['status'];
                    }
                } else {
                    // Backward compatibility for old format
                    $updateData['comment'] = $data;
                }
                
                MessageHistories::where('user_page_id', $user_page_id)->update($updateData);
            }

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking message status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái tin nhắn'
            ], 500);
        }
    }
}
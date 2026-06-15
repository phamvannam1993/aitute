<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\AIChatMessage;
use App\Models\AIChatSession;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Repositories\SemesterMaterialRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class AIChatService
{
    public function __construct() {}

    public function createSessions()
    {
        try {
            $session = AIChatSession::create([
                'user_id' => auth('web')->id(),
                'ai_assistant_id' => 1 // hard code
            ]);
            if (!$session) {
                Log::error('Tạo phiên chat mới thất bại.');
                return null;
            }
            return $session->id;
        } catch (Exception $e) {
            Log::error('Lỗi khi tạo phiên chat: ' . $e->getMessage());
            return null;
        }
    }

    public function getHistories($sessionId)
    {
        try {
            $per_page = 2;
            $page = request()->get('page', 1);

            // Lấy 2 tin nhắn theo trang
            $messages = AIChatMessage::where('ai_chat_session_id', '=', $sessionId)
                ->select('id', 'sender', 'file_url', 'content', 'file_type')
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $per_page)
                ->take($per_page)
                ->get()
                ->sortBy(function($message) {
                    return $message->sender === 'ai' ? 1 : 0;
                })
                ->values();

            return new LengthAwarePaginator(
                $messages,
                AIChatMessage::where('ai_chat_session_id', '=', $sessionId)->count(),
                $per_page,
                $page,
                ['path' => request()->url()]
            );

        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử tin nhắn: ' . $e->getMessage());
            return null;
        }
    }

    public function storeMessage($data)
    {
        try {
            $message = AIChatMessage::create($data);
            if (!$message) {
                Log::error('Lưu trữ tin nhắn thất bại.');
                return null;
            }
            return $message;
        } catch (Exception $e) {
            Log::error('Lỗi khi lưu trữ tin nhắn: ' . $e->getMessage());
            return null;
        }
    }
    public function getSessionChatHistories()
    {
        try {
            $per_page = 8;
            $userId = auth('web')->id();

            $sessions = AIChatSession::with('oldestMessage')
                ->where('user_id', $userId)
                ->orderBy('id', 'desc')
                ->paginate($per_page);

            return $sessions;
        } catch (Exception $e) {
            Log::error('Server error:' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function deleteSessionById($sessisonId)
    {
        $userId = auth('web')->id();
        $session = AIChatSession::where('id', $sessisonId)->where('user_id', $userId)->first();
        if (!$session) {
            return response()->json(['message' => 'Phiên trò chuyện không tồn tại.'], 404);
        }
        AIChatMessage::where('ai_chat_session_id', $sessisonId)->delete();
        $session->delete();
        return response()->json(['message' => 'Xoá phiên trò chuyện thành công.'], 200);
    }

    public function deleteMessageById($messageId)
    {
        $message = AIChatMessage::find($messageId);
        if (!$message) {
            return response()->json(['message' => 'Tin nhắn không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá tin nhắn thành công.'], 200);
    }

    public function getLatestSession()
    {
        $userId = auth('web')->id();
        return AIChatSession::where('user_id', $userId)->orderBy('created_at', 'desc')->first();
    }

    public function checkSessionHasMessages($sessionId)
    {
        return AIChatMessage::where('ai_chat_session_id', $sessionId)->exists();
    }

    public function getMessagesBySessionId($sessionId)
    {
        return AIChatMessage::where('ai_chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getListChat($userId, $params) {
        $limit = isset($params["limit"]) ? $params["limit"] : 100;
        return AIChatMessage::where('user_id', $userId)->where("sender", "guru")->orderBy("id", "DESC")->paginate($limit);
    }

    public function getMessageById($id) {
        return AIChatMessage::where('id', $id)->first();
    }
}

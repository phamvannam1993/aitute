<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\AIAssistantHistories;
use Exception;
use Illuminate\Support\Facades\Log;

class AITextService
{
    protected $aIAssistantHistories;

    public function __construct(AIAssistantHistories $aIAssistantHistories)
    {
        $this->aIAssistantHistories = $aIAssistantHistories;
    }

    public function getListMessageByUserId($userId, $ai_assistant_id, $per_page = 10)
    {
        $result = AIAssistantHistories::with(['conversation'])->where('user_id', $userId)
            ->where('ai_assistant_id', $ai_assistant_id)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        return $result;
    }

    public function storeMessage($data)
    {
        try {

            $message = $this->aIAssistantHistories->updateOrCreate(
                [
                    'id' => $data['id'],
                    'user_id' => auth('web')->id(),
                    'ai_assistant_id' => $data['ai_assistant']['id']
                ],
                [
                    'response' => $data['response'],
                    'prompt' => $data['prompt'],
                    'type' => $data['ai_assistant']['type'],
                    'media_link' => $data['media_link'],
                    'css_style' => $data['css_style'],
                    'ai_text_conversation_id' => !empty($data['ai_text_conversation_id']) ? $data['ai_text_conversation_id'] : null,
                    'step_ais' => json_encode($data['step_ais']),
                ]
            );

            if (!$message) {
                Log::error('Lưu trữ tin nhắn thất bại.');
                return response()->json(['success' => false, 'message' => 'Lưu trữ tin nhắn thất bại.'], 500);
            }

            if ($message->wasRecentlyCreated) {
                // Nếu là bản ghi mới, lấy toàn bộ thông tin
                $fullRecord = $this->aIAssistantHistories->find($message->id);
                return response()->json(['success' => true, 'data' => $fullRecord]);
            }

            return response()->json(['success' => true, 'data' => $message]);
        } catch (Exception $e) {
            Log::error('Lỗi khi lưu trữ tin nhắn: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi khi lưu trữ tin nhắn.'], 500);
        }
    }

    public function deleteMessageById($messageId)
    {
        try {
            $message = AIAssistantHistories::find($messageId);
            if (!$message) {
                return response()->json(['message' => 'Tin nhắn không tồn tại.'], 404);
            }
            $message->delete();
            return response()->json(['message' => 'Xoá tin nhắn thành công.'], 200);
        } catch (\Throwable $e) {
            Log::error('Error deleting message:', ['error' => $e->getMessage()]); // Ghi log lỗi
            return response()->json(['message' => 'Xoá tin không thành công.'], 500);
        }
    }
}

<?php

namespace App\Services;

use App\Models\AIChatMessageItem;
use App\Models\AIChatSession;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\Log;

class AIChatMessageItemService
{
    public function __construct() {}

    public function storeMessageItem($data)
    {
        try {
            $message = AIChatMessageItem::create($data);
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

    public function deleteMessageByChatId($chatId)
    {
       return AIChatMessageItem::where("chat_message_id", $chatId)->delete();
    }

    public function getListMessageItem($userId, $params) {
        $limit = isset($params["limit"]) ? $params["limit"] : 20;
        return AIChatMessageItem::where('user_id', $userId)->where("chat_message_id", $params["chat_id"])->orderBy("id", "DESC")->paginate($limit);
    }
}

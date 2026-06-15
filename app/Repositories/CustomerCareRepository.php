<?php

namespace App\Repositories;

use App\Services\ChatGPTService;
use App\Models\MessageHistories;
use Illuminate\Support\Facades\DB;

class CustomerCareRepository
{
    protected $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }
    // Kiểm tra xem người dùng có lịch sử tin nhắn trong bảng message_histories không
    public function checkUserMessageHistory($userId)
    {
        return MessageHistories::where('user_id', $userId)->exists(); 
    }

    // Lấy lịch sử tin nhắn của người dùng
    public function getMessageHistories($userId, $perPage = 8)
    {
        // Kiểm tra người dùng có lịch sử tin nhắn không
        if (!$this->checkUserMessageHistory($userId)) {
            return null; // Nếu không có lịch sử tin nhắn, trả về null
        }

        // Lấy user_page_ids với thông tin về tin nhắn mới nhất
        $userPageIds = MessageHistories::where('user_id', $userId)
            ->select('user_page_id', 'page_id', DB::raw('MAX(created_at) as latest_message'))
            ->groupBy('user_page_id', 'page_id')
            ->orderBy('latest_message', 'desc')
            ->paginate($perPage);

        // Lấy tất cả các tin nhắn cho các user_page_id này
        $messages = MessageHistories::where('user_id', $userId)
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

        return [
            'conversations' => $histories,
            'pagination' => $userPageIds
        ];
    }

    // Kiểm tra trạng thái của tất cả các tin nhắn
    public function checkAllMessagesStatus($userPageIds)
    {
        $statuses = [];
        
        foreach ($userPageIds as $user_page_id) {
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

                // Gọi ChatGPT service để phân tích nội dung
                $response = $this->chatGPTService->getResponse($promptTemplate, null, 'o3-mini');

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

        // Save comment and status to database by user_page_id
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

        return $statuses;
    }
}
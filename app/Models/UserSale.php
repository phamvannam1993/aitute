<?php

namespace App\Models;

use App\Services\ChatGPTService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserSale extends Model
{
    use HasFactory;
    protected $table = 'user_sale';

    protected $fillable = [
        'user_id',
        'sale_id',
        'conversation_id',
        'message_id',
        'fb_message_psid',
        'name',
        'email',
        'phone',
        'age',
        'treatment',
        'appointment',
        'user_sale_contact_status_id',
        'contact_note',
        'meta',
        'order_date',
        'conversation_status'
    ];

    protected $casts = [
        'meta' => 'array',
        'order_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userSaleContactStatus()
    {
        return $this->belongsTo(UserSaleContactStatus::class);
    }

    public function aiTuTeConversation()
    {
        return $this->belongsTo(AiTuTeConversation::class, 'conversation_id');
    }

    public function getConversationStatus()
    {
        $status = $this->conversation_status;

        if ($status === null) {
            $messages = data_get($this, 'aiTuTeConversation.messages');
            if (empty($messages) || $messages->count() == 0) {
                return 'cool';
            }
            $chatContent = $messages->map(function ($msg) {
                $content = $msg->content;
                $result = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($result)) {
                    foreach ($result as $value) {
                        if(data_get($value, 'type') == 'title_form') {
                            $content = data_get($value, 'content');
                        }
                    }
                }
                return ($msg->sender == 'user' ? 'User: ' : 'AI: ') . $content;
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

            try {
                // Gọi ChatGPT service để phân tích nội dung
                $response = app(ChatGPTService::class)->getResponse($promptTemplate, null, 'o3-mini');
                $responseData = json_decode($response, true);

                // Ensure status is boolean
                if (data_get($responseData, 'status')) {
                   $this->conversation_status = 'hot';
                }else {
                    $this->conversation_status = 'cool';
                }
                $this->update();
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return $status;
    }
}

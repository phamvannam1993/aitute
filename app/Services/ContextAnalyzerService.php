<?php

namespace App\Services;

use App\Exceptions\DomainException;
use Illuminate\Http\Response;
use App\Helper\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenAI;

class ContextAnalyzerService
{
    private $messages;
    private $patterns = [
        'analysis' => [
            'dự.*báo',
            'phân.*tích',
            'thế.*nào',
            'như.*nào',
            'xu.*hướng',
            'giá.*[sẽ|có]',
            'thị.*trường'
        ],
        'advice' => [
            'nên.*không',
            'có.*nên',
            'tư.*vấn',
            'khuyên',
            'giúp.*[tôi|mình]',
            'làm.*sao'
        ],
        'knowledge' => [
            'là.*gì',
            'tại.*sao',
            'vì.*sao',
            'giải.*thích',
            'định.*nghĩa',
            'khái.*niệm'
        ],
        'creative' => [
            'viết',
            'sáng.*tạo',
            'thiết.*kế',
            'ý.*tưởng'
        ]
    ];

    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    public function detect(): string
    {
        // Lấy message cuối cùng của user
        $lastUserMessage = $this->getLastUserMessage();
        if (!$lastUserMessage) {
            return 'casual';
        }

        // Chuyển message về chữ thường và bỏ dấu
        $normalizedMessage = $this->normalizeText($lastUserMessage);

        // Kiểm tra từng pattern
        foreach ($this->patterns as $context => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match("/$pattern/i", $normalizedMessage)) {
                    return $context;
                }
            }
        }

        // Phân tích sâu hơn dựa trên độ dài và từ khóa
        return $this->analyzeDeeper($normalizedMessage);
    }

    private function getLastUserMessage(): ?string
    {
        $userMessages = array_filter($this->messages, function($message) {
            return isset($message['role']) && $message['role'] === 'user';
        });

        if (empty($userMessages)) {
            return null;
        }

        $lastMessage = end($userMessages);
        return $lastMessage['content'] ?? null;
    }

    private function normalizeText($text): string
    {
        // Chuyển về chữ thường
        $text = mb_strtolower($text, 'UTF-8');

        // Thêm xử lý các ký tự đặc biệt nếu cần
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        return $text;
    }

    private function analyzeDeeper($message): string
    {
        // Phân tích dựa trên độ dài
        $wordCount = str_word_count($message);

        // Phân tích dựa trên các từ khóa phổ biến
        $commonKeywords = [
            'analysis' => ['thị trường', 'giá', 'xu hướng', 'dự báo', 'phân tích'],
            'advice' => ['nên', 'khuyên', 'tư vấn', 'giúp'],
            'knowledge' => ['là gì', 'định nghĩa', 'giải thích'],
            'creative' => ['viết', 'tạo', 'thiết kế']
        ];

        $contextScores = [
            'analysis' => 0,
            'advice' => 0,
            'knowledge' => 0,
            'creative' => 0,
            'casual' => 0
        ];

        // Tính điểm cho từng context
        foreach ($commonKeywords as $context => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($message, $keyword) !== false) {
                    $contextScores[$context]++;
                }
            }
        }

        // Thêm điểm dựa trên độ dài
        if ($wordCount > 15) {
            $contextScores['analysis'] += 2;
        } elseif ($wordCount < 5) {
            $contextScores['casual'] += 2;
        }

        // Trả về context có điểm cao nhất
        $maxScore = max($contextScores);
        if ($maxScore === 0) {
            return 'casual';
        }

        return array_search($maxScore, $contextScores);
    }

    // Có thể thêm các methods phụ trợ khác
    public function getConfidence(): float
    {
        // Tính độ tin cậy của việc phân loại
        // Implement logic tính confidence ở đây
        return 0.8;
    }

    public function getSuggestedPrompt(): ?string
    {
        // Gợi ý prompt bổ sung dựa trên context
        $context = $this->detect();

        $suggestedPrompts = [
            'analysis' => "Hãy phân tích chi tiết với số liệu và ví dụ cụ thể",
            'advice' => "Đưa ra lời khuyên với pros/cons rõ ràng",
            'knowledge' => "Giải thích đơn giản, kèm ví dụ thực tế",
            'creative' => "Sáng tạo nhưng vẫn đảm bảo tính thực tế",
            'casual' => null
        ];

        return $suggestedPrompts[$context] ?? null;
    }
}

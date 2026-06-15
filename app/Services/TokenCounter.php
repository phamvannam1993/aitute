<?php

namespace App\Services;

use App\Models\StepAi;
use App\Repositories\AiAssistantRepository;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TokenCounter
{
    private const USE_CASES = [
        'general' => [
            'gpt-4o' => 2.0,
            'gpt-3.5-turbo' => 1.5,
            'gpt-4o-mini' => 1.8,
            'claude-3-5-sonnet-20240620' => 2.2,
            'o3-mini' => 1.5
        ],
        'summary' => [
            'gpt-4o' => 0.6,
            'gpt-3.5-turbo' => 0.5,
            'gpt-4o-mini' => 0.5,
            'claude-3-5-sonnet-20240620' => 0.7
        ],
        'translation' => [
            'gpt-4o' => 1.3,
            'gpt-3.5-turbo' => 1.2,
            'gpt-4o-mini' => 1.2,
            'claude-3-5-sonnet-20240620' => 1.4,
            'o3-mini' => 1.0
        ],
        'chat' => [
            'gpt-4o' => 2.5,
            'gpt-3.5-turbo' => 2.0,
            'gpt-4o-mini' => 2.2,
            'claude-3-5-sonnet-20240620' => 2.8,
            'o3-mini' => 2.0
        ],
        'code' => [
            'gpt-4o' => 3.0,
            'gpt-3.5-turbo' => 2.5,
            'gpt-4o-mini' => 2.8,
            'claude-3-5-sonnet-20240620' => 3.2
        ],
        'analysis' => [
            'gpt-4o' => 2.8,
            'gpt-3.5-turbo' => 2.3,
            'gpt-4o-mini' => 2.5,
            'claude-3-5-sonnet-20240620' => 3.0
        ]
    ];

    public function estimateOutputTokens($input_tokens, $model, $use_case = 'general')
    {
        // Validate model
        if (!$this->isValidModel($model)) {
            throw new \Exception('Unsupported estimateOutputTokens model: ' . $model);
        }

        // Validate use case
        if (!isset(self::USE_CASES[$use_case])) {
            $use_case = 'general';
        }

        // Get ratio for specific model and use case
        $ratio = self::USE_CASES[$use_case][$model];

        // Calculate estimated output tokens
        $estimated_tokens = ceil($input_tokens * $ratio);

        // Add safety buffer based on model
        return $this->addSafetyBuffer($estimated_tokens, $model);
    }

    public function countTokens($text, $model)
    {
        switch ($model) {
            case 'gpt-4o':
            case 'gpt-3.5-turbo':
            case 'o3-mini':
            case 'gpt-4o-mini':
                return $this->estimateGPTTokens($text);
                break;
            case 'claude-3-5-sonnet-20240620':
                return $this->estimateClaudeTokens($text);
                break;
            default:
                throw new \Exception('Unsupported model');
        }
    }

    private function estimateGPTTokens($text)
    {
        $text = trim($text);

        // Đếm số từ
        $words = preg_split('/\s+/u', $text);
        $wordCount = count($words);

        // Đếm số ký tự Unicode
        $charCount = mb_strlen($text, 'UTF-8');

        // Với tiếng Việt
        // GPT: khoảng 3 ký tự = 1 token
        $tokensByChars = ceil($charCount / 3);

        // Ước tính theo số từ
        $tokensByWords = ceil($wordCount * 1.5);

        // Lấy giá trị lớn hơn và thêm buffer 10%
        return ceil(max($tokensByChars, $tokensByWords) * 1.1);
    }

    private function estimateClaudeTokens($text)
    {
        $text = trim($text);

        // Đếm số ký tự Unicode
        $charCount = mb_strlen($text, 'UTF-8');

        // Claude: khoảng 2.5 ký tự = 1 token với tiếng Việt
        $estimatedTokens = ceil($charCount / 2.5);

        // Thêm buffer 20% vì Claude thường count nhiều token hơn
        return ceil($estimatedTokens * 1.2);
    }

    private function isValidModel($model)
    {
        return in_array($model, [
            'gpt-4o',
            'gpt-3.5-turbo',
            'gpt-4o-mini',
            'claude-3-5-sonnet-20240620',
            'o3-mini'
        ]);
    }

    private function addSafetyBuffer($tokens, $model)
    {
        $buffers = [
            'gpt-4o' => 1.1,            // 10% buffer
            'gpt-3.5-turbo' => 1.15,   // 15% buffer
            'gpt-4o-mini' => 1.15,      // 15% buffer
            'claude-3-5-sonnet-20240620' => 1.2,  // 20% buffer
            'o3-mini' => 1.0
        ];

        return ceil($tokens * $buffers[$model]);
    }
}
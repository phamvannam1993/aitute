<?php

namespace App\Helper;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI;

class AudioReaderService
{
    public function extractAudioToText(string $audioPath, string $languageKey = 'en'): ?string
    {
        $apiKey = config('chatgpt.api_key');
        $apiEndpoint = config('chatgpt.transcriptions_audio_endpoint');
        $transcriptionModel = config('chatgpt.transcriptions_model');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->timeout(60) // Tăng thời gian chờ lên 60 giây
            ->attach('file', fopen($audioPath, 'r'), basename($audioPath)) // Đính kèm tệp âm thanh
            ->post($apiEndpoint, [
                'model' => $transcriptionModel,
                'language' => $languageKey,
                // 'temperature' => 0.0, // Tham số độ ngẫu nhiên
                'prompt' => 'The audio provided may have moments of silence, do not make up words to fill in extended times of silence',
            ]);
    
            $responseData = json_decode($response->getBody(), true);
    
            return $responseData['text'] ?? null; // Trả về nội dung hoặc null nếu không có
        } catch (Exception $e) {
            Log::error('Error during audio transcription: ' . $e->getMessage());
            return null; // Hoặc ném ngoại lệ tùy thuộc vào cách bạn muốn xử lý lỗi
        }
    }
}

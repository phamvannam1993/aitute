<?php

namespace App\Http\Controllers\Client;

use App\Helper\FFmpegHelper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeechToTextController extends Controller
{
    public function callGoogleSpeechToText(Request $request) {
        if (!$request->hasFile('audio')) {
            Log::error('Không nhận được file ghi âm');
            return response()->json(['error' => 'No audio file received'], 400);
        }

        $audioFile = $request->file('audio');
        $text = '';
        $apiKey = config('services.google_speech_to_text.api_key');
        $url = config('services.google_speech_to_text.url');

        $extension = $audioFile->extension();
        Log::info('API Key: '.$apiKey);
        Log::info('API Endpoint: '.$url);
        Log::info('Audio extension: '.$extension);
        try {
            if (in_array($extension, ['webm', 'mp4'])) {
                Log::info('Bắt đầu chuyển đổi audio: ' . $extension);
                $content = FFMpegHelper::convertToMp3Content2($audioFile);
                Log::info('Chuyển đổi audio thành công');
            } else {
                $content = file_get_contents($audioFile->getRealPath());
                if (!$content) {
                    throw new Exception("Không thể đọc nội dung file âm thanh");
                }
            }
        } catch (Exception $e) {
            Log::error('Lỗi khi chuyển đổi âm thanh: ' . $e->getMessage());
            return response()->json(['error' => 'Error converting audio to mp3'], 500);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Goog-Api-Key' => $apiKey,
        ])->post($url, [
            'config' => [
                'encoding' => 'MP3',
                'sampleRateHertz' => 16000,
                'languageCode' => 'vi-VN',
                'model' => 'default',
            ],
            'audio' => [
                'content' => base64_encode($content),
            ],
        ]);
        if ($response->failed()) {
            Log::error('Yêu cầu API không thành công với mã trạng thái ' . $response->status() . ': ' . $response->body());
            return response()->json(['error' => 'API request failed'], $response->status());
        }

        $result = json_decode($response->getBody(), true);
        if (!isset($result['results']) || empty($result['results'])) {
            Log::error('Phản hồi API không chứa "results": ' . $response->body());
            return response()->json(['error' => 'API response does not contain results'], 500);
        }

        foreach ($result['results'] as $item) {
            $text .= $item['alternatives'][0]['transcript'] . ' ';
        }

        return response()->json(['text' => trim($text)]);
    }
}

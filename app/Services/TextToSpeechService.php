<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TextToSpeechService
{
    public function ssml2Audio($ssml, $language = 'vi-VN')
    {
        try {
            $apiKey = config('services.google_text_to_speech.api_key');
            $url = config('services.google_text_to_speech.url_v1_beta');

            Log::info('Google Text to Speech bắt đầu gửi request: ' . $ssml);

            $resp = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8',
                'X-Goog-Api-Key' => $apiKey,
            ])->post($url, [
                'input' => [
                    'ssml' => $ssml
                ],
                'voice' => [
                    'languageCode' => $language,
                ],
                'audioConfig' => [
                    'audioEncoding' => 'MP3',
                    'pitch' => 0,
                    'speakingRate' => 1,
                ]
            ]);
            Log::info("response google text to speech: ====================" . $resp);
            if ($resp->getStatusCode() == 200) {
                $audio = base64_decode($resp->json()['audioContent']);
                $path = 'audio/' . Str::uuid() . '.mp3';
                Storage::disk('public')->put($path, $audio);
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('Không tạo được audio');
                }
                return $path;
            }
        } catch (\Exception $e) {
            Log::error('Google Text to Speech không tạo được audio: ' . $e->getMessage());
            return null;
        }
    }

    public function convertSSMLtoText($ssml)
    {
        $text = preg_replace('/<[^>]+>/', '', $ssml);

        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $text = preg_replace('/([.!?])\s+/', "$1\n", $text);

        return $text;
    }

    public function findImageForVideo($keyword)
    {
        $images = [];

        $response = Http::withHeaders([
            'Authorization' => config('common.pexels_api_key')
        ])->get('https://api.pexels.com/v1/search', [
            'query' => $keyword,
            'per_page' => 3,
        ]);

        $results = $response->json();
        Log::info("Pexels Result: " . json_encode($results));

        if (isset($results['photos']) && !empty($results['photos'])) {
            foreach ($results['photos'] as $item) {
                $images[] = $item['src']['original'];
            }
        }

        if (empty($images)) { // Nếu không có ảnh từ Pexels, thử Pixabay
            $response = Http::get('https://pixabay.com/api/', [
                'key' => config('common.pixabay_api_key'),
                'q' => $keyword,
                'page' => 1,
                'per_page' => 3,
                'image_type' => 'all',
            ]);

            $results = $response->json();
            Log::info("Pixabay Result: " . json_encode($results));
            if (isset($results['hits']) && !empty($results['hits'])) {
                foreach ($results['hits'] as $item) {
                    $images[] = $item['largeImageURL'];
                }
            }
        }

        if (empty($images)) {
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
            $images[] = 'https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5';
        }

        return $images;
    }
}

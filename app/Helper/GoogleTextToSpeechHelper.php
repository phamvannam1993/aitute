<?php

namespace App\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleTextToSpeechHelper
{
    public static function getAudio($text, $gender, $languageCode = 'en-US', $index = null)
    {
        // https://cloud.google.com/text-to-speech/docs/voices
        $voiceSets = [
            'en-US' => [
                'young_female' => 'en-GB-Wavenet-A',
                'young_male' => 'en-GB-Wavenet-B',
                'old_female' => 'en-US-Neural2-C',
                'old_male' => 'en-US-Neural2-A'
            ],
            'vi-VN' => [
                'young_female' => 'vi-VN-Wavenet-A',
                'young_male' => 'vi-VN-Wavenet-B',
                'old_female' => 'vi-VN-Wavenet-C',
                'old_male' => 'vi-VN-Wavenet-D'
            ]
        ];

        $voiceConfig = [
            'languageCode' => $languageCode,
            'name' => $voiceSets[$languageCode][$gender] ?? 'en-GB-Wavenet-A'
        ];

        $data = [
            'input' => [
                'text' => $text
            ],
            'voice' => $voiceConfig,
            'audioConfig' => [
                'audioEncoding' => 'MP3',
                'pitch' => 0,
                'speakingRate' => 1,
            ]
        ];

        $apiKey = config('services.google_text_to_speech.api_key');
        $url = config('services.google_text_to_speech.url');

        $resp = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Goog-Api-Key' => $apiKey,
        ])->post($url, $data);

        if ($resp->failed()) {
            Log::error($resp->json());
        }

        $audio = base64_decode($resp->json()['audioContent']);

        if ($index) {
            $path = config('services.google_text_to_speech.folder') . "/" . time() .$index. '.mp3';
        } else {
            $path = config('services.google_text_to_speech.folder') . "/" . time() . '.mp3';
        }
        // check disk
        $isLocal = config('filesystems.default') == 'local';
        if ($isLocal) {
            $path = 'public/' . $path;
        }

        Storage::put($path, $audio);

        return $isLocal ? str_replace('public', '/storage', $path) : $path;
    }
}

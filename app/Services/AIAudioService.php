<?php

namespace App\Services;

use App\Models\AIGeneratedMedia;
use App\Models\VoiceType;
use App\Repositories\VoiceTypeRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIAudioService
{
    public function __construct(
        private VoiceTypeRepository $voiceTypeRepository,
    ) {}

    public function text_to_audio($text, $voice)
    {
        $apiKey = config('services.google_text_to_speech.api_key');
        $url = 'https://texttospeech.googleapis.com/v1/text:synthesize';
        $ssml = $this->convert_convesation_to_ssml($text, $voice);
        // post api
        $resp = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Goog-Api-Key' => $apiKey,
        ])->post($url, [
            'input' => [
                'ssml' => $ssml
            ],
            'voice' => [
                'languageCode' => 'vi-VN',
                'name' => $voice
            ],
            'audioConfig' => [
                'audioEncoding' => 'MP3',
                'pitch' => 0,
                'speakingRate' => 1,
            ]
        ]);

        if ($resp->failed()) {
            Log::error($resp->json());
            return response()->json([
                'message' => __('Failed to convert text to speech')
            ], 500);
        }

        $audio = base64_decode($resp->json()['audioContent']);

        $path = config('services.google_text_to_speech.folder') . "/" . "vocabularies/" . Str::random(40) . time() . '.mp3';

        Storage::disk('s3')->put($path, $audio);

        return [
            'path' => $path,
            'ssml' => $ssml
        ];
    }

    public function getListAudioByUserId($userId, $type = 'audio' , $per_page = 10)
    {
        $result = AIGeneratedMedia::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        return $result;
    }

    public function convert_convesation_to_ssml($text, $voice_a = 'vi-VN-Neural2-A')
    {
        // split by \n
        $lines = explode("\n", $text);
        // for each line
        for ($i = 0; $i < count($lines); $i++) {
            // if match with (.{0,}Charlie:) replace with <voice name="en-GB-Wavenet-A">
            if (preg_match('/(.{0,}Charlie:)/', $lines[$i])) {
                $lines[$i] = preg_replace('/Charlie:/', '<voice name="' . $voice_a . '">', $lines[$i]);
                $lines[$i] = $lines[$i] . '</voice>';
            }
        }

        // join array to string
        $ssml = implode("\n", $lines);

        return "<speak>$ssml</speak>";
    }

    public function getUrl($path)
    {
        return Storage::disk('s3')->url($path);
    }

    public function deleteMessageById($textId)
    {
        try {
            $message = AIGeneratedMedia::find($textId);
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

    public function getListVoice($userId) {
        $listVoice = $this->voiceTypeRepository->getListVoice($userId);
        return $listVoice;
    }
    public function getVoiceTypesByUserId($userId) {
        return $this->voiceTypeRepository->getVoiceTypesByUserId($userId);
    }
    public function getListVoiceClones() {
        $listVoice = $this->voiceTypeRepository->getListVoiceClones();
        return $listVoice;
    }

    public function authorCloneVoice($userId){
        $listVoice = $this->voiceTypeRepository->getAuthorCloneVoice($userId);
        return $listVoice;
    }
    public function getVoiceTypeByType($type) {
        return $this->voiceTypeRepository->getVoiceTypeByType($type);
    }
}

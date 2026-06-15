<?php

namespace App\Services;

use Exception;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;

class AudioService
{
    public function convertWebmToMp3($filePath, $audioType, $audioName, $targetType = '.mp3')
    {
        try {
            Log::info('Bắt đầu chuyển đổi định dạng audio từ ' . $audioType . ' sang ' . $targetType);
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => config('ffmpeg.ffmpeg.binaries') ?? exec('which ffmpeg'),
                'ffprobe.binaries' => config('ffmpeg.ffprobe.binaries') ?? exec('which ffprobe'),
                'timeout' => 3600,
                'ffmpeg.threads' => 12,
            ]);
            $audio = $ffmpeg->open($filePath);
            $format = new \FFMpeg\Format\Audio\Mp3();
            if ($targetType == '.wav') {
                $format = new \FFMpeg\Format\Audio\Wav();
            }
            $format->setAudioChannels(1);
            $format->setAudioKiloBitrate(16000);
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio->save($format, storage_path('app/public/audio/' . $audioName . $targetType));
            Log::info('Đã chuyển đổi xong: ' .$audioName. $targetType);
            return storage_path('app/public/audio/' . $audioName . $targetType);
        } catch (Exception $e) {
            Log::error('Đã có lỗi xảy ra trong quá trình chuyển đổi định dạng audio. ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VideoSubtitleService
{
    protected $ffmpeg;
    protected $defaultFont = 'Roboto'; // Font mặc định

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('ffmpeg.ffmpeg.binaries') ?? exec('which ffmpeg'),
            'ffprobe.binaries' => config('ffmpeg.ffprobe.binaries') ?? exec('which ffprobe'),
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);
    }

    public function processVideoWithSubtitle(
        $videoUrl,
        $subtitleUrl,
        $fontSize = 14,
        $fontName = null,
        $marginV = 20
    ) {
        $videoPath = null;
        $subtitlePath = null;
        $outputPath = null;
        try {
            $this->registerFont('Roboto-Regular', storage_path('app/fonts/Roboto-Regular.ttf'));
            // Tạo thư mục temp nếu chưa tồn tại
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            // Sử dụng font được chỉ định hoặc font mặc định
            $fontName = $fontName ?? $this->defaultFont;
            // Tạo tên file ngẫu nhiên
            $videoFileName = 'video_' . Str::random(10) . '.mp4';
            $subtitleFileName = 'subtitle_' . Str::random(10) . '.ass';
            $outputFileName = 'output_' . Str::random(10) . '.mp4';

            // Đường dẫn đầy đủ
            $videoPath = storage_path('app/temp/' . $videoFileName);
            $subtitlePath = storage_path('app/temp/' . $subtitleFileName);
            $outputPath = storage_path('app/temp/' . $outputFileName);

            // Download video và subtitle
            file_put_contents($videoPath, file_get_contents($videoUrl));
            file_put_contents($subtitlePath, file_get_contents($subtitleUrl));

            // Mở video
            $video = $this->ffmpeg->open($videoPath);
            // Thêm subtitle với font chữ, kích thước và margin
            $styleOptions = [
                'MarginV=' . $marginV,
                'FontSize=' . $fontSize,
                'FontName=' . $fontName,
            ];
            // Thêm subtitle vào video với margin bottom
            $video->filters()
                ->custom("subtitles=" . $subtitlePath . ":force_style='" . implode(',', $styleOptions) . "'");

            // Thiết lập format cho video output
            $format = new X264('aac', 'libx264');
            $format->setKiloBitrate(2000);       // Bitrate video
            $format->setAudioKiloBitrate(192);   // Bitrate audio
            $format->setAdditionalParameters([
                '-preset', 'slow',        // Chất lượng nén tốt hơn
                '-profile:v', 'high',     // Profile cao cấp
                '-level', '4.1',          // Tương thích tốt
                '-movflags', '+faststart' // Tối ưu cho web playback
            ]);

            // Lưu video với subtitle
            $video->save($format, $outputPath);

            // Cleanup input files
            @unlink($videoPath);
            @unlink($subtitlePath);

            return $outputPath;
        } catch (\Exception $e) {
            // Cleanup all files on error
            if ($videoPath && file_exists($videoPath)) @unlink($videoPath);
            if ($subtitlePath && file_exists($subtitlePath)) @unlink($subtitlePath);
            if ($outputPath && file_exists($outputPath)) @unlink($outputPath);

            Log::error('Video processing error: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Đăng ký font mới vào hệ thống
     */
    public function registerFont($fontName, $fontFilePath)
    {
        try {
            //check app/fonts/ path not exists then create it
            if (!file_exists(storage_path('app/fonts'))) {
                mkdir(storage_path('app/fonts'), 0777, true);
            }
            $destinationPath = storage_path('app/fonts/' . $fontName . '.ttf');
            copy($fontFilePath, $destinationPath);
            return true;
        } catch (\Exception $e) {
            Log::error('Font registration error: ' . $e->getMessage());
            return false;
        }
    }
}

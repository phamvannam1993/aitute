<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FFmpegHelper
{
    public static function convertToMp3Content($audioFile)
    {
        $name = Str::uuid();
        Storage::disk('local')->putFileAs('audio', $audioFile, $name . '.webm');

        $filePath = storage_path('app/audio/' . $name . '.webm');
        $audioName = pathinfo($filePath, PATHINFO_FILENAME);

        $newFilePath = storage_path('app/audio/' . $audioName . '.mp3');

        $command = 'ffmpeg -i "' . $filePath . '" -vn -ab 128k -ar 44100 -y "' . $newFilePath . '"';

        Log::info($command);

        exec($command);

        $content = file_get_contents($newFilePath);

        // remove unused files
        unlink($filePath);
        unlink($newFilePath);

        return $content;
    }

    public static function convertToMp3Content2($audioFile)
    {
        $extension = $audioFile->extension();
        $name = Str::uuid();
        try {
            Storage::disk('local')->putFileAs('audio', $audioFile, $name . '.' . $extension);
        } catch (\Exception $e) {
            Log::error("Error saving audio file: " . $e->getMessage());
            throw new \RuntimeException("Error saving audio file.");
        }

        $filePath = storage_path('app/audio/' . $name . '.' . $extension);
        if (!file_exists($filePath)) {
            Log::error("Original audio file not found: " . $filePath);
            throw new \RuntimeException("Original audio file not found.");
        }

        $audioName = pathinfo($filePath, PATHINFO_FILENAME);
        $newFilePath = storage_path('app/audio/' . $audioName . '.mp3');
        // Check if the file exists before reading it
//        if (!file_exists($newFilePath)) {
//            Log::error("File not found: " . $newFilePath);
//        }
        $command = 'ffmpeg -i "' . $filePath . '" -vn -ab 128k -ar 44100 -y "' . $newFilePath . '"';

        Log::info($command);

        exec($command);
        $content = file_get_contents($newFilePath);

        // remove unused files
        unlink($filePath);
        unlink($newFilePath);

        return $content;
    }

    public static function  convertContentToMp3($filePath, $newFilePath) {
        try {
            $command = 'ffmpeg -i '.$filePath.' -vn '.$newFilePath;
            exec($command);
        } catch(\Exception $e) {
            return null;
        }
    }

    public static function  convertContentToMp4($filePath, $newFilePath) {
        try {
            $command = 'ffmpeg -i '.$filePath.' '.$newFilePath.' -threads 1 -hide_banner';
            exec($command);
        } catch(\Exception $e) {
            return null;
        }
    }

    public static function mergeVideoAndAudioWithFFmpeg($videoPath, $audioPath, $outputPath, $thumbnailPath = null)
    {
        try {
            $command = 'ffmpeg -i ' . $videoPath . ' -i ' . $audioPath . ' -threads 1 -c:v copy -c:a aac -movflags faststart -strict -2 ' . $outputPath . ' -y';
            Log::info($command);
            exec($command);

            // generate thumbnail
            if (!empty($thumbnailPath)) {
                Log::info("Create thumbnail");
                $command = "ffmpeg -ss 00:00:01 -i $outputPath -vframes 1 -q:v 2 $thumbnailPath";
                exec($command);
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function mergeMultiFile($fileName, $outputPath) {
        try {
            $command = 'ffmpeg -f concat -i '.$fileName.' -c copy '. $outputPath;
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function convertVideoToAudio($inputPath, $outputPath) {
        try {
            $command = 'ffmpeg -i '.$inputPath.' -vn '.$outputPath;
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function convertVideoToVideo($inputPath, $outputPath) {
        try {
            $command = 'ffmpeg -i '.$inputPath.' -an '.$outputPath;
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function addDuration($audioPath, $outputPath, $duration) {
        try {
            $command = 'ffmpeg -i '.$audioPath.' -filter_complex "apad" -t '.$duration.' '.$outputPath;
            return exec($command);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function createThumbnail($videoPath, $thumbnailPath)
    {
        try {
            $command = "ffmpeg -ss 00:00:01 -i $videoPath -vframes 1 -q:v 2 -threads 1 $thumbnailPath";
            Log::info($command);
            exec($command);
            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function mergerVideoAudioV2($videoPath, $audioPath, $outputPath, $thumbnailPath = null, $vol = '1.0') {
        try {
            $command = 'ffmpeg -i ' . $videoPath . ' -i ' . $audioPath . ' -filter_complex "[0:a]volume=1.0[a0];[1:a]volume=1.0[a1];[a0][a1]amix=inputs=2:duration=longest" -c:v copy -c:a aac -threads 1 ' . $outputPath . ' -y';
            if( $vol == "0.2") {
                $command = 'ffmpeg -i ' . $videoPath . ' -i ' . $audioPath . ' -filter_complex "[0:a]volume=1.0[a0];[1:a]volume=0.1[a1];[a0][a1]amix=inputs=2:duration=longest" -c:v copy -c:a aac -threads 1 ' . $outputPath . ' -y';
            }
            exec($command);

            // generate thumbnail
            if (!empty($thumbnailPath)) {
                Log::info("Create thumbnail");
                $command = "ffmpeg -ss 00:00:01 -i $outputPath -vframes 1 -q:v 2 $thumbnailPath";
                exec($command);
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public static function resizeImage($inputPath, $outputPath, $resizeImage = 600)
    {
        try {
            $command = 'ffmpeg -i '.$inputPath.' -vf scale="'.$resizeImage.':-1" '.$outputPath;
            return exec($command);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function cutVideo($videoPath, $outputPath, $start, $duration)
    {
        try {
            $start = self::formatTime($start);
            $duration = self::formatTime($duration);
            $command = 'ffmpeg -i ' . $videoPath . ' -ss ' . $start . ' -t ' . $duration . ' -c copy -movflags faststart ' . $outputPath . ' -y';
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function createAudio($duration, $outputPath) {
        try {
            $command = 'ffmpeg -f lavfi -i anullsrc=channel_layout=stereo:sample_rate=44100 -t '.$duration.' '. $outputPath;  
            exec($command); 
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function cutVideoV2($videoPath, $outputPath, $start, $duration)
    {
        try {
            $start = self::formatTime($start);
            $duration = self::formatTime($duration);
            $command = 'ffmpeg -i ' . $videoPath . ' -t '.$duration.' ' . $outputPath;
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function getVideoDuration($videoPath)
    {
        try {
            $command = 'ffprobe -v error -select_streams v:0 -show_entries stream=duration -of default=noprint_wrappers=1:nokey=1 ' . $videoPath;
            Log::info($command);
            $duration = exec($command);
            return $duration;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function cutAudio($audioPath, $outputPath, $start, $duration)
    {
        try {
            $start = self::formatTime($start);
            $duration = self::formatTime($duration);
            $command = 'ffmpeg -i ' . $audioPath . ' -ss ' . $start . ' -t ' . $duration . ' -c copy ' . $outputPath . ' -y';
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function convertRatio($videoPath, $outputPath, $ratio = "1080:1080")
    {
        try {
            $command = 'ffmpeg -i '.$videoPath.' -vf "scale='.$ratio.'" '.$outputPath;
            if($ratio == "1080:1920") {
                $command = 'ffmpeg -i '.$videoPath.' -vf "scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2" '.$outputPath;
            }
            return exec($command);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function resizeVideo($videoPath, $videoPath2)
    {
        try {
            $command = 'ffmpeg -i '.$videoPath.' -c:v libx264 -pix_fmt yuv420p -crf 28 -preset fast -tune zerolatency -c:a aac -threads 1 '.$videoPath2;
            $duration = exec($command);
            return $duration;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function mergeImageVideo($videoPath,$imagePath, $outputPath) {
        try {
            $command = 'ffmpeg -i '.$videoPath.' -i '.$imagePath.' -filter_complex "overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2:enable='."'between(t,0,1)'".'" -threads 1 '.$outputPath;
            return exec($command);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function cutAudioV2($audioPath, $outputPath, $start, $duration)
    {
        try {
            $start = self::formatTime($start);
            $duration = self::formatTime($duration);
            $command = 'ffmpeg -i ' . $audioPath . ' -t '.$duration.' -threads 1 ' . $outputPath;
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function getAudioDuration($audioPath)
    {
        try {
            $command = 'ffprobe -v error -select_streams a:0 -show_entries stream=duration -of default=noprint_wrappers=1:nokey=1 ' . $audioPath;
            Log::info($command);
            $duration = exec($command);
            return $duration;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function changeVideoResolution($videoPath, $outputPath, $width, $height)
    {
        try {
            $command = 'ffmpeg -i ' . $videoPath . ' -vf scale=' . $width . ':' . $height . ' ' . $outputPath;
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    private static function formatTime($time)
    {
        if (is_numeric($time)) {
            return gmdate('H:i:s', (int) $time);
        }
        return $time; // If it's already a formatted string, return as is
    }

    public static function repeatVideo($videoPath, $duration, $outputPath)
    {
        try {
            $duration = self::formatTime($duration);
            $command = 'ffmpeg -i ' . $videoPath . ' -c copy -f segment -segment_time ' . $duration . ' -reset_timestamps 1 ' . $outputPath . ' -y';
            Log::info($command);
            exec($command);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function mergeFadeVideo($videoPaths = [], $transition = "circlecrop", $duration = 2,  $outputPath)
    {
        $videos = $videoPaths;
        $output = $outputPath;
        $filter = '';
        $offset = 5;
        foreach ($videos as $index => $video) {
            if($index == count($videos) - 1) { 
                continue;
            }
            if($index == count($videos) - 2) {
                $filter .= "[".($index)."v][".($index+1).":v]xfade=transition=".$transition.":duration=".$duration.":offset=".$offset;
            } else if ($index === 0) {
                $filter .= "[0:v][1:v:0]xfade=transition=".$transition.":duration=".$duration.":offset=".$offset."[1v]; ";
            } else {
               $filter .= "[".($index)."v][".($index+1).":v]xfade=transition=".$transition.":duration=".$duration.":offset=".$offset."[".($index+1)."v]; ";
            }
            $offset += 5;
        }
        $command = "ffmpeg " . implode(' ', array_map(fn($v) => "-i $v", $videos)) . " -filter_complex \"$filter\" $output"; 
        $res = exec($command, $outputLog, $returnCode);
        return $returnCode;
    }

    public static function  createVideo($imagePath, $duration, $outputPath, $infoVideo) {
        $command = 'ffmpeg -f lavfi -i color=c=black:s='.$infoVideo.':d='.$duration.' -i '.$imagePath.' -filter_complex "[1:v]scale=1280:-1[img];[0:v][img]overlay=W/H:enable=between'."'(t,0,".$duration.")'".'" -pix_fmt yuv420p '.$outputPath;
        $res = exec($command, $outputLog, $returnCode);
        return $returnCode;
    }

    public static function mergeTwoVideo($video1, $video2, $ouputPath) {
        $command = 'ffmpeg -i "concat:'.$video1.'|'.$video2.'" -codec copy "'.$ouputPath;
        $res = exec($command, $outputLog, $returnCode);
        return $returnCode;
    }

    public static function getWidthHeightVideo($videoPath) {
        $command = 'ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=p=0 '.$videoPath;
        $res = exec($command);
        return $res;
    }
}

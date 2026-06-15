<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Helper\FFmpegHelper;
use App\Helper\Helpers;
use App\Models\Video;
use App\Models\ShortVideo;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VideoService
{
    public function getHistories($user)
    {
        try {
            $userId = $user->id;
            $per_page = 8;
            return Video::where('user_id', '=', $userId)->select('*')
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getHistoryDetail($user, $id)
    {
        try {
            $userId = $user->id;
            $result = Video::where('user_id', $userId)
                ->where('id', $id)
                ->select('*')
                ->firstOrFail();
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getHistoriesWithoutPaginate($user)
    {
        try {
            $userId = $user->id;
            $result = Video::where('user_id', '=', $userId);
            $result =  $result->select('*')
                ->orderBy('created_at', 'desc')
                ->limit(4) // limit result 3 result
                ->get();
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function deleteFileById($id)
    {
        try {
            $video = Video::find(id: $id);
            if (!$video) {
                return [
                    'message' => 'Video không tồn tại.'
                ];
            }
            $video->delete();
            return [
                'message' => 'Xoá file thành công.'
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Lỗi khi xoá file.'
            ];
        }
    }

    public function mergeVideo($ids = [], $videoID) {
        $videoUpdate = Video::where("id", $videoID)->first();
        if(!$videoUpdate) {
            return ["success" => false, 'message' => "Video không tồn tại, vui lòng kiểm tra lại"];
        }
        $dataUpdate = [];
        $videoURLNews = [];
        $ListVideo = [];
        if(count($ids) > 0) {
            $videoURLs = [];
            foreach($ids as $key => $value) {
                $id = $value;
                $video = ShortVideo::where("id", $id)->first();
                if(!$video) {
                    return ["success" => false, 'message' => "Video không tồn tại, vui lòng kiểm tra lại"];
                }
                $s3_url = $video->s3_url;
                $nameVideo = 'video_'.Str::uuid().'.mp4';
                $videoPath = 'videos/'. $nameVideo;
                $videoNewName = 'video_'.Str::uuid().'.mp4';
                $videoPathNew = 'videos/'.$videoNewName;

                $videoPathMerge = 'videos/'.'video_'.Str::uuid().'.mp4';
                $audioNew = 'videos/'.'video_'.Str::uuid().'.mp3';
                $s3URL = Helpers::preSignedS3Url($s3_url);
                file_put_contents(storage_path('app/public/'.$videoPath), file_get_contents($s3URL));
                $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
                $infoVideo = FFmpegHelper::getWidthHeightVideo(storage_path('app/public/'.$videoPath));
                $infoVideo = str_replace(",","x",$infoVideo);
                $textFileVideo =  'videos/'.Str::uuid().'_list.txt';
                $textVideo = "";
                $textVideo = $textVideo."file '".$nameVideo."'\n";
                FFmpegHelper::createThumbnail(storage_path('app/public/'.$videoPath), storage_path('app/public/'.$thumbnailPath), "00:00:05");
                $res = FFmpegHelper::createVideo(storage_path('app/public/'.$thumbnailPath), 2, storage_path('app/public/'.$videoPathNew), $infoVideo);
                FFmpegHelper::createAudio(2, storage_path('app/public/'.$audioNew));
                $videoAudioMergeName = 'video_'.Str::uuid().'.mp4';
                $videoAudioMergePath = 'videos/'. $videoAudioMergeName;
                $textVideo = $textVideo."file '".$videoNewName."'\n";

                FFmpegHelper::mergeVideoAndAudioWithFFmpeg(storage_path('app/public/'.$videoPathNew),  storage_path('app/public/'.$audioNew), storage_path('app/public/'.$videoAudioMergePath),  null);
                file_put_contents( storage_path('app/public/'.$textFileVideo), $textVideo);

                FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileVideo) , storage_path('app/public/'.$videoPathMerge));
                if(count($ids) - 1 == $key || !$videoUpdate->x_fade) {
                    $videoURLNews[] =  storage_path('app/public/'.$videoPath); 
                } else {
                    $videoURLNews[] =  storage_path('app/public/'.$videoPathMerge);
                }
                $ListVideo[] = $thumbnailPath;
                $ListVideo[] = $videoPathNew;
                $ListVideo[] = $audioNew;
                $ListVideo[] = $textFileVideo;
                $ListVideo[] = $videoPathMerge;
                $ListVideo[] = $videoPath;
                $videoURLs[] = Helpers::preSignedS3Url($s3_url);
            }
            $textVideo = "";
            $textAudio = "";
            $textFileVideo =  'videos/'.Str::uuid().'_list.txt';
            $textFileAudio =  'videos/audio_'.Str::uuid().'_list.txt';
            $videoResizeFinal = 'videos/final_video_'.Str::uuid().'.mp4';
            $audioFinal = 'videos/final_audio_'.Str::uuid().'.mp3';
            $totalDuration = 0;
            for($i = 0; $i < count($videoURLs); $i++) {
                $nameVideo = 'video_'.Str::uuid().''.$i.'.mp4';
                $videoPath = 'videos/'. $nameVideo;
                $s3URL = Helpers::preSignedS3Url($videoURLs[$i]);
                file_put_contents(storage_path('app/public/'.$videoPath), file_get_contents($s3URL));

                $duration = FFmpegHelper::getVideoDuration(storage_path('app/public/'.$videoPath));
                $totalDuration += (int)$duration;

                $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath = 'videos/'. $nameVideoConvert;
                FFmpegHelper::convertVideoToVideo(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$videoConvertPath));
                $nameVideoConvert2 = 'video_conver_'.Str::uuid().''.$i.'.mp4';
                $videoConvertPath2 = 'videos/'. $nameVideoConvert2;
                FFmpegHelper::cutVideoV2(storage_path('app/public/'.$videoConvertPath), storage_path('app/public/'.$videoConvertPath2), 0, (int)$duration);
                $textVideo = $textVideo."file '".$nameVideoConvert2."'\n";
              
                $nameAudioConvert = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath = 'videos/'. $nameAudioConvert;
                FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$videoPath) , storage_path('app/public/'.$audioConvertPath));

                $nameAudioConvert2 = 'audio_convert_'.Str::uuid().''.$i.'.mp3';
                $audioConvertPath2 = 'videos/'. $nameAudioConvert2;
                if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
                    FFmpegHelper::createAudio((int)$duration, storage_path('app/public/'.$audioConvertPath2));
                } else {
                    FFmpegHelper::addDuration(storage_path('app/public/'.$audioConvertPath), storage_path('app/public/'.$audioConvertPath2), (int)$duration);
                }
                $textAudio = $textAudio."file '".$nameAudioConvert2."'\n";
                $ListVideo[] = $videoPath;
                $ListVideo[] = $videoConvertPath2;
                $ListVideo[] = $audioConvertPath;
                $ListVideo[] = $videoConvertPath;
                $ListVideo[] = $audioConvertPath2;
            }
            file_put_contents( storage_path('app/public/'.$textFileVideo), $textVideo);
            file_put_contents( storage_path('app/public/'.$textFileAudio), $textAudio);
            $nameVideoConvert = 'video_conver_'.Str::uuid().''.$i.'.mp4';
            $videoConvertPath = 'videos/'. $nameVideoConvert;
            $xfade = $videoUpdate->x_fade ? $videoUpdate->x_fade : "fade";
            $duration = 2;
            if(!$videoUpdate->x_fade) {
                $duration = 0;
            }
            $message = "";
            $res = FFmpegHelper::mergeFadeVideo($videoURLNews, $xfade, $duration, storage_path('app/public/'.$videoResizeFinal));
            if($res != 0) {
                if($duration > 0) {
                    $message = "Tạo hiệu ứng cho video thất bại.";
                }
                FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileVideo) , storage_path('app/public/'.$videoConvertPath));
            }  else {
                FFmpegHelper::convertVideoToVideo(storage_path('app/public/'.$videoResizeFinal) , storage_path('app/public/'.$videoConvertPath));
            }
            $ListVideo[] = $videoResizeFinal;
            $ListVideo[] = $videoConvertPath;
            $ListVideo[] = $audioFinal;
            $ListVideo[] = $textFileVideo;
            $ListVideo[] = $textFileAudio;
            FFmpegHelper::mergeMultiFile(storage_path('app/public/'.$textFileAudio) , storage_path('app/public/'.$audioFinal));
            $ListVideo[] = $videoConvertPath;
            $dataUpdate['duration'] = $totalDuration;
            $outputPath = 'videos/video_'.Str::uuid().'.mp4';
            $thumbnailPath = 'videos/' . Str::uuid() . '.jpg';
            $output_path = 'final_videos/' . Str::uuid() . '.mp4';
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(storage_path('app/public/'.$videoConvertPath),  storage_path('app/public/'.$audioFinal), storage_path('app/public/'.$outputPath),  storage_path('app/public/'.$thumbnailPath));
            $videoContent = file_get_contents(storage_path('app/public/' . $outputPath));
            Storage::disk('s3')->put($output_path, $videoContent);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('s3')->put($thumbnailPath, Storage::disk('public')->get($thumbnailPath));
            }
            $dataUpdate['thumbnail'] = $thumbnailPath;
            $dataUpdate['s3_url'] = $output_path;
            $dataUpdate['s3_url_video_result'] = $output_path;
            $dataUpdate["thumbnail"] =  $thumbnailPath;
            $videoUpdate->update($dataUpdate);
            $ListVideo[] = $videoResizeFinal;
            $ListVideo[] = $thumbnailPath;
            $ListVideo[] = $outputPath;
            $this->cleanupFiles($ListVideo);
            $record = Video::where("id", $videoID)->first();
            $url = "";
            if($record->s3_url_video_result) {
                $record->s3_url = $record->s3_url_video_result;
            }
            if($record->s3_url) {
                $url = Helpers::preSignedS3Url($record->s3_url);
            }
            return ["success" => true, "s3_url" => $url, 'data' => $record, 'message' => $message];
        }
    }

    public function getDetail($user, $id) {
        return Video::where("id", $id)->where("user_id", $user->id)->first();
    }

    public function storeVideo($data = []) {
        return Video::create($data);
    }

    public function update($id, $data = []) {
        return Video::where("id", $id)->update($data);
    }

    private function cleanupFiles(array $paths)
    {
        try {
            foreach ($paths as $path) {
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        } catch (Exception $e) {
            Log::error("cleanupFiles: " . $e->getMessage());
        }
    }

    public function mergeAudioVideo($videoId, $audioUrl) {
        $video = Video::find($videoId);
        $urls = [];
        $video_url = $video->s3_url;
        if($video->transcription_url) {
            $video_url = $video->transcription_url;
        }
        $video_url = Helpers::preSignedS3Url($video_url);

        $video_path = 'videos/' . Str::uuid() . '.mp4';
        $urls[] = $video_path;
        file_put_contents(storage_path('app/public/' .$video_path), file_get_contents($video_url));

        $audioUrlS3 = Helpers::preSignedS3Url($audioUrl);

        file_put_contents(storage_path('app/public/' .$audioUrl), file_get_contents($audioUrlS3));
        $urls[] = $audioUrl;
        $videoPath2 =  'videos/' . Str::uuid() . '.mp4';
        $audioPath2 = 'audio/' . Str::uuid() . '.mp3';
        $videoDuration = FFmpegHelper::getVideoDuration(storage_path('app/public/' . $video_path));
        FFmpegHelper::cutAudioV2(
            storage_path('app/public/' . $audioUrl),
            storage_path('app/public/' . $audioPath2),
            0,
            (int)$videoDuration
        );
        $urls[] = $audioPath2;

        $nameAudioConvert = 'audio_convert_'.Str::uuid().'.mp3';
        $audioConvertPath = 'videos/'. $nameAudioConvert;
        FFmpegHelper::convertVideoToAudio(storage_path('app/public/'.$video_path) , storage_path('app/public/'.$audioConvertPath));
        if(!file_exists(storage_path('app/public/'.$audioConvertPath))) {
            FFmpegHelper::mergeVideoAndAudioWithFFmpeg(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null
            );
        } else {
            FFmpegHelper::mergerVideoAudioV2(
                storage_path('app/public/' . $video_path),
                storage_path('app/public/' . $audioPath2),
                storage_path('app/public/' . $videoPath2),
                null,
                "0.2"
            );
        }
        $url[] = $audioConvertPath;
        $urls[] = $videoPath2;
        Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        $this->cleanupFiles($urls);
        Video::where("id", $videoId)->update(["s3_url_video_audio" => $videoPath2, "s3_url_video_result" => $videoPath2, "is_upload_audio" => true]);
        $record = Video::where("id", $videoId)->first();
        return ["success" => true, 'data' => $record, "s3_url" => Helpers::preSignedS3Url($record->s3_url_video_result)];
    }

    public function updateById($id, $data = []) {
        return  Video::where("id", $id)->update($data);
    }

    public function generateVideoWithTranscriptionWithSegmind($user, $data)
    {
        $s3Url = "";
        $videoId = $data['video_id'];
        $video = Video::find($videoId);
        try {
            if($video->transcription_info) {
                $data = json_decode($video->transcription_info, true);
            }
            $s3Url = $video->s3_url;
            if (!$video) {
                throw new Exception('Video không tồn tại.');
            }
            // Get pre-signed URL for the video
            $video_url = Helpers::preSignedS3Url($s3Url);

            $payload = [
                'MaxChars' => 10,
                'bg_blur' => false,  // Changed from true to false
                'bg_color' => "None",  // Changed from null to "None"
                'color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
                'font' => isset($data['font']) ? $data['font'] : "Arial/Arial_Bold.ttf",  // Changed font
                'fontsize' => 5,  // Changed from 7.0 to 7
                'highlight_color' => isset($data['highlight_color']) ? $data['highlight_color'] : "yellow",
                'kerning' => -2,  // Changed from -2.0 to -2
                'opacity' => 0,  // Changed from 1.0 to 0
                'right_to_left' => false,  // Changed from true to false
                'stroke_color' => "black",
                'stroke_width' => $data['stroke_width'] ?? 2,
                'subs_position' =>  isset($data['subs_position']) ? $data['subs_position'] : "bottom75",
                'input_video' => $video_url
            ];

            // Log::info('Segmind payload:', ['payload' => $payload]);
            // $response = Http::timeout(120)->withHeaders([
            //         'x-api-key' => env("SEGMIND_API_KEY"),
            //         'Content-Type' => 'application/json',
            //     ])
            //     ->post('https://api.segmind.com/v1/video-captioner', $payload);

            // if (!isset($response)) {
            //     throw new Exception('Failed to generate video captions');
            // }
            $response = $this->getTran(json_encode($payload));
           
            // Download and store the captioned video
            $videoContent = $response;
            $video_path = 'videos/'. 'video_'.Str::uuid().'.mp4';
            file_put_contents(storage_path('app/public/'.$video_path), $videoContent);
            if(!file_exists(storage_path('app/public/'.$video_path))) {
                return [
                    'success' => true,
                    'video' => $video->s3_url,
                    'debug' => false
                ];
            }
            Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));

            // Update the AI media record with the new transcribed video
            $video->update([
                'transcription_url' => $video_path,
                's3_url_video_result' => $video_path,
            ]);
            $this->cleanupFiles([$video_path]);
            return [
                'success' => true,
                'video' => Helpers::preSignedS3Url($video_path)
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'video' => $video->s3_url
            ];
        }
    }

    function getTran($data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.segmind.com/v1/video-captioner',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'x-api-key: '.env("SEGMIND_API_KEY"),
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        return  $response;
    }
}

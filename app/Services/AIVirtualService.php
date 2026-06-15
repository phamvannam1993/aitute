<?php

namespace App\Services;

use App\Models\AIGeneratedMedia;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Helper\FFmpegHelper;
use App\Services\ChatGPTService;
use App\Services\TextToSpeechService;
use App\Helper\Helpers;
use App\Models\McVirtual;
use App\Repositories\McVirtualRepository;
use Illuminate\Support\Facades\Auth;
class AIVirtualService
{
    private $mcVirtualRepository;
    private $aiVideoService;
    private $videoSubtitleService;

    public function __construct(
        McVirtualRepository $mcVirtualRepository,
        AIVideoService $aiVideoService,
        VideoSubtitleService $videoSubtitleService
    ) {
        $this->mcVirtualRepository = $mcVirtualRepository;
        $this->aiVideoService = $aiVideoService;
        $this->videoSubtitleService = $videoSubtitleService;
    }

    function store($params)
    {
        $userId = auth('web')->id();

        $data = [
            'user_id' => $userId,
            'voice_id' => $params['voice'],
            'mc_virtual_id' => $params['mc_virtual_id'],
            'result_url' => $params['result_url'] ?? null,
            'content' => $params['content'],
            'status' => $params['status'],
            'ai_generated_media_id' => $params['audio_id'] ?? null,
            'created_by' => $params['created_by'] ?? null,
            'type' => $params['type'] ?? 'talk',
            'duration' => $params['duration'] ?? null,
            'info_json' => $params['info_json'] ?? null,
            'model' => $params['model'] ?? null
        ];
        if (!empty($params['avatar_url']) && $params['avatar_url'] != 'null') {
            $data['avatar_url'] = $params['avatar_url'];
        }

        if (!empty($params['background_url']) && $params['background_url'] != 'null') {
            $data['background_url'] = $params['background_url'];
        }
        $record = $this->mcVirtualRepository->create($data);
        return $record;
    }
    public function getVideoProcessing(){
        $userId = auth('web')->id();
        $result = $this->mcVirtualRepository->getVideoProcessing($userId);
        return $result;
    }

    public function getHistories($per_page = 8)
    {
        try {
            $userId = Auth('web')->id();
            $result = $this->mcVirtualRepository->getHistories($userId, $per_page);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử mc ảo: ' . $e->getMessage());
            return null;
        }
    }
    public function getHistoriesV2()
    {
        try {
            $userId = Auth('web')->id();
            $per_page = 4;
            $result = $this->mcVirtualRepository->getHistoriesV2($userId, $per_page);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử mc ảo: ' . $e->getMessage());
            return null;
        }
    }

    public function getAllNotDone() {
        try {
            $userId = Auth('web')->id();
            $result = $this->mcVirtualRepository->getAllNotDone($userId);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function findOne($id) {
        return $this->mcVirtualRepository->findOne($id);
    }

    public function updateBy($id, $data) {
        return $this->mcVirtualRepository->updateBy($id, $data);
    }

    public function fetchVideo($item)
    {
        $id = $item->mc_virtual_id;
        $apiKey = env('D_ID_API_KEY');
        Log::info('MC Virutal Type: ' . $item->type);
        if ($item->type == 'clips') {
            $ch = curl_init("https://api.d-id.com/clips/" . $id);
        } else {
            $ch = curl_init("https://api.d-id.com/talks/" . $id);
        }

        try {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Authorization: Basic ' . $apiKey,
            ]);

            $statusResponse = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }

            curl_close($ch);

            $statusData = json_decode($statusResponse, true);
            Log::info('Debug: ' . $statusResponse);
            $data = [
                'status' => $statusData['status'],
                'result_url' => null
            ];

            if ($statusData['status'] == 'done') {
                // Download the video file
                $fileContent = file_get_contents($statusData['result_url']);
                if ($fileContent === false) {
                    throw new \Exception('Failed to download video file from ' . $statusData['result_url']);
                }

                // check file webm thì ghép background
                if (!empty($item['background_url']) && strpos($item['background_url'], 'virtual-image/presenter_') === false && $item->type === 'clips') {
                    // tạo file tạm để ghép background
                    $tempFilePath = storage_path('app/public/videos/' . uniqid('clip_', true) . '.mp4');
                    file_put_contents($tempFilePath, $fileContent);

                    $backgroundFilecontent = file_get_contents($item['background_url']);

                    $backgroundFilePath = storage_path('app/public/videos/' . uniqid('background_', true) . '.png');
                    file_put_contents($backgroundFilePath, $backgroundFilecontent);

                    $outputVideoPath = storage_path('app/public/videos/' . uniqid('processed_', true) . '.mp4');

                    // Lệnh FFmpeg để ghép background vào video
                    $command = 'ffmpeg -i ' . escapeshellarg($tempFilePath) . ' -i ' . escapeshellarg($backgroundFilePath) . ' -filter_complex "[0:v]format=rgba,colorkey=0xFFFFFF:similarity=0.1:blend=0.1[ck];[1:v]scale=1920:1080[bg];[bg][ck]overlay=0:0" -c:v libvpx-vp9 -b:v 1M -c:a libopus ' . escapeshellarg($outputVideoPath);
                    exec($command, $output, $returnVar);

                    // Kiểm tra kết quả của lệnh FFmpeg
                    if ($returnVar !== 0) {
                        throw new \Exception('FFmpeg command failed: ' . implode("\n", $output));
                    }

                    // Cập nhật nội dung file với video đã ghép background
                    $fileContent = file_get_contents($outputVideoPath);
                }
                // Save the file to S3
                $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual_', true) . '.mp4';
                Storage::disk('s3')->put($filename, $fileContent);

                // Lấy URL đã được ký trước
                $url = Helpers::preSignedS3Url($filename);
                $data['result_url'] = $filename;
            }

            // Update the database with status and result_url
            $this->mcVirtualRepository->updateByMcVirtualId($id, $data);

            // Update the $item object
            $item->result_url = $data['result_url'] ?? null;
            $item->status = $data['status'];

            return $item;
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error in fetchVideo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchVideoHeyGen($item)
    {
        $user = auth()->user();
        $id = $item->mc_virtual_id;
        Log::info('MC Virutal Type: ' . $item->type);
        $res = null;
        $outputPath = null;

        try {
            $res = $this->aiVideoService->retrieveVideo($id);
            if (!$res) {
                throw new \Exception('Failed to fetch video from Heygen');
            }
            $data = [
                'status' => Helpers::mapStatusFromHeygenToDiD($res->data->status),
                'result_url' => null,
                'avatar_url' => null
            ];
            if ($res->data->status == 'completed') {
                try {
                    if (!empty($res->data->caption_url)) {
                        Log::info('Processing video with caption');
                        // Get processed video file path
                        $outputPath = $this->videoSubtitleService->processVideoWithSubtitle(
                            $res->data->video_url,
                            $res->data->caption_url
                        );
                        $processedVideoContent = file_get_contents($outputPath);
                    } else {
                        $processedVideoContent = file_get_contents($res->data->video_url);
                    }

                    if ($processedVideoContent === false) {
                        throw new \Exception('Failed to get video content');
                    }

                    $avatar = 'virtual-image/'. uniqid('presenter_') . '.jpg';
                    Storage::disk('s3')->put($avatar, file_get_contents($res->data->thumbnail_url));
                    $data['avatar_url'] = $avatar;

                    // Save to S3
                    $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual_', true) . '.mp4';
                    Storage::disk('s3')->put($filename, $processedVideoContent);
                    $data['result_url'] = $filename;
                    Auth::user()->chargeCredit($item->model, $item->model, $res->data->duration ?? 1, null, null, 5, 5);

                } finally {
                    // Cleanup output file if it exists
                    if ($outputPath && file_exists($outputPath)) {
                        @unlink($outputPath);
                    }
                }
            }

            // Update database and return
            $this->mcVirtualRepository->updateByMcVirtualId($id, $data);
            $item->result_url = $data['result_url'] ?? null;
            $item->status = $data['status'];
            $item->credit = $user->credit ?? 0;
            $item->avatar_url = $data['avatar_url'] ?? null;
            return $item;

        } catch (\Exception $e) {
            Log::error('Error in fetchVideoHeyGen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchVideoHeyGenV2($item)
    {
        $id = $item['mc_virtual_id'];
        $data = $item;
        $res = null;
        $outputPath = null;

        try {
            $res = $this->aiVideoService->retrieveVideo($id);
            if (!$res) {
                throw new \Exception('Failed to fetch video from Heygen');
            }
            $data['status'] = Helpers::mapStatusFromHeygenToDiD($res->data->status);
            $data['result_url'] = null;

            if ($res->data->status == 'completed') {
                try {
                    if (!empty($res->data->caption_url)) {
                        // Get processed video file path
                        $outputPath = $this->videoSubtitleService->processVideoWithSubtitle(
                            $res->data->video_url,
                            $res->data->caption_url
                        );
                        $processedVideoContent = file_get_contents($outputPath);
                    } else {
                        $processedVideoContent = file_get_contents($res->data->video_url);
                    }

                    if ($processedVideoContent === false) {
                        throw new \Exception('Failed to get video content');
                    }

                    $avatar = 'virtual-image/'. uniqid('presenter_') . '.jpg';
                    Storage::disk('s3')->put($avatar, file_get_contents($res->data->thumbnail_url));
                    $data['avatar_url'] = $avatar;
                    // Save to S3
                    $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual_', true) . '.mp4';
                    Storage::disk('s3')->put($filename, $processedVideoContent);
                    $data['result_url'] = $filename;
                } finally {
                    // Cleanup output file if it exists
                    if ($outputPath && file_exists($outputPath)) {
                        @unlink($outputPath);
                    }
                }
            }
            // Update database and return
            $data = $this->store($data);
            return $data;

        } catch (\Exception $e) {
            Log::error('Error in fetchVideoHeyGen: ' . $e->getMessage());
            return null;
        }
    }

    public function mergeVideo($data)
    {
        try {
            Log::info('Merge video step 3: bắt đầu tìm bản ghi');
            // Tìm bản ghi trong repository
            $cloneMcVirtual = $this->mcVirtualRepository->find($data['id']);
            Log::info('Merge video step 4: clone bản ghi - ' . json_encode($cloneMcVirtual));
            // Lấy đường dẫn video và audio từ repository
            $videoUrl = $this->mcVirtualRepository->getVideo($data['id'])->result_url;
            $audioUrl = $this->mcVirtualRepository->getAudio($data['ai_generated_media_id'])->s3_url;
            Log::info('Merge video step 5: lấy đường dẫn video và audio | video -> ' . $videoUrl . ' | audio -> ' . $audioUrl);

            Log::info('Merge video step 6: tạo đường dẫn tạm trong storage');
            // Đường dẫn tạm cho file download
            $videoPath = storage_path('app/public/' . uniqid('video_') . 'mp4');
            $audioPath = storage_path('app/public/' . uniqid('audio_') . '.mp3');
            $outputPath = storage_path('app/public/' . uniqid('output_') . '.mp4');

            Log::info('Merge video step 7: tải video và audio về storage');
            // Tải video và audio từ URL về file tạm
            file_put_contents($videoPath, file_get_contents($videoUrl));
            file_put_contents($audioPath, file_get_contents($audioUrl));

            Log::info('Merge video step 8: lấy độ dài video và audio');
            // Lấy độ dài video và audio
            $videoDuration = FFmpegHelper::getVideoDuration($videoPath);
            $audioDuration = FFmpegHelper::getAudioDuration($audioPath);

            Log::info('Merge video step 8.1: video duration - ' . $videoDuration . ' | audio duration - ' . $audioDuration);


            // Xác định độ dài cắt
            $cutDuration = min($videoDuration, $audioDuration);
            Log::info('Merge video step 9: Xác định độ dài cắt: ' . $cutDuration);

            // Tạo đường dẫn cho video và audio cắt
            $cutVideoPath = storage_path('app/public/' . uniqid('cut_video_') . '.mp4');
            $cutAudioPath = storage_path('app/public/' . uniqid('cut_audio_') . '.mp3');

            Log::info('Merge video step 10.1: cắt audio và video');
            // Cắt video và audio
            FFmpegHelper::cutVideo($videoPath, $cutVideoPath, 0, $cutDuration);
            FFmpegHelper::cutAudio($audioPath, $cutAudioPath, 0, $cutDuration);
            Log::info('Merge video step 10.2: cắt audio và video thành công');

            // Câu lệnh FFMpeg để thay thế âm thanh trong video
            $command = "ffmpeg -i $cutVideoPath -i $cutAudioPath -c:v copy -map 0:v:0 -map 1:a:0 -shortest $outputPath";
            Log::info('Merge video step 11: Câu lệnh FFMpeg để thay thế âm thanh trong video: ' . $command);
            Log::info('Merge video step 12: bắt đầu thực thi lệnh');
            // Thực thi lệnh
            exec($command, $output, $returnVar);
            Log::info('Merge video step 12: kết thúc thực thi lệnh');
            // Kiểm tra lỗi
            if ($returnVar !== 0) {
                Log::error("Lỗi khi xử lý video: " . implode("\n", $output));
                return response()->json(['error' => 'Lỗi khi xử lý video'], 500);
            }

            Log::info('Merge video step 13: xoá link file tạm');
            // Xóa các file tạm
            unlink($videoPath);
            unlink($audioPath);
            unlink($cutVideoPath);
            unlink($cutAudioPath);

            // Upload output lên S3
            $video = file_get_contents($outputPath);
            $filename = 'ai_mc_virtual/' . uniqid('ai_mc_virtual', true) . '.mp4';
            Storage::disk('s3')->put($filename, $video);
            Log::info('Merge video step 14: upload file đã merge lên s3 - path: ' . $filename);

            Log::info('Merge video step 15: lưu bản sao record video đã merge vào database');
            $newRecord = new McVirtual(); // Tạo instance mới của model McVirtual
            $newRecord->user_id = $cloneMcVirtual->user_id; // Sao chép user_id từ clone
            $newRecord->avatar_url = $cloneMcVirtual->avatar_url; // Sao chép avatar_url từ clone
            $newRecord->voice_id = $cloneMcVirtual->voice_id; // Sao chép voice_id từ clone
            $newRecord->mc_virtual_id = $cloneMcVirtual->mc_virtual_id; // Sao chép mc_virtual_id từ clone
            $newRecord->result_url = $filename; // Đặt result_url
            $newRecord->content = $cloneMcVirtual->content; // Sao chép content từ clone
            $newRecord->status = $cloneMcVirtual->status; // Sao chép status từ clone
            $newRecord->created_by = $cloneMcVirtual->created_by; // Sao chép created_by từ clone
            $newRecord->save(); // Lưu bản ghi mới

            unlink($outputPath);
            // Trả về đường dẫn của file kết quả trên S3
            return true;
        } catch (Exception $e) {
            Log::error("Exception in mergeVideo: " . $e->getMessage());
            return false; // Trả về false nếu có exception
        }
    }

    public function delete($id)
    {
        return $this->mcVirtualRepository->deleteByFilter(['id' => $id]);
    }
    public function generateVideoWithTranscriptionWithSegmindForMC($data)
    {
        try {
            $id = $data['id'];
            $record = McVirtual::find($id);

            if (!$record) {
                throw new Exception('Video không tồn tại.');
            }

            // Get pre-signed URL for the video
            $video_url = Helpers::preSignedS3Url($record->result_url);

            Log::info('generateVideoWithTranscriptionWithSegmind:', ['video_url' => $video_url]);


            $payload = [
                'MaxChars' => 10,
                'bg_blur' => false,  // Changed from true to false
                'bg_color' => "None",  // Changed from null to "None"
                'color' => "white",
                'font' => "Arial/Arial_Bold.ttf",  // Changed font
                'fontsize' => 7,  // Changed from 7.0 to 7
                'highlight_color' => "yellow",
                'kerning' => -2,  // Changed from -2.0 to -2
                'opacity' => 0,  // Changed from 1.0 to 0
                'right_to_left' => false,  // Changed from true to false
                'stroke_color' => "black",
                'stroke_width' => 2,  // Changed from 2.0 to 2
                'subs_position' => "bottom75",
                'input_video' => $video_url
            ];

            Log::info('Segmind payload:', ['payload' => $payload]);
            $response = Http::timeout(120)
                ->withHeaders([
                    'x-api-key' => env("SEGMIND_API_KEY"),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.segmind.com/v1/video-captioner', $payload);

            if (!isset($response)) {
                throw new Exception('Failed to generate video captions');
            }

            // Download and store the captioned video
            $videoContent = $response;
            $tempFile = tempnam(sys_get_temp_dir(), 'captioned_');
            file_put_contents($tempFile, $videoContent);

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'captioned_video.mp4',
                mime_content_type($tempFile),
                null,
                true
            );

            $video_path = $uploadedFile->store('videos', 'public');
            Storage::disk('s3')->put($video_path, Storage::disk('public')->get($video_path));

            // Update the AI media record with the new transcribed video
            $record->update([
                'transcription_url' => $video_path
            ]);

            return [
                'success' => true,
                'video' => Helpers::preSignedS3Url($video_path)
            ];
        } catch (Exception $e) {
            Log::error("generateVideoWithTranscriptionWithSegmind error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function mergeAudioVideo($aiMcId, $audioUrl) {
        $mcVirtual = McVirtual::find($aiMcId);
        $urls = [];
        $video_url = $mcVirtual->result_url;
        if($mcVirtual->transcription_url) {
            $video_url = $mcVirtual->transcription_url;
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
        FFmpegHelper::mergerVideoAudioV2(
            storage_path('app/public/' . $video_path),
            storage_path('app/public/' . $audioPath2),
            storage_path('app/public/' . $videoPath2),
            null,
            '0.2'
        );
        $urls[] = $videoPath2;
        Storage::disk('s3')->put($videoPath2, Storage::disk('public')->get($videoPath2));
        $this->cleanupFiles($urls);
        McVirtual::where("id", $aiMcId)->update(["s3_url_video_audio" => $videoPath2, "is_upload_audio" => true]);
    }

    public function updateById($id, $data = []) {
        return  McVirtual::where("id", $id)->update($data);
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
}

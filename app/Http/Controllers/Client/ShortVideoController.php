<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\UsageLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\PictoryVideo;
use App\Services\ChatGPTService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use DevDojo\GoogleImageSearch\ImageSearch;
use App\Services\ShortVideoService;
use App\Models\User;
use App\Jobs\MergeVideo;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helpers;
use Exception;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Jobs\MergeAudioVideo;

class ShortVideoController extends Controller
{
    private $shortVideo;

    public function __construct(
        ShortVideoService $shortVideo,
        protected ChatGPTService $chatGPTService
    ) {
        $this->shortVideo = $shortVideo;
    }

    public function generateVideoAudio(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            if($request["model"] == "kling") {
                $res = $this->shortVideo->generateVideoKling($request, Auth::user());
                return $res;
            }
            $validated = $request->validate([
                'audio_description' => 'required|string',
                'model' => 'required|string|max:255',
                'duration' => 'required|string|max:255',
                'resolution' => 'required|max:255',
                'audio_file' => 'file',
            ]);

            $user = auth()->user();
            $audio_file = $request->file('audio_file');
            $image_file = $request->file('image_file');

            $payload = [
                'model' => $validated['model'],
                'video_description' => $request['video_description'] ?  $request['video_description'] : "",
                'audio_description' => $validated['audio_description'],
                'duration' => $validated['duration'],
                'resolution' => $validated['resolution'],
                'scene_number' => $request["scene_number"],
                'voice_type' => $request["voice_type"],
                'short_video_id' => $request["short_video_id"],
                'position_camera' => $request['position_camera'],
                'image_description' => $request['image_description'],
            ];

            if ($audio_file) {
                if (!file_exists(storage_path('app/public/audio'))) {
                    mkdir(storage_path('app/public/audio'), 0777, true);
                }
                $audio_path = $audio_file->store('audio', 'public');
                $validated['audio_path'] = $audio_path;
                $payload['audio_path'] = $audio_path;
            }

            if (isset($request["s3_image_url"]) && !empty($request["s3_image_url"])) {
                $payload['promptImage'] = $request["s3_image_url"];
            } else if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
                $promptImage = Helpers::preSignedS3Url($image_file);
                $payload['promptImage'] = $promptImage;
            }

            // Thay vì truyền $user trực tiếp, chỉ truyền user_id

            switch ($payload['model']) {
                case 'Runway/gen3-alphaturbo':
                    if (!isset($payload['promptImage'])) {
                        return response()->json(["success" => false, 'message' => 'Vui lòng chọn ảnh']);
                    }
                    $res = $this->shortVideo->generateVideoAudioRunway($payload, $user_id);
                    if(!$res["isExist"] && $res["success"]) {
                        Auth::user()->chargeCredit('gen3-alphaturbo', '', $payload['duration'], null, null, 4, 28);
                    }
                    return $res;
            };
            return response()->json(['message' => 'Video is generating...', 'credits' => $user->credit ?? 0]);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error generateVideoAudioe: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getVideoURLRun($shortVideoId, Request $request)
    {
        return $this->shortVideo->getVideoURLRun($shortVideoId);
    }

    public function mergeAudioVideoQueue(Request $request) {
        try {
            $audio_file = $request->file('audio_file');
            $type = $request["type"] ? $request["type"] : 4;
            $audioUrl = $request["audioUrl"] ? $request["audioUrl"] : "";
            $shortVideoId = $request->short_video_id;
            if ($audioUrl != "") {
                $this->shortVideo->updByIds([$shortVideoId], ["audio_url" => $audioUrl]);
                // MergeAudioVideo::dispatch($shortVideoId, $audioUrl, $type, "")->onQueue("merge-audio-video");
                $data = $this->shortVideo->mergeAudioVideoV2($shortVideoId, $audioUrl);
                return response()->json($data);
            } else {
                return response()->json(['message' => "audio không tồn tại", "success" => false]);
            }
        } catch(Exception $ex) {
            return response()->json(['message' => "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại", "success" => false]);
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $record = $this->shortVideo->getDetail($user, $id);
        if($record) {
            $url = "";
            if($record->transcription_url) {
                $record->s3_url = $record->transcription_url;
            }
            if($record->s3_url) {
                $url = Helpers::preSignedS3Url($record->s3_url);
            }
            $dataOld = [
                "descriptions" => [
                    [
                        "sub_title" => $record["description"],
                        "description" => $record["audio_description"],
                    ]
                ],
                "shortVideoId" =>  $record["id"],
                "position_camera" =>  $record["position_camera"],
                "image_description" => $record["image_description"] != "undefined" &&  $record["image_description"] != "null" ? $record["image_description"] : "",
                "isVoice" => $record["voice_over"] ? false : true,
                "s3_url" => $record["s3_url"],
                "audioUrl" => $record["audio_url"],
                "videoRef" => $record["s3_url"],
                "video_id" => $record["video_id"],
                "voice_type" => $record["voice_over"],
                "s3_image_url" => $record["image_url"],
                "imageUrl" => $record["image_url"],
            ];
            return response()->json(["success" => true, "s3_url" => $url, 'id' => $id, 'dataOld' => $dataOld, 'error_msg' =>  $record->error_msg, 'data' => $record, 'is_upload_audio' => $record->is_upload_audio]);
        } else {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"]);
        }
    }

    public function checkAudio(Request $request) {
        try {
            $audio = $request["audio_url"];
            if(empty($audio)) {
                return response()->json(["success" => false, "message" => "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại"]);
            }
            $checkAudio = file_get_contents($audio);
            if(!$checkAudio) {
                return response()->json(["success" => false, "message" => "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại"]);
            }
            return response()->json(["success" => true, "message" => "Audio hợp lệ"]);
        } catch(Exception $ex) {
            return response()->json(["success" => false, "message" => "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại"]);
        }
    }

    public function generateVideoWithTranscriptionWithSegmind(Request $request) {
        $user = Auth::user();
        $response = $this->shortVideo->generateVideoWithTranscriptionWithSegmind($user, $request->all());
        if($response['success']) {
            $screenId = isset($request['screen_id']) ? $request['screen_id'] : 25;
            Auth::user()->chargeCredit('caption', 'caption', $request['duration'], null, null, $screenId, 26);
        }
        return response()->json($response);
    }

    public function checkSizeImage(Request $request) {
        try {
            $imageUrl = $request["image_url"];
            $getID3 = new \getID3;
            $imagePath = storage_path('app/public/image.png');
            $image_file = $request->file('image_file');
            if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                $imagePath = storage_path('app/public/'.$image_file);
            } else {
                file_put_contents($imagePath, file_get_contents($imageUrl));
            }
            $file = $getID3->analyze($imagePath);
            $filesize = $file["filesize"];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            if($filesize > 16000000) {
                return response()->json(["success" => false, "message" => "Ảnh của bạn vượt quá 16MB. Vui lòng chọn ảnh khác."]);
            }
            return response()->json(["success" => true]);
        } catch(Exception $ex) {
            return response()->json(["success" => false, "message" => "Ảnh của bạn vượt quá 16MB. Vui lòng chọn ảnh khác."]);
        }
    }

    public function createShortVideoAIV2(Request $request) {
        $res = $this->shortVideo->createShortVideoAIV2($request);
        return $res;
    }

    public function createShortVideoAIV3(Request $request) {
        $res = $this->shortVideo->createShortVideoAIV3($request);
        return $res;
    }

    public function getVideoAIUrl(Request $request) {
        $res = $this->shortVideo->getVideoAIUrl($request["task_id"], $request["s3_audio_url"]);
        return $res;
    }

    public function createVideoPrediction(Request $request) {
        $isCredit = $request["is_credit"] ? $request["is_credit"] : false;
        $res = $this->shortVideo->createVideoPrediction($request["start_image_url"], $request["end_image_url"], $request["audio_url"], $isCredit);
        return $res;
    }

    public function getAudioDes(Request $request) {
        try {
            $video_des = $request->video_des;
            $numberWords =  ($request->duration*40)/10;
            $protm = 'căn cứ vào nội dung kịch bản : '.$video_des.'.Hãy tạo ra cho tôi NỘI DUNG THUYẾT MINH độ dài không được vượt quá '.$numberWords.' từ phù hợp với nội dung trên. ChỈ trả nội dung thuyết minh là text.';
            $responseData = $this->chatGPTService->getResponse2(
                $protm,
                null,
                'gpt-4o',
                null
            );
            return response()->json(["audio_des" => $responseData, "success" => true]);
        } catch(\Exception $ex) {
            return response()->json(["audio_des" => "", "success" => true]);
        }
    }

    public function mergeVideoAuto(Request $request) {
        $videoUrls[] = $request->video1_url;
        $videoUrls[] = $request->video2_url;
        $res = $this->shortVideo->mergeVideo($videoUrls, $request->audio_url);
        return $res;
    }

    public function mergeVideoAutoV2(Request $request) {
        $videoUrls = $request->videoUrls ? json_decode($request->videoUrls, true) : [];
        $audio_file = $request->file('audio1');
        $audio_url1 = $request->audio_s3;
        $audio_url2 = "";

        if ($audio_file) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_file = $audio_file->store('audio', 'public');
            Storage::disk('s3')->put($audio_file, Storage::disk('public')->get($audio_file));
            $audio_url2 = Helpers::preSignedS3Url($audio_file);
            if (file_exists(storage_path('app/public/audio/'.$audio_file))) {
                unlink(storage_path('app/public/audio/'.$audio_file));
            }
        }
        $enableCaption = $request->enableCaption && $request->enableCaption != 'false' ? true : false;
        $res = $this->shortVideo->mergeVideoV2($videoUrls, $audio_url1, $audio_url2, $enableCaption, $request->totalDuration);
        return $res;
    }
    public function mergeVideoAutoV3(Request $request) {
        $videoUrls = $request->videoUrls ? json_decode($request->videoUrls, true) : [];
        $audio_file = $request->file('audio1');
        $audio_file2 = $request->file('audio2');
        $audio_url1 = "";
        $audio_url2 = "";
        if ($audio_file2) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_file2 = $audio_file2->store('audio', 'public');
            Storage::disk('s3')->put($audio_file2, Storage::disk('public')->get($audio_file2));
            if (file_exists(storage_path('app/public/audio/'.$audio_file2))) {
                unlink(storage_path('app/public/audio/'.$audio_file2));
            }
            $audio_url1 = Helpers::preSignedS3Url($audio_file2);
        } else if( $request->audio_url) {
            $audio_url1 = $request->audio_url;
        }

        if ($audio_file) {
            if (!file_exists(storage_path('app/public/audio'))) {
                mkdir(storage_path('app/public/audio'), 0777, true);
            }
            $audio_file = $audio_file->store('audio', 'public');
            Storage::disk('s3')->put($audio_file, Storage::disk('public')->get($audio_file));
            $audio_url2 = Helpers::preSignedS3Url($audio_file);
            if (file_exists(storage_path('app/public/audio/'.$audio_file))) {
                unlink(storage_path('app/public/audio/'.$audio_file));
            }
        }
        $enableCaption = $request->enableCaption && $request->enableCaption != 'false' ? true : false;
        $res = $this->shortVideo->mergeVideoV2($videoUrls, $audio_url1, $audio_url2, $enableCaption, $request->totalDuration);
        return $res;
    }

    public function uploadImageToS3(Request $request) {
        $image_file = $request->file('image_file');
        if ($image_file) {
            if (!file_exists(storage_path('app/public/images'))) {
                mkdir(storage_path('app/public/images'), 0777, true);
            }
            $image_file = $image_file->store('images', 'public');
            Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
            $promptImage = Helpers::preSignedS3Url($image_file);
            $imagePath = storage_path('app/public/'.$image_file);
            unlink($imagePath);
            return response()->json(["s3_url" => $promptImage, "success" => true]);
        }
        return response()->json(["s3_url" => "", "success" => false]);
    }
}

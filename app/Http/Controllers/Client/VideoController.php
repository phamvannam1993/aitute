<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Services\ShortVideoService;
use App\Jobs\MergeVideo;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helpers;
use Exception;
use App\Rules\MaxFilePageRule;
use App\Services\DocumentReaderService;
use Illuminate\Support\Facades\Storage;
use App\Jobs\MergeAudioVideo;
use App\Models\Video;
use App\Models\ShortVideo;

class VideoController extends Controller
{
    private $videoService;
    private $shortVideoService;

    public function __construct(
        VideoService $videoService,
        ShortVideoService $shortVideoService,
    ) {
        $this->videoService = $videoService;
        $this->shortVideoService = $shortVideoService;
    }

    public function edit($id, Request $request)
    {
        $user = Auth::user();
        $record = $this->videoService->getDetail($user, $id);
        if(!$record) {
           return redirect()->back();
        }
        if($record->transcription_url) {
            $record->s3_url = $record->transcription_url;
        }
        if($record->s3_url_video_audio) {
            $record->s3_url = $record->s3_url_video_audio;
        }
        if($record->s3_url_video_result) {
            $record->s3_url = $record->s3_url_video_result;
        }
        $slideContents = [];
        $shortVideos = $this->shortVideoService->getListByVideoId($id);
        for($i = 0; $i < count($shortVideos); $i++) {
            $slide = [
                "descriptions" => [
                    [
                        "sub_title" => $shortVideos[$i]["description"],
                        "description" => $shortVideos[$i]["audio_description"],
                    ]
                ],
                "shortVideoId" =>  $shortVideos[$i]["id"],
                "position_camera" =>  $shortVideos[$i]["position_camera"],
                "image_description" => $shortVideos[$i]["image_description"] != "undefined" && $shortVideos[$i]["image_description"] != "null" ? $shortVideos[$i]["image_description"] : "",
                "isVoice" => $shortVideos[$i]["voice_over"] ? false : true,
                "s3_url" => $shortVideos[$i]["s3_url"],
                "audioUrl" => $shortVideos[$i]["audio_url"],
                "videoRef" => $shortVideos[$i]["s3_url"],
                "video_id" => $shortVideos[$i]["video_id"],
                "voice_type" => $shortVideos[$i]["voice_over"],
                "s3_image_url" => $shortVideos[$i]["image_url"],
                "imageUrl" => $shortVideos[$i]["image_url"],
            ];
            $slideContents[] = $slide;
        }
        $audioName = "";
        $audioUrl = "";
        if(!empty($record->audio_url)) {
            $audioArr = json_decode($record->audio_url);
            if(isset($audioArr->audio_name)) {
                $audioName = $audioArr->audio_name;
            }
            if(isset($audioArr->audio_url)) {
                $audioUrl = $audioArr->audio_url;
            }
        }
        $xFades = Video::xFades();
        $positionCamera = ShortVideo::positionCamera();
        $xFadeArr = [];
        for($i = 0; $i < count($xFades); $i++) {
            if(isset($xFades[$i]["isDisabled"]) && $xFades[$i]["isDisabled"]) {
               continue;
            } else {
                $xFadeArr[] = $xFades[$i];
            }
        }
        $is_transcription = $record->is_transcription ? true : false;
        return Inertia::render('Client/Video/Edit', ['slideContents' =>  $slideContents, 'positionCameras' => $positionCamera, "is_transcription" => $is_transcription, "slideContentOlds" => $slideContents, "audioName" => $audioName, "audio_url" => $audioUrl, "s3_url" => $record->s3_url, "data" => $record, 'xFades' => $xFadeArr]);
    }

    public function mergeVideoQueue(Request $request) {
        try {
            $user = Auth::user();
            $ids = $request->ids;
            $videoId = $request->video_id;
            $audioDescription = $request["audioDescription"];
            if($videoId > 0) {
                // $this->videoService->update($videoId, ["s3_url" => "", "transcription_url" => "", "s3_url_video_audio" => "", "is_upload_audio" => false, "s3_url_video_image" => "", "s3_url_video_result" => ""]);
                $data = $this->videoService->mergeVideo($ids, $videoId);
                // dispatch(new MergeVideo($ids, $videoId, "", "", $audioDescription, "", 3))->onQueue("merge-video");
                return response()->json($data);
            }
            // $checkExist = $this->shortVideoService->getByIds($ids);
            // if($checkExist) {
            //     return response()->json(["success" => false, "message" => "Video đã được ghép"], 500);
            // }
            $audioFile = $request->file('audio_file');

            $audioPath = "";
            if ($audioFile) {
                if (!file_exists(storage_path('app/public/audio'))) {
                    mkdir(storage_path('app/public/audio'), 0777, true);
                }
                $audioPath = $audioFile->store('audio', 'public');
                Storage::disk('s3')->put($audioPath, file_get_contents(storage_path('app/public/'.$audioPath)));
            }

            $imageFile = $request->file('image_file');

            $imagePath = "";
            if ($imageFile) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $imagePath = $imageFile->store('images', 'public');
                Storage::disk('s3')->put($imagePath, file_get_contents(storage_path('app/public/'.$imagePath)));
            }
            if(count($ids) > 0) {
                $dataAudio = [];
                if(!empty($audioPath)) {
                    $name = $request["audio_name"] ? $request["audio_name"] : "";
                    $dataAudio = [
                        "audio_name" => $name,
                        "audio_url" => $audioPath,
                    ];
                }
                $transcription_info = json_encode(
                    [
                        'subs_position' =>  $request['subs_position'],
                        'highlight_color' =>  $request['highlight_color'],
                        'font' =>  $request['font'],
                    ]
                );
                
                $store = $this->videoService->storeVideo([
                    'user_id' => $user->id,
                    'description' => $request["description"] ? $request["description"] : "",
                    'business_project_id' => $request["businessProjectId"] ? $request["businessProjectId"] : 0,
                    'ratio' =>  $request["ratio"] ? $request["ratio"] : "9:16",
                    'duration' => $request["duration"] ? $request["duration"] : 15,
                    'is_transcription' => $request["is_transcription"] == 'true' ? 1 : 0,
                    'transcription_info' => $request["is_transcription"] == 'true' ? $transcription_info : "",
                    'image_url' => $imagePath,
                    'x_fade' => $request["is_fade"] == 'true' ? $request["x_fade"] : "",
                    "audio_url" => json_encode($dataAudio),
                    's3_url' => "",
                    'thumbnail' => "",
                ]);
                $this->shortVideoService->updByIds($ids, ["video_id" => $store->id]);
                $this->cleanupFiles([$audioPath, $imagePath]);
                $data = $this->videoService->mergeVideo($ids, $store->id);
                return response()->json($data);
            } else {
                return response()->json(["success" => false, "message" => "không tồn tại dữ liệu"], 500);
            }
        } catch (Exception $ex) {
            return response()->json(["success" => false, "message" => "Có lỗi xảy ra trong quá trình ghép video"], 500);
        }
    }

    public function detail($id) {
        $user = Auth::user();
        $record = $this->videoService->getDetail($user, $id);
        if($record) {
            $url = "";
            if($record->transcription_url) {
                $record->s3_url = $record->transcription_url;
            }
            if($record->s3_url_video_audio) {
                $record->s3_url = $record->s3_url_video_audio;
            }
            if($record->s3_url_video_result) {
                $record->s3_url = $record->s3_url_video_result;
            }
            if($record->s3_url) {
                $url = Helpers::preSignedS3Url($record->s3_url);
            }
            return response()->json(["success" => true, "s3_url" => $url, 'id' => $id,  'error_msg' =>  $record->error_msg, 'data' => $record, 'is_upload_audio' => $record->is_upload_audio]);
        } else {
            return response()->json(["success" => false, "message" => "Không tồn tại dữ liệu"]);
        }
    }

    public function generateVideoWithTranscriptionWithSegmind(Request $request) {
        $user = Auth::user();
        $response = $this->videoService->generateVideoWithTranscriptionWithSegmind($user, $request->all());
        if($response['success']) {
            $screenId = isset($request['screen_id']) ? $request['screen_id'] : 25;
            Auth::user()->chargeCredit('caption', 'caption', $request['duration'], null, null, $screenId, 26);
        }
        return response()->json($response);
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

    public function mergeAudioVideoQueue(Request $request) {
        try {
            $audio_file = $request->file('audio_file');
            $audio_s3_url = $request["audio_s3_url"];
            $type = 5;
            $videoId = $request->video_id;
            if($audio_s3_url) {
                $this->videoService->updateById($videoId, ["is_upload_audio" => false]);
                $data = $this->videoService->mergeAudioVideo($videoId, $audio_s3_url);
                // MergeAudioVideo::dispatch($videoId, $audio_s3_url, $type, "")->onQueue("merge-audio-video");
                return  response()->json($data);
            } else if ($audio_file) {
                if (!file_exists(storage_path('app/public/audio'))) {
                    mkdir(storage_path('app/public/audio'), 0777, true);
                }
                $audio_path = $audio_file->store('audio', 'public');
                $this->videoService->updateById($videoId, ["is_upload_audio" => false]);
                Storage::disk('s3')->put($audio_path, file_get_contents(storage_path('app/public/'.$audio_path)));
               
                $this->cleanupFiles([
                    $audio_path,
                ]);
                $data = $this->videoService->mergeAudioVideo($videoId, $audio_path);
                return  response()->json($data);
            } else {
                return response()->json(['message' => "audio không tồn tại", "success" => false]);
            }
        } catch(Exception $ex) {
            return response()->json(['message' => "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại", "success" => false]);
        }
    }

    public function history(Request $request)
    {
        try {
            $user = Auth::user();
            $list = $this->videoService->getHistories($user);
            return Inertia::render('Client/Video/History', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ApiGetHistory(Request $request) {
        try {
            $user = Auth::user();
            $list = $this->videoService->getHistories($user);
            foreach($list as $record) {
                if($record->transcription_url) {
                    $record->s3_url = $record->transcription_url;
                }
                if($record->s3_url_video_audio) {
                    $record->s3_url = $record->s3_url_video_audio;
                }
                if($record->s3_url_video_result) {
                    $record->s3_url = $record->s3_url_video_result;
                }
            }
            return response()->json(["list" => $list, "success" => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function historyDetail(Request $request, $id)
    {
        $user = Auth::user();
        $record = $this->videoService->getHistoryDetail($user, $id);
        if($record) {
            $historyList = $this->videoService->getHistoriesWithoutPaginate($user);
            // Convert Collection toArray
            if ($historyList instanceof \Illuminate\Database\Eloquent\Collection) {
                $historyList = $historyList->toArray();
            }

            // Filter element duplicate with $record
            $filteredHistoryList = array_filter($historyList, function ($item) use ($record) {
                return $item['id'] !== $record['id']; // Giả sử $record có trường 'id'
            });

            // Without duplicate id => get 3 (0,3)
            if (empty($filteredHistoryList)) {
                $filteredHistoryList = array_slice($historyList, 0, 3);
            } else {
                $filteredHistoryList = array_slice($filteredHistoryList, 0, 3);
            }
            if($record->transcription_url) {
                $record->s3_url = $record->transcription_url;
            }
            if($record->s3_url_video_audio) {
                $record->s3_url = $record->s3_url_video_audio;
            }
            if($record->s3_url_video_result) {
                $record->s3_url = $record->s3_url_video_result;
            }
            $params = ['record' => $record, 'list' => $filteredHistoryList];

            return Inertia::render('Client/Video/HistoryDetail', $params);
        } else {
            return redirect()->back();
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->videoService->deleteFileById($id);
        return response()->json($response);
    }

    public function uploadImage(Request $request) {
        try {
            $imageFile = $request->file('image_file');

            $imagePath = "";
            if ($imageFile) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $imagePath = $imageFile->store('images', 'public');
                Storage::disk('s3')->put($imagePath, file_get_contents(storage_path('app/public/'.$imagePath)));
                $image_url = Helpers::preSignedS3Url($imagePath);
                return response()->json(['image_url' => $image_url], 200);
            } else {
                return response()->json(['message' => "Ảnh của bạn không hợp lệ"], 500);
            }
        } catch(\Exception $ex) {
            return response()->json(['message' => "Ảnh của bạn không hợp lệ"], 500);
        }
    }

    public function readFile(Request $request)
    {
        try {
            $request->validate([
                'imageUrl' => 'nullable|url',
            ]);
            $content = "";
            if ($request->has('imageUrl')) {
                $imageUrl = $request->imageUrl;
                $imageFile = file_get_contents($imageUrl);
                $tempFile = storage_path('app/public/image' . uniqid('img_') . 'png');
                file_put_contents($tempFile, $imageFile);
                $sourceImage = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    'image_template.png',
                    mime_content_type($tempFile),
                    null,
                    true
                );
                $documentReader = app(DocumentReaderService::class);
                $content = $documentReader->readContent($sourceImage);

            } else {
                $request->validate([
                    'content' => 'required_without:file|nullable|string',
                    'file' => [
                        'required_without:content',
                        'file',
                        'mimes:doc,docx,pdf,png,jpg,dotx,jpeg,webp'
                    ],
                ]);

                $content = $request->input('content');
                if ($request->hasFile('file')) {
                    $documentReader = app(DocumentReaderService::class);
                    $content = $documentReader->readContent($request->file('file'));
                }
            }
            return response()->json([
                'content' => $content,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Ảnh của bạn không hợp lệ. Xin vui lòng tải ảnh hợp lệ',
            ], 400);
        }
    }

    public function getXface() {
        $xFades = Video::xFades();
        $positionCamera = ShortVideo::positionCamera();
        $xFadeArr = [];
        for($i = 0; $i < count($xFades); $i++) {
            if(isset($xFades[$i]["isDisabled"]) && $xFades[$i]["isDisabled"]) {
               continue;
            } else {
                $xFadeArr[] = $xFades[$i];
            }
        }
        return response()->json(['xFades' => $xFadeArr, 'positionCameras' => $positionCamera]);
    }

    public function uploadVideoToS3(Request $request) {
        $video_file = $request->file('video_file');
        if ($video_file) {
            if (!file_exists(storage_path('app/public/videos'))) {
                mkdir(storage_path('app/public/videos'), 0777, true);
            }
            $video_file = $video_file->store('videos', 'public');
            Storage::disk('s3')->put($video_file, Storage::disk('public')->get($video_file));
            $videoUrl = Helpers::preSignedS3Url($video_file);
            $videoPath = storage_path('app/public/'.$video_file);
            unlink($videoPath);
            return response()->json(["s3_url" => $videoUrl, "success" => true]);
        }
        return response()->json(["s3_url" => "", "success" => false]);
    }
}

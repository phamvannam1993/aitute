<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\AIChatMessage;
use App\Models\AIChatSession;
use App\Models\AIGeneratedMedia;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\SemesterMaterialRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class AIMediaService
{
    public function __construct() {}

    public function getHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('type', 'image')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height')
                ->where(function($query) {
                    $query->where('model', '!=', 'photo-beauty')
                          ->orWhereNull('model');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }
    public function getHistoriesBeauty()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('type', 'image')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height')
                ->where('model', 'photo-beauty')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }


    public function storeMedia($data)
    {
        try {
            $message = AIGeneratedMedia::create($data);
            if (!$message) {
                Log::error('Lưu trữ file thất bại.');
                return null;
            }
            return $message;
        } catch (Exception $e) {
            Log::error('Lỗi khi lưu trữ file: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteFileById($id)
    {
        $message = AIGeneratedMedia::find(id: $id);
        if (!$message) {
            return response()->json(['message' => 'File không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá file thành công.'], 200);
    }

    public function getListMedia($type = 'image', $per_page=2)
    {
        try {
            $userId = auth('web')->id();
            $result = AIGeneratedMedia::where('user_id', '=', $userId)
                ->where('type', $type)
                ->select('id', 's3_url', 'type')
                ->orderBy('id', 'desc')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }

    public function saveSwapFace(string $s3Path, string $sourceImg, string $targetImg, $mode = 'swap face'): AIGeneratedMedia
    {
        $description = "target_image: {$targetImg}, source_image: {$sourceImg}";

        $mediaData = array_filter([
            'user_id' => auth('web')->id(),
            'type' => 'image',
            'model' => $mode,
            's3_url' => $s3Path,
            'description' => $description
        ]);

        try {
            $media = AIGeneratedMedia::create($mediaData);

            Log::info('AI Generated Media saved successfully', [
                'media_id' => $media->id,
                'user_id' => auth('web')->id()
            ]);

            return $media;
        } catch (\Exception $e) {
            Log::error('Failed to save AI Generated Media', [
                'error' => $e->getMessage(),
                'data' => $mediaData,
                'user_id' => auth('web')->id()
            ]);
            throw $e;
        }
    }

    public function getFaceSwapHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('model', 'swap face')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }

    public function getOneById($id) {
        $media = AIGeneratedMedia::find(id: $id);
        return $media;
    }

    public function getMyAIImageHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = AIGeneratedMedia::where('user_id', '=', $userId)->where('model', 'my_ai_image')
                ->select('id', 'description', 'style', 'artist', 's3_url', 'width', 'height')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }
    public function getListMediaByModel($model = null, $type = 'image', $per_page = 20)
    {
        try {
            $userId = auth('web')->id();

            $query = AIGeneratedMedia::where('user_id', '=', $userId)
                ->where('type', $type)
                ->whereNotNull('model');

            if ($model !== null) {
                $query->where('model', $model);
            }

            $result = $query->select('id', 's3_url', 'type')
                ->orderBy('id', 'desc')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Error while fetching media list: ' . $e->getMessage());
            return null;
        }
    }
}

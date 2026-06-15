<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\AIChatMessage;
use App\Models\AIChatSession;
use App\Models\AiGeneratedBannerPoster;
use App\Models\AIGeneratedMedia;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\SemesterMaterialRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class AIBannerPostService
{
    public function __construct() {}
    
    public function storeMedia($data)
    {
        try {
            $message = AiGeneratedBannerPoster::create($data);
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

    public function getHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = AiGeneratedBannerPoster::where('user_id', '=', $userId)
                ->select('id', 'description', 'type', 's3_url', 'width', 'height')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);

            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử hình ảnh: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteFileById($id)
    {
        $message = AiGeneratedBannerPoster::find(id: $id);
        if (!$message) {
            return response()->json(['message' => 'File không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá file thành công.'], 200);
    }
}

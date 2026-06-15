<?php

namespace App\Services;

use App\Models\PebblelyVideo;
use App\Repositories\PebblelyVideoRepository;
use App\Services\FacebookApiService;
use Exception;
use Illuminate\Support\Facades\Log;

class PebblelyVideoService
{
    public function __construct(
        private AuthService $authService,
        protected PebblelyVideoRepository $pebblelyVideoRepository
    ) {}

    public function store($params)
    {
        $userId = auth('web')->id();

        $data = [
            'user_id' => $userId,
            'theme' => $params['theme'],
            'input_url' => $params['input_url'],
            's3_url' => $params['s3_url'],
        ];

        $record = $this->pebblelyVideoRepository->create($data);
        return $record;
    }
    public function getHistories() {
        try {
            $userId = auth('web')->id();
            $per_page = 8;
            $result = PebblelyVideo::where('user_id', '=', $userId)
                ->select('id', 's3_url')
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteFileById($id)
    {
        $message = PebblelyVideo::find(id: $id);
        if (!$message) {
            return response()->json(['message' => 'File không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá file thành công.'], 200);
    }
}

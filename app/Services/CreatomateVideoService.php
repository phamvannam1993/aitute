<?php

namespace App\Services;

use App\Models\CreatomateVideo;
use App\Repositories\CreatomateTemplateRepository;
use App\Repositories\CreatomateVideoRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class CreatomateVideoService
{
    protected $creatomateVideoRepository;
    protected $creatomateTemplateRepository;
    public function __construct(CreatomateVideoRepository $creatomateVideoRepository, CreatomateTemplateRepository $creatomateTemplateRepository)
    {
        $this->creatomateVideoRepository = $creatomateVideoRepository;
        $this->creatomateTemplateRepository = $creatomateTemplateRepository;
    }


    public function store($creatomate_video_id, $templateId, $result, $snapshotUrl, $content, $title)
    {
        try {
            $userId = auth('web')->id();
            $creatomateTemplate = $this->creatomateTemplateRepository->findByFilter([
                ['template_id', $templateId]
            ]);
            $data = [
                'user_id' => $userId,
                'creatomate_template_id' => $creatomateTemplate->id,
                'title' => $title,
                'snapshot_url' => $snapshotUrl,
                'content' => json_encode($content),
                'result' => $result
            ];
            if (empty($creatomate_video_id)) {
                $result = $this->creatomateVideoRepository->create($data);
            } else {
                $result = $this->creatomateVideoRepository->updateOrCreate(['id' => $creatomate_video_id], $data);
            }
            if (!$result) {
                Log::error('Lưu trữ thất bại.');
                return null;
            }
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lưu trữ: ' . $e->getMessage());
            return null;
        }
    }

    public function getHistories() {
        try {
            $userId = Auth('web')->id();
            $per_page = 8;
            $result = CreatomateVideo::where('user_id', '=', $userId)
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
        try {
            $message = CreatomateVideo::find(id: $id);
            if (!$message) {
                return [
                    'message' => 'File không tồn tại.'
                ];
            }
            $message->delete();
            return [
                'message' => 'Xoá file thành công.'
            ];
        } catch (Exception $e) {
            Log::error('Lỗi khi xoá file: ' . $e->getMessage());
            return [
                'message' => 'Lỗi khi xoá file.'
            ];
        }
    }

    public function getVideoById($id) {
        $record = $this->creatomateVideoRepository->findOrFail($id);
        return $record;
    }
}

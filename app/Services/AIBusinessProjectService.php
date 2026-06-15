<?php

namespace App\Services;

use App\Helper\Helpers;
use App\Models\AIBusinessProject;
use App\Models\PebblelyVideo;
use App\Repositories\AIBusinessProjectRepository;
use App\Repositories\PebblelyVideoRepository;
use App\Services\FacebookApiService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIBusinessProjectService
{
    public function __construct(
        private AuthService $authService,
        protected AIBusinessProjectRepository $aIBusinessProjectRepository
    ) {}

    public function store($params)
    {
        $userId = auth('web')->id();
        $data = [
            'user_id' => $userId,
            'title' => $params['title'] ?? "",
            'description' => $params['description'] ?? "",
            'url' => $params['url'] ?? "",
            'files'=> isset($params['files']) ? (array)json_decode($params['files']) : [],
            'project_type'=> $params['project_type'] ?? '',
            'total_campaign_days'=> $params['total_campaign_days'] ?? 0
        ];

        //upload image product to s3
        if (isset($params['image_product'])) {
            $data['image_product'] = Helpers::extractRelativePathFromSignedS3Url($params['image_product']);
        }
        if (isset($params['image_model'])) {
            $data['image_model'] = Helpers::extractRelativePathFromSignedS3Url($params['image_model']);
        }
        $record = $this->aIBusinessProjectRepository->create($data);
        return $record;
    }
    public function getListProjects()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 8;
            $result = AIBusinessProject::where('user_id', '=', $userId)
                ->select('id', 'user_id', 'title', 'description', 'url', 'image_product', 'meta_data', 'image_model', 'files', 'data_json', 'created_at', 'project_type', 'total_campaign_days')
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
        $message = AIBusinessProject::find(id: $id);
        if (!$message) {
            return response()->json(['message' => 'File không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá file thành công.'], 200);
    }
    public function update($id, $params)
    {
        try {
            $project = AIBusinessProject::find($id);
            if (!$project) {
                throw new Exception('Dự án không tồn tại.');
            }
            $data = [];
            if (isset($params['title'])) {
                $data['title'] = $params['title'];
            }
            if (isset($params['description'])) {
                $data['description'] = $params['description'];
            }
            if (isset($params['url'])) {
                $data['url'] = $params['url'];
            }
            if (isset($params['total_campaign_days'])) {
                $data['total_campaign_days'] = $params['total_campaign_days'];
            }
            if (isset($params['project_type'])) {
                $data['project_type'] = $params['project_type'];
            }
            if (isset($params['image_product'])) {
                $data['image_product'] = Helpers::extractRelativePathFromSignedS3Url($params['image_product']);
            }
            if (isset($params['image_model'])) {
                $data['image_model'] = Helpers::extractRelativePathFromSignedS3Url($params['image_model']);
            }
            if (isset($params['files'])) {
                $data['files'] = (array)json_decode($params['files']);
            }
            if (isset($params['data_json'])) {
                $data['data_json'] = (array)json_decode($params['data_json']);
            }
            if (isset($params['image_urls'])) {
                $imageUrls = is_array($params['image_urls']) ? $params['image_urls'] : json_decode($params['image_urls'], true);
                $data['image_urls'] = $imageUrls;
            }   
            if (isset($params['content'])) {
                $data['content'] = $params['content'];
            }    
            if (isset($params['video_url'])) {
                $data['video_url'] = $params['video_url'];
            }    
 
            $data['count'] = $project->count + 1;
            $project->update($data);
            $project = AIBusinessProject::find($id);

            return $project;
        } catch (Exception $e) {
            Log::error('Lỗi khi cập nhật dự án: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật dự án.'], 500);
        }
    }

    public function checkTrialOverCount()
    {
        $userId = auth('web')->id();

        $count = $this->aIBusinessProjectRepository->getCount($userId);

        return $count >= config('common.trial_max_count') ? 1 : 0;
    }

    public function getListProjectHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 8;
            $result = AIBusinessProject::where('user_id', '=', $userId)
                ->select('id', 'title', 'content', 'image_urls', 'video_url', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy lịch sử video: ' . $e->getMessage());
            return null;
        }
    }
}

<?php

namespace App\Services;

use App\Models\AIProjectExpert;
use App\Repositories\AIProjectExpertRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIProjectExpertService
{
    public function __construct(
        private AuthService $authService,
        protected AIProjectExpertRepository $AIProjectExpertRepository
    ) {}

    public function store($params)
    {
        $userId = auth('web')->id();
        $data = [
            'user_id' => $userId,
            'title' => $params['title'] ?? "",
            'description' => $params['description'] ?? "",
            'answers' => $params['answers'] ?? "",
            'url' => $params['url'] ?? "",
            'files'=> isset($params['files']) ? (array)json_decode($params['files']) : [],
        ];

        //upload image product to s3
        if (isset($params['image_product'])) {
            $imageProduct = $params['image_product'];
            $sampleFilePath = 'business-project/' . uniqid() . '.' . $imageProduct->getClientOriginalExtension();
            Storage::disk('s3')->put($sampleFilePath, file_get_contents($imageProduct));
            $data['image_product'] = $sampleFilePath;
        }

        if (isset($params['model_product'])) {
            $modelProduct = $params['model_product'];
            $sampleFilePath = 'business-project/' . uniqid() . '.' . $modelProduct->getClientOriginalExtension();
            Storage::disk('s3')->put($sampleFilePath, file_get_contents($modelProduct));
            $data['model_product'] = $sampleFilePath;
        }

        $record = $this->AIProjectExpertRepository->create($data);
        return $record;
    }

    public function getListProjects()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 8;
            $result = AIProjectExpert::where('user_id', '=', $userId)
                ->select('id', 'title', 'created_at')
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
        $message = AIProjectExpert::find(id: $id);
        if (!$message) {
            return response()->json(['message' => 'File không tồn tại.'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Xoá file thành công.'], 200);
    }
    public function update($id, $params)
    {
        try {
            $dataUpdate = [];
            $project = AIProjectExpert::find($id);
            if (!$project) {
                throw new Exception('Dự án không tồn tại.');
            }
            if(isset($params["title"])) {
                $dataUpdate["title"] = $params["title"];
            }
            if(isset($params["expert_info"])) {
                $answers = json_decode($project->answers, true);
                $answers["conversation_id"] = $params["conversation_id"];
                $dataUpdate["answers"] = json_encode($answers);
                $dataUpdate["expert_info"] = $params["expert_info"];
            }
            if(isset($params["buss_info"])) {
                $dataUpdate["buss_info"] = $params["buss_info"];
            }
            if(isset($params["description"])) {
                $dataUpdate["description"] = $params["description"];
            }
            if(isset($params['files'])) {
                $dataUpdate["files"] = (array)json_decode($params['files']);
            }
            if(isset($params["analysis_sections"])) {
                $dataUpdate["analysis_sections"] = $params["analysis_sections"];
            }
            if(isset($params["communication_strategy"])) {
                $dataUpdate["communication_strategy"] = $params["communication_strategy"];
            }
            $results = $project->results ? json_decode($project->results, true) : [];
            if(isset($params["res_content"])) {
                $results["content"] = $params["res_content"];
                $dataUpdate["results"] = json_encode($results);
            }
            if(isset($params["res_images"])) {
                $results["images"] = $params["res_images"];
                $dataUpdate["results"] = json_encode($results);
            }
            if(isset($params["res_video_url"])) {
                $results["video_url"] = $params["res_video_url"];
                $dataUpdate["results"] = json_encode($results);
            }
            if(isset($params["remove_video"])) {
                $results["video_url"] = "";
                $dataUpdate["results"] = json_encode($results);
            }
            if(isset($params["is_video"]) && $params["is_video"] == 1) {
                $images = json_decode($results["images"], true);
                for($i = 0; $i < count($images); $i++) {
                    $images[$i]["is_post"] = false;
                }
                $dataUpdate["images"] = json_encode($images);
                $dataUpdate["is_video"] = $params["is_video"];
                $dataUpdate["results"] = json_encode($results);
            }
            if(isset($params["is_video"]) && $params["is_video"] == 2) {
                $dataUpdate["is_video"] = false;
            }
            if(isset($params["url"])) {
                $dataUpdate["url"] = $params["url"];
            }
            if (isset($params['image_product'])) {
                $imageProduct = $params['image_product'];
                $sampleFilePath = 'business-project/' . uniqid() . '.' . $imageProduct->getClientOriginalExtension();
                Storage::disk('s3')->put($sampleFilePath, file_get_contents($imageProduct));
                $dataUpdate['image_product'] = $sampleFilePath;
            }
            if (isset($params['model_product'])) {
                $imageProduct = $params['model_product'];
                $sampleFilePath = 'business-project/' . uniqid() . '.' . $imageProduct->getClientOriginalExtension();
                Storage::disk('s3')->put($sampleFilePath, file_get_contents($imageProduct));
                $dataUpdate['model_product'] = $sampleFilePath;
            }
            if (isset($params['files'])) {
                $dataUpdate['files'] = (array)json_decode($params['files']);
            }
            $project->update($dataUpdate);
            $project = AIProjectExpert::find($id);

            return $project;
        } catch (Exception $e) {
            Log::error('Lỗi khi cập nhật dự án: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật dự án.'], 500);
        }
    }
}

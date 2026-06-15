<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\CreatomateTemplateService;
use App\Services\CreatomateVideoService;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Creatomate\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class CreatomateController extends Controller
{
    public function __construct(
        protected CreatomateTemplateService $creatomateTemplateService,
        protected ChatGPTService $chatGPTService,
        protected StorageService $storageService,
        protected CreatomateVideoService $creatomateVideoService
    ) {}

    public function index(Request $request): Response
    {
        $list = $this->creatomateTemplateService->getListTemplate();
        foreach ($list as $template) {
            $template->demo_url = Helpers::preSignedS3Url($template->demo_url); 
        }
        return Inertia::render('Client/Creatomate/Index', ['list' => $list]);
    }

    public function getTemplates(Request $request) {
        try {
            // Lấy tham số từ request
            $search = $request->input('search', '');
            $resolution = $request->input('resolution', '');
    
            // Gọi dịch vụ tìm kiếm
            $list = $this->creatomateTemplateService->search($search, $resolution);
    
            // Kiểm tra nếu $list là JsonResponse, lấy dữ liệu từ nó
            if ($list instanceof \Illuminate\Http\JsonResponse) {
                $list = $list->getData(); // Lấy dữ liệu từ JsonResponse
            }
    
            foreach ($list->data as $template) {
                try {
                    if (isset($template->demo_url)) {
                        $template->demo_url = Helpers::preSignedS3Url($template->demo_url); 
                    }
                } catch (Exception $e) {
                    Log::error('Lỗi khi tạo URL: ' . $e->getMessage(), ['template' => $template]);
                    $template->demo_url = null; // Gán giá trị mặc định nếu lỗi
                }
            }
            return response()->json($list, 200);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy danh sách template: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi server'], 500);
        }
    }
    

    public function create(Request $request)
    {
        $id = $request->id;
        $record = $this->creatomateTemplateService->getTemplateById($id);
        return Inertia::render('Client/Creatomate/Create', ['template' => $record]);
    }
    public function history(Request $request): Response
    {
        $list = $this->creatomateVideoService->getHistories();
        return Inertia::render('Client/Creatomate/History', ['list' => $list]);
    }

    public function creatomateCreateContent(Request $request)
    {
        $user = auth()->user();
        $params = $request->all();
        if (empty($params['description']) || empty($params['structure'])) {
            return response()->json(['error' => 'Chưa cung cấp đủ thông tin'], 400);
        }
        $prompt = 'Hãy tạo nội dung theo chủ đề sau đây: ' . $params['description'] . ' Câu trả lời phải đúng theo format sau đây: ' . $params['structure'] . '. Chỉ cần trả lời cho các field text với nội dung trả lời tuyệt đối không được vượt quá số lượng ký tự của thuộc tính "length" của text đó bao gồm cả các ký tự đặc biệt, các field còn lại thì cứ giữ nguyên như format không cần trả lời. Câu trả lời không được thêm bất kỳ nội dung nào khác. Bắt buộc phải đúng như format cung cấp.';
        $model = AIModel::GPT_4_MINI;
        try {
            $generatedText = $this->chatGPTService->gptSendRequest($prompt, null, $model);
            $dataGeneratedText = json_decode($generatedText);
            Log::info('Debugggggg: '.$generatedText);
            foreach ($dataGeneratedText->composition as $key => $item) {
                if (isset($item->type) && $item->type === "text") {
                    $value = $item->value;
                    $length = $item->length ?? 0;
                    if (strlen($value) > (int)$length) { 
                        $item->value = $this->chatGPTService->gptSendRequest('Hãy tóm tắt văn bản sau đây: '.$value.' ngắn hơn với độ dài không được vượt quá '.$length.' ký tự bao gồm cả ký tự đặc biệt và khoảng cách.', null, $model);
                    }
                }
            }

            $generatedText = json_encode($dataGeneratedText);

            // $response = app(ChatClaudeService::class)->sendRequestAI($prompt);
            // $decodedResponse = json_decode($response);
            // $generatedText = $decodedResponse->content[0]->text;
            // Trừ tiền credit user
            Auth::user()->chargeCredit('text', 'GPT-4o mini', null, 'ai-text', $prompt, 18, 18);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi gọi AI: ' . $e->getMessage()], 500);
        }
        $jsonString = str_replace("\n", '', $generatedText);
        return response()->json(['result' => $jsonString, 'credits' => $user->credit ?? 0]);
    }

    public function createVideo(Request $request)
    {
        $user = auth()->user();
        $templateId = $request->input('template_id');
        $creatomate_video_id = $request->input('creatomate_video_id');
        $data = $request->except(['template_id', 'creatomate_video_id','duration']);
        $files = $request->file();
        foreach ($data as $key => $item) {
            if (str_contains($key, '_fill_color')) {
                $value = $item;
                $k = preg_replace('/_/', '.', $key, 1);

                unset($data[$key]);
                $data[$k] = $value;
            }
        }

        foreach ($files as $key => $file) {
            if ($file->isValid()) {
                $result = $this->storageService->putToS3($file, folderName: 'creatomate');
                $data[$key] = $result['url'];
            }
        }

        $result = $this->sendToCreatomate($templateId, $data);
        if (isset($result[0]) && $result[0]['status'] === 'succeeded') {
            $videoUrl = $result[0]['url'];
            $snapshotUrl = $result[0]['snapshot_url'];
            $title = isset($result[0]['modifications']['Itemtitle']) ? $result[0]['modifications']['Itemtitle'] : 'Tiêu đề mặc định';
            $record = $this->creatomateVideoService->store($creatomate_video_id, $templateId, $videoUrl, $snapshotUrl, $data, $title);
            $duration = $this->getDuration($videoUrl);
            Auth::user()->chargeCredit('creatomate', 'creatomate', $duration, null, null, 17, 17);
            return response()->json([
                'success' => true,
                'message' => 'Video created successfully!',
                'data' => [
                    'video_url' => $videoUrl,
                    'snapshot_url' => $snapshotUrl,
                    'id' => $record->id,
                ],
                'credits' => $user->credit ?? 0,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Video creation failed.',
            ], 500);
        }
    }

    public function getDuration($full_video_path) {
        $getID3 = new \getID3;
        $videoPath = storage_path('app/public/video.mp4');
        file_put_contents($videoPath, file_get_contents($full_video_path));
        $file = $getID3->analyze($videoPath);
        $playtime_seconds = $file['playtime_seconds'];
        $second = date('s', $playtime_seconds);
        $minutes = date('i', $playtime_seconds);
        $total = $minutes*60 + $second;
        return $total;
    }

    private function sendToCreatomate($templateId, $data)
    {
        $apiKey = env('CREATOMATE_API_KEY');
        $client = new Client($apiKey);

        $output = $client->render([
            'template_id' => $templateId,
            'modifications' => $data,
        ]);
        return $output;
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->creatomateVideoService->deleteFileById($id);
        return response()->json($response);
    }

    public function edit(Request $request) {
        $id = $request->id;
        $record = $this->creatomateVideoService->getVideoById($id);
        $template = $this->creatomateTemplateService->getTemplateById($record->creatomate_template_id);
        return Inertia::render('Client/Creatomate/Edit', ['record' => $record, 'template' => $template]);
    }
}

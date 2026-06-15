<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Constants\Sender;
use App\Exceptions\UsageLimitExceededException;
use App\Helper\Helpers;
use App\Helper\ImageReaderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AIImage\GenerateContentRequest;
use App\Services\AIBannerPostService;
use App\Services\AIChatService;
use App\Services\AIMediaService;
use App\Services\ChatGPTService;
use App\Services\AIAssistantsService;
use App\Services\DocumentReaderService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\CreditHistoryService;
use App\Services\ChatClaudeService;

class AiImageAdvertisementController extends Controller
{
    private $aIBannerPostService;
    private $aIMediaService;
    private $chatGPTService;
    private $getImgApiBaseUrl;
    private $getImgApiModelPath;
    private $getImgToken;
    private $imageReaderService;
    private $replicateApiToken;
    private $creditHistoryService;

    private $aIAssistantsService;
    private $chatClaudeService;

    public function __construct(ChatGPTService $chatGPTService, AIBannerPostService $aIBannerPostService, ImageReaderService $imageReaderService, AIAssistantsService $aIAssistantsService,  CreditHistoryService $creditHistoryService, ChatClaudeService $chatClaudeService)
    {
        $this->chatGPTService = $chatGPTService;
        $this->aIBannerPostService = $aIBannerPostService;
        $this->creditHistoryService = $creditHistoryService;
        $this->getImgApiBaseUrl = env('GETIMG_API_BASE_URL', 'https://api.getimg.ai/v1/');
        $this->getImgApiModelPath = env('GETIMG_API_MODEL_PATH');
        $this->getImgToken = env('GETIMG_API_TOKEN');

        $this->replicateApiToken = env('REPLICATE_API_TOKEN');

        $this->imageReaderService = $imageReaderService;

        $this->aIAssistantsService = $aIAssistantsService;
        $this->chatClaudeService = $chatClaudeService;
    }
    public function index(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AiImageAdvertisement/Index', ['ai_assistant' => $ai_assistant]);
    }

    public function generateImage(Request $request)
    {
        $model = $request->input('model');
        $request->validate([
            'model' => 'required|string',
            'description' => 'required|string|max:2000',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'aspect_ratio' => 'required|string|max:255',
        ]);

        $description = $request->input('description');
        $width = $request->input('width');
        $height = $request->input('height');
        $aspect_ratio = $request->input('aspect_ratio');
        $typeOutput = $request->input('typeOutput') ?? 'banner';
        try {
            $user = auth()->user();
            $user->decrementUsage(config('constant.assistant_types.image'));
            $prompt = $this->chatGPTService->getResponse2('Dịch văn bản sau sang tiếng Anh: ' . $description);
            if ($typeOutput == 'banner') {
                $sologan = $this->chatGPTService->getResponse2('Please create slogan banner:' . $prompt);
                $promt_primary = "As a banner designer with 15 years of experience, please design a banner with the slogan: $sologan
                Some aspects I typically consider when designing banners:
                Target audience
                Brand message
                Color psychology
                Typography hierarchy
                Visual elements
                Layout composition
                Call to action
                Responsive design
                ";
            } else {
                $sologan = $this->chatGPTService->getResponse2('Please create slogan banner:' . $prompt);
                $promt_primary = "As a poster designer with 15 years of experience, please design a poster with the slogan: $sologan
                Some aspects I typically consider when designing banners:
                Target audience
                Brand message
                Color psychology
                Typography hierarchy
                Visual elements
                Layout composition
                Call to action
                Responsive design
                ";
            }
            $promt_primary = $this->chatClaudeService->claudeAsk($promt_primary);
            switch ($model) {
                case 'flux-1.1-pro':
                    $imageContent = $this->generateImageFlux($promt_primary, $width, $height, $aspect_ratio);
                    break;
//                default:
//                    $imageContent = $this->generateImageReplicate2($promt_primary, $width, $height, $aspect_ratio);
//                    break;
            }

            if ($imageContent === false) {
                Log::error('Không thể tải hình ảnh từ URL.');
            }

            $filename = 'images/' . uniqid('image_', true) . '.png';
            Storage::disk('s3')->put($filename, $imageContent);

            $s3Url = Storage::disk('s3')->url($filename);
            Log::info('S3 url generate image: ' . $s3Url);

            $data = [
                'user_id' => auth('web')->id(),
                'description' => $description,
                'width' => $width,
                'height' => $height,
                's3_url' => $filename,
                'type' => $typeOutput
            ];
            $record = $this->aIBannerPostService->storeMedia($data);
            $url = Helpers::preSignedS3Url($filename);
            return response()->json(['s3_url' => $url, 'path' => $filename, 'generate_file' => $record, 'credits' => $user->credit ?? 0], 200);
        } catch (UsageLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateImageFlux($prompt, $width, $height, $aspect_ratio = 'custom')
    {
        $apiUrl = 'https://api.replicate.com/v1/models/black-forest-labs/flux-1.1-pro/predictions';
        $requestData = [
            'input' => [
                'prompt' => $prompt,
                'aspect_ratio' => $aspect_ratio,
                'width' => $width,
                'height' => $height,
                'output_format' => 'png',
                'output_quality' => 100,
                'safety_tolerance' => 2,
                'prompt_upsampling' => true,
                'seed' => rand(1000,9999), // random seed
            ]
        ];

        Log::info('Request data for Replicate:', $requestData);

        try {
            // Gửi yêu cầu POST đến API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
                'Prefer' => 'wait',
            ])->post($apiUrl, $requestData);

            // Kiểm tra lỗi phản hồi
            if ($response->failed()) {
                throw new \Exception('Không thể kết nối tới API Replicate. Vui lòng thử lại sau.');
            }

            $responseData = $response->json();
            Log::info('Response from Replicate API:', $responseData);

            if (!isset($responseData['urls']['get'])) {
                throw new \Exception('Không nhận được phản hồi hợp lệ từ Replicate.');
            }

            // Lấy URL kết quả từ phản hồi ban đầu
            $getUrl = $responseData['urls']['get'];

            // Gửi yêu cầu GET để lấy dữ liệu hình ảnh
            $responseImg = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
            ])->get($getUrl);

            $responseImgData = $responseImg->json();
            Log::info('Response from Image Fetch:', $responseImgData);

            if (!isset($responseImgData['output'][0])) {
                throw new \Exception('Không nhận được URL hình ảnh hợp lệ từ API.');
            }

            // Tải nội dung hình ảnh
            $imageUrl = $responseImgData['output'];
            $imageContent = file_get_contents($imageUrl);

            // Trừ tiền credit user
            Auth::user()->chargeCredit('flux-1.1-pro', null, null, null, null, 22, 22);
            return $imageContent;
        } catch (\Exception $e) {
            Log::error('Error generating image from Replicate: ' . $e->getMessage());
            throw $e;
        }
    }

    public function history()
    {
        try {
            $list = $this->aIBannerPostService->getHistories();
            return Inertia::render('Client/AiImageAdvertisement/History', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->aIBannerPostService->deleteFileById($id);
        return $response;
    }
}

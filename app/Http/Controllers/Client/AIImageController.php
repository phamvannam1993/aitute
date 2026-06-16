<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Constants\Sender;
use App\Events\MessageAIGeneratedMediaWebhookGPUMyAIImage;
use App\Exceptions\UsageLimitExceededException;
use App\Helper\FFmpegHelper;
use App\Helper\Helpers;
use App\Helper\ImageReaderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AIImage\GenerateContentRequest;
use App\Jobs\CheckFinetuneStatus;
use App\Models\AIGeneratedMedia;
use App\Models\MyAIImage;
use App\Models\MyAIImageTemplate;
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
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Validator;


class AIImageController extends Controller
{
    private $aIMediaService;
    private $chatGPTService;
    private $getImgApiBaseUrl;
    private $getImgApiModelPath;
    private $getImgToken;
    private $imageReaderService;
    private $replicateApiToken;
    private $creditHistoryService;

    private $aIAssistantsService;

    public function __construct(ChatGPTService $chatGPTService, AIMediaService $aIMediaService, ImageReaderService $imageReaderService, AIAssistantsService $aIAssistantsService,  CreditHistoryService $creditHistoryService,         private UserService $userService)
    {
        $this->chatGPTService = $chatGPTService;
        $this->aIMediaService = $aIMediaService;
        $this->creditHistoryService = $creditHistoryService;
        $this->getImgApiBaseUrl = env('GETIMG_API_BASE_URL', 'https://api.getimg.ai/v1/');
        $this->getImgApiModelPath = env('GETIMG_API_MODEL_PATH');
        $this->getImgToken = env('GETIMG_API_TOKEN');

        $this->replicateApiToken = env('REPLICATE_API_TOKEN');

        $this->imageReaderService = $imageReaderService;

        $this->aIAssistantsService = $aIAssistantsService;
    }
    public function index(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIImage/Index', ['ai_assistant' => $ai_assistant]);
    }

    public function imageToImage(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIImage/Image', ['ai_assistant' => $ai_assistant]);
    }

    public function photoBeauty(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIImage/PhotoBeauty', ['ai_assistant' => $ai_assistant]);
    }

    public function generateImage(Request $request)
    {
        $user = Auth::user();
        $validatedModel = $request->validate([
            'model' => 'required|string',
        ]);
        $model = $request->input('model');

        // if ($model === 'Flux Schnell') {
        //     $request->validate([
        //         'description' => 'required|string|max:2000',
        //     ]);
        // }
        // else if ($model === 'Aesthetic') {
        //     $request->validate([
        //         'description' => 'required|string|max:2000',
        //     ]);
        // }else {
        //     $request->validate([
        //         'description' => 'required|string|max:2000',
        //         'style' => 'required|string|max:255',
        //         'artist' => 'required|string|max:255',
        //         'width' => 'required|integer|min:1',
        //         'height' => 'required|integer|min:1',
        //         'aspect_ratio' => 'required|string|max:255',
        //     ]);
        // }

        $description = $request->input('description');
        $style = $request->input('style');
        $artist = $request->input('artist');
        $width = $request->input('width');
        $height = $request->input('height');
        $aspect_ratio = $request->input('aspect_ratio');

        try {
            $user = auth()->user();
            $user->decrementUsage(config('constant.assistant_types.image'));
            $prompt = $this->chatGPTService->getResponse('Dịch văn bản sau sang tiếng Anh: ' . $description);
            $negative_prompt = $this->chatGPTService->getResponse('Dịch văn bản sau sang tiếng Anh: phong cách ' . $style . ', nghệ sĩ ' . $artist);
            $imageContent = "";
            switch ($model) {
                case 'Latent Consistency':
                    $imageContent = $this->generateImageGetIMG($prompt, $negative_prompt, $width, $height);
                    break;
                case 'Flux Schnell':
                    $imageContent = $this->generateImageReplicate($prompt, $width, $height, $aspect_ratio);
                    break;
                case 'Aesthetic':
                    $imageContent = $this->generateAestheticImage($prompt, $width, $height, $aspect_ratio);
                    break;
                case 'GPT Image':
                    $imageContent = $this->generateImageOpenAI($prompt, $aspect_ratio);
                    break;
                default:
                    $imageContent = $this->generateImageReplicate($prompt, $width, $height, $aspect_ratio);
                    break;
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
                'style' => $style,
                'artist' => $artist,
                'width' => $width,
                'height' => $height,
                's3_url' => $filename,
                'type' => 'image'
            ];
            $record = $this->aIMediaService->storeMedia($data);
            $url = Helpers::preSignedS3Url($filename);
            return response()->json(['url' => $url, 'path' => $filename, 'generate_file' => $record, 'credits' => $user->credit ?? 0], 200);
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

    public function suggestContent(Request $request)
    {
        $content = $request->input('content');
        $content =  $content.' gợi ý phối cảnh 50 từ tiếng việt';
        $prompt = $this->chatGPTService->getResponse($content);
        return response()->json([
            'data' => $prompt,
        ]);
    }

    public function generateImageToImage(Request $request) {
        try {
            $user = Auth::user();
            $request->validate([
                'model' => 'required|string',
            ]);
            $model = $request->input('model');
            $request->validate([
                'description' => 'required|string|max:2000',
            ]);
            $description = $request->input('description');
            $textTran = $this->chatGPTService->getResponse("Dịch văn bản sau sang tiếng Anh: " . $description);
            $textTran = str_replace('"', '', $textTran);
            $textTran = str_replace('"', '', $textTran);
            $textTrans = explode("English is", $textTran);
            if(count($textTrans) > 1) {
                $textTran = $textTrans[1];
            }
            $textTrans = explode("Translates to", $textTran);
            if(count($textTrans) > 1) {
                $textTran = $textTrans[1];
            }
            $image_file = $request->file('image_file');
            $promptImage = "";
            if (isset($request["s3_url"]) && !empty($request["s3_url"])) {
                $promptImage = $request["s3_url"];
            } else if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
                $promptImage = Helpers::preSignedS3Url($image_file);
            }

            $user = auth()->user();
            // Prepare data for API request
            $requestData = [
                'seed' => 42,
                'prompt' => $textTran,
                'output_format' => "jpg",
                'subject' => $promptImage,
                'model' => $model,
                'negative_prompt' =>  'low quality,blur',
                'randomise_poses' => true,
                'number_of_outputs' => 1,
                'number_of_images_per_pose' => 1
            ];
            // Make API request to Segmind
            $response = Http::timeout(600)->withHeaders([
                'x-api-key' => config('segmind.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.segmind.com/v1/consistent-character', $requestData);
            if($response->getStatusCode() != 200) {
                if($response->getStatusCode() == 502) {
                    return response()->json(['success' => false, 'message' => 'Ảnh của bạn không hợp lệ.Vui lòng chọn ảnh khác'], 500);
                } else {
                    return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện'], 500);
                }
            }
            $filename = 'images/' . uniqid('image_', true) . '.png';
            Storage::disk('s3')->put($filename, $response);
            $url = Helpers::preSignedS3Url($filename);

            $data = [
                'user_id' => auth('web')->id(),
                'description' => $description,
                'model' => 'consistent-character',
                's3_url' => $filename,
                'type' => 'image'
            ];
            $record = $this->aIMediaService->storeMedia($data);
            Auth::user()->chargeCredit('consistent-character', null, null, null, null, 20, 20);
            return response()->json(['s3_url' => $url, 'path' => $filename, 'generate_file' => $record, 'credits' => $user->credit ?? 0], 200);
        } catch(\Exception $ex) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện'], 500);
        }
    }

    public function generatePhotoBeauty(Request $request) {
        try {
            $user = Auth::user();
            $image_file = $request->file('image_file');
            $promptImage = "";
            if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
                $promptImage = Helpers::preSignedS3Url($image_file);
            } else {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn ảnh bạn muốn sửa'], 400);
            }

            $requestData = [
                'version' => "7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56",
                "input" => [
                    "image" => $promptImage,
                    "codeformer_fidelity" => isset($request["codeformer_fidelity"]) ? (float)$request["codeformer_fidelity"] : 0.5
                ]
            ];
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
            ])->timeout(1200)->post("https://api.replicate.com/v1/predictions", $requestData);
            if(!isset($response->json()["id"])) {
                return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện'], 500);
            }
            $id = $response->json()["id"];
            $data = [
                'user_id' => $user->id,
                'model' => 'photo-beauty',
                'description' => "Làm đẹp ảnh",
                's3_url' => "",
                'task_id' => $id,
                's3_url_mobile' => "",
                'type' => 'image'
            ];
            $record = $this->aIMediaService->storeMedia($data);
            return response()->json(['id' => $record->id], 200);
        } catch(\Exception $ex) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện'], 500);
        }
    }

    public function getImage($id) {
        try {
            $aiMedia = $this->aIMediaService->getOneById($id);
            if(empty($aiMedia)) {
                return response()->json(['success' => false, 'message' => 'Không tồn tại dữ liệu'], 400);
            }

            $task_id = $aiMedia->task_id;
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
            ])->timeout(1200)->get("https://api.replicate.com/v1/predictions/".$task_id);
            $paths = [];
            if(isset($response->json()["status"])) {
                $status = $response->json()["status"];
                if($status != "succeeded") {
                    return response()->json(['success' => false, 'message' => 'Đang trong quá trình tạo ảnh', 'status' => $status], 200);
                }

                $filename = 'images/' . uniqid('image_', true) . '.png';
                $imgContent = file_get_contents($response->json()["output"]);
                file_put_contents(storage_path('app/public/'.$filename), $imgContent);
                Storage::disk('s3')->put($filename, $imgContent);
                $url = Helpers::preSignedS3Url($filename);
                // Resize image for mobile
                $paths[] =  $filename;
                $filename_mobile = 'images/' . uniqid('image_', true) . '.png';
                $paths[] =  $filename_mobile;
                FFmpegHelper::resizeImage(storage_path('app/public/'.$filename), storage_path('app/public/'.$filename_mobile)); // Resize image for mobile
                Storage::disk('s3')->put($filename_mobile, file_get_contents(storage_path('app/public/'.$filename_mobile)));
                $urlMobile = Helpers::preSignedS3Url($filename_mobile);
                $aiMediaCheck = $this->aIMediaService->getOneById($id);
                $this->cleanupFiles($paths);
                if(!empty($aiMediaCheck->s3_url)) {
                    return response()->json(['s3_url' => $url, 's3_url_mobile' => $urlMobile, 'path' => $filename, 'generate_file' => $aiMediaCheck, 'credits' => $user->credit ?? 0], 200);
                }

                $aiMedia->update(["s3_url" => $filename,'s3_url_mobile' => $filename_mobile]);

                Auth::user()->chargeCredit('photo-beauty', null, null, null, null, 24, 25);
                return response()->json(['s3_url' => $url, 's3_url_mobile' => $urlMobile, 'path' => $filename, 'generate_file' => $aiMedia, 'credits' => $user->credit ?? 0], 200);
            } else {
                return response()->json(["success" => false, "message" => "Có lỗi xảy ra trong quá trình thực hiện"], 500);
            }
        } catch(\Exception $ex) {
            return response()->json(["success" => false, "message" => "Có lỗi xảy ra trong quá trình thực hiện"], 500);
        }

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

    private function resizeImage($imageContent, $targetWidth = null, $targetHeight = null) {
        // Create image from string content
        $source = imagecreatefromstring($imageContent);
        if (!$source) {
            throw new \Exception('Unable to create image from string');
        }

        // Get original dimensions
        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);

        // If no target dimensions provided, resize to 50% of original
        if (!$targetWidth && !$targetHeight) {
            $targetWidth = $originalWidth * 0.6;
            $targetHeight = $originalHeight * 0.6;
        }
        // If only one dimension is provided, maintain aspect ratio
        elseif (!$targetHeight) {
            $ratio = $targetWidth / $originalWidth;
            $targetHeight = $originalHeight * $ratio;
        }
        elseif (!$targetWidth) {
            $ratio = $targetHeight / $originalHeight;
            $targetWidth = $originalWidth * $ratio;
        }

        // Create new image with target dimensions
        $destination = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG images
        imagealphablending($destination, false);
        imagesavealpha($destination, true);

        // Resize the image
        imagecopyresampled(
            $destination,
            $source,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        // Start output buffering
        ob_start();

        // Save the image to buffer
        imagepng($destination, null, 9); // Use highest compression level (9)

        // Get the image content
        $resizedImageContent = ob_get_clean();

        // Free up memory
        imagedestroy($source);
        imagedestroy($destination);

        return $resizedImageContent;
    }

    public function generateMultipleImages(Request $request)
    {
        $user = Auth::user();

        $validatedModel = $request->validate([
            'model' => 'required|string',
        ]);
        $model = $request->input('model');

        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
        ]);

        if ($model !== 'Flux Schnell' && $model !== 'Aesthetic') {
            $validatedData = array_merge($validatedData, $request->validate([
                'style' => 'required|string|max:255',
                'artist' => 'required|string|max:255',
                'width' => 'required|integer|min:1',
                'height' => 'required|integer|min:1',
                'aspect_ratio' => 'required|string|max:255',
            ]));
        }

        $description = $request->input('description');
        $style = $request->input('style');
        $artist = $request->input('artist');
        $width = $request->input('width');
        $height = $request->input('height');
        $aspect_ratio = $request->input('aspect_ratio');

        try {
            $user->decrementUsage(config('constant.assistant_types.image'));

            // Generate four different descriptions
            $descriptions = [];
            for ($i = 0; $i < 4; $i++) {
                $descriptions[] = $this->chatGPTService->getResponse(
                    "Tạo mô tả khác nhau cho: " . $description . ". Biến thể $i."
                );
            }

            dd($descriptions);

            $imageUrls = [];

            // Generate images for each description
            foreach ($descriptions as $prompt) {
                $negative_prompt = $this->chatGPTService->getResponse(
                    'Dịch văn bản sau sang tiếng Anh: phong cách ' . $style . ', nghệ sĩ ' . $artist
                );

                $imageContent = "";
                if ($model === 'Latent Consistency') {
                    $imageContent = $this->generateImageGetIMG($prompt, $negative_prompt, $width, $height);
                } else {
                    $imageContent = $this->generateImageReplicate($prompt, $width, $height, $aspect_ratio);
                }

                if ($imageContent === false) {
                    Log::error('Không thể tải hình ảnh từ URL.');
                    continue;
                }

                $filename = 'images/' . uniqid('image_', true) . '.png';
                Storage::disk('s3')->put($filename, $imageContent);

                $s3Url = Storage::disk('s3')->url($filename);
                Log::info('S3 url generate image: ' . $s3Url);

                $data = [
                    'user_id' => auth('web')->id(),
                    'description' => $description,
                    'style' => $style,
                    'artist' => $artist,
                    'width' => $width,
                    'height' => $height,
                    's3_url' => $filename,
                    'type' => 'image'
                ];
                $record = $this->aIMediaService->storeMedia($data);
                $imageUrls[] = Helpers::preSignedS3Url($filename);
            }

            return response()->json(['urls' => $imageUrls, 'credits' => $user->credit ?? 0], 200);

        } catch (UsageLimitExceededException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function generateImageForPost(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string'
        ]);

        $description = $request->input('description');

        try {
            $prompt = $this->chatGPTService->getResponse('Dịch văn bản sau sang tiếng Anh và rút gọn thành câu chủ đề độ dài dưới 50 ký tự: ' . $description, null, 'gpt-4o-mini');

            $apiUrl = $this->buildApiUrlgetImg();

            Log::info('API URL:', [$apiUrl]);

            $requestData = [
                'prompt' => $prompt . ', hd',
                "output_format" => "png",
                "response_format" => "url",
            ];

            Log::info('Request data:', $requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->getImgToken}",
            ])->post($apiUrl, $requestData);

            if ($response->failed()) {
                throw new \Exception('Không thể kết nối tới API. Vui lòng thử lại sau.');
            }

            $responseData = $response->json();
            Log::info('Response from API:', $responseData);

            if (!isset($responseData['url'])) {
                throw new \Exception('URL không hợp lệ. Vui lòng kiểm tra lại yêu cầu.');
            }

            $get_url = $responseData['url'];
            $imageContent = file_get_contents($get_url);
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
                's3_url' => $filename,
                'type' => 'image'
            ];
            $record = $this->aIMediaService->storeMedia($data);
            $url = Helpers::preSignedS3Url($filename);
            return response()->json(['url' => $url, 'generate_file' => $record], 200);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function history()
    {
        try {
            $list = $this->aIMediaService->getHistories();
            return Inertia::render('Client/AIImage/History', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function historyBeautyImage()
    {
        try {
            $list = $this->aIMediaService->getHistoriesBeauty();
            return Inertia::render('Client/AIImage/HistoryBeauty', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function listMedia(Request $request)
    {
        try {
            $type = $request->type;
            $per_page = $request->per_page;
            $list = $this->aIMediaService->getListMedia($type, $per_page);
            return response()->json(['data' => $list], 200);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->aIMediaService->deleteFileById($id);
        return $response;
    }
    private function buildApiUrlGetImg()
    {
        $baseUrl = rtrim($this->getImgApiBaseUrl, '/');
        $modelPath = trim($this->getImgApiModelPath, '/');

        if (empty($modelPath)) {
            return $baseUrl;
        } else {
            return $baseUrl . '/' . $modelPath . '/text-to-image';
        }
    }

    public function generateContent(GenerateContentRequest $request)
    {
        try {
            $prompt = $request->prompt;
            $imageUrl = $request->imageUrl;
            $postingContent = $request->postingContent;
            $lang = 'vi';
            $model = 'gpt-4o-mini';
            $content = '';

            if ($imageUrl) {
                $content = $this->imageReaderService->extractText($imageUrl);
                if (!$content) {
                    return response()->json([
                        'error' => 'Không thể trích xuất nội dung từ hình ảnh.'
                    ], 400);
                }
            }

            $promptTemplate = config('chatgpt.seo-generate');
            $fullPrompt = str_replace([':prompt', ':lang', ':file_content', ':posting'], [$prompt, $lang, $content, $postingContent], $promptTemplate);

            $response = $this->chatGPTService->getResponse($fullPrompt, null, $model);
            $cleanResponse = preg_replace('/(\*\*|###)(.*?)\1/', '', $response);

            if (!$response) {
                return response()->json([
                    'error' => 'Không nhận được phản hồi từ GPT-4.'
                ], 500);
            }

            return response()->json([
                'content' => $cleanResponse
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã xảy ra lỗi khi xử lý yêu cầu.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateImageGetIMG($prompt, $negative_prompt, $width=1024, $height=1024)
    {
        $apiUrl = $this->buildApiUrlgetImg();

        Log::info('API URL:', [$apiUrl]);

        $requestData = [
            'prompt' => $prompt . ', hd',
            'negative_prompt' => $negative_prompt,
            'width' => $width,
            'height' => $height,
            "output_format" => "png",
            "response_format" => "url",
        ];

        Log::info('Request data:', $requestData);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->getImgToken}",
        ])->post($apiUrl, $requestData);

        if ($response->failed()) {
            throw new \Exception('Không thể kết nối tới API. Vui lòng thử lại sau.');
        }

        $responseData = $response->json();
        Log::info('Response from API:', $responseData);

        if (!isset($responseData['url'])) {
            throw new \Exception('URL không hợp lệ. Vui lòng kiểm tra lại yêu cầu.');
        }

        $get_url = $responseData['url'];
        $imageContent = file_get_contents($get_url);
//        $this->handleUsageCredit('latent-consistency');
        // Trừ tiền credit user
        Auth::user()->chargeCredit('latent-consistency', null, null, null, null, 2, 2);
        return $imageContent;
    }

    private function generateImageReplicate($prompt, $width, $height, $aspect_ratio='1:1')
    {
        $apiUrl = 'https://api.replicate.com/v1/models/black-forest-labs/flux-schnell/predictions';
        $requestData = [
            'input' => [
                'prompt' => $prompt,
                'go_fast' => true,
                'megapixels' => '1',
                'num_outputs' => 1,
                'aspect_ratio' => $aspect_ratio,
                'output_format' => 'png',
                'output_quality' => 100,
                'num_inference_steps' => 4
            ]
        ];

        Log::info('Request data for Replicate:', $requestData);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
                'Prefer' => 'wait',
            ])->post($apiUrl, $requestData);

            if ($response->failed()) {
                throw new \Exception('Không thể kết nối tới API Replicate. Vui lòng thử lại sau.');
            }

            $responseData = $response->json();
            Log::info('Response from Replicate API:', $responseData);

            if (!isset($responseData['output'])) {
                throw new \Exception('Không nhận được phản hồi hợp lệ từ Replicate.');
            }

            $responseData = $response->json();

            Log::info('Response from AnimeIMG API:', $responseData);
            $get_url = $responseData['urls']['get'];
            $responseImg = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->replicateApiToken}",
            ])->get($get_url);
            $responseImgData = $responseImg->json();
            Log::info($responseImgData);
            $output = $responseImgData['output'];
            $imageContent = file_get_contents($output[0]);
//            $this->handleUsageCredit('flux-schnell');
            // Trừ tiền credit user
            Auth::user()->chargeCredit('flux-schnell', null, null, null, null, 2, 2);
            return $imageContent;
        } catch (\Exception $e) {
            Log::error('Error generating image from Replicate: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateImageOpenAI($prompt, $aspect_ratio = '1:1')
    {
        // GPT Image 2 yêu cầu size là bội số của 16. Map theo tỉ lệ khung hình.
        switch ($aspect_ratio) {
            case '16:9':
                $size = '1280x720';
                break;
            case '9:16':
                $size = '720x1280';
                break;
            default:
                $size = '1024x1024';
                break;
        }

        $requestData = [
            'model' => config('openai.image_model'),
            'prompt' => $prompt,
            'size' => $size,
            'quality' => 'medium',
            'n' => 1,
        ];

        Log::info('Request data for OpenAI GPT Image:', $requestData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('openai.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(180)->post(config('openai.image_url'), $requestData);

            if ($response->failed()) {
                Log::error('OpenAI GPT Image API error:', $response->json() ?? []);
                throw new \Exception('Không thể kết nối tới API OpenAI. Vui lòng thử lại sau.');
            }

            $responseData = $response->json();

            if (!isset($responseData['data'][0]['b64_json'])) {
                throw new \Exception('Không nhận được phản hồi hợp lệ từ OpenAI.');
            }

            $imageContent = base64_decode($responseData['data'][0]['b64_json']);

            // Trừ tiền credit user (tái dùng pricing flux-schnell: 2 credit)
            Auth::user()->chargeCredit('flux-schnell', null, null, null, null, 2, 2);

            return $imageContent;
        } catch (\Exception $e) {
            Log::error('Error generating image from OpenAI: ' . $e->getMessage());
            throw $e;
        }
    }

    public function downloadImage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $result;
    }

    private function translatePrompt($prompt) {
        try {
            $response = Http::withHeaders([
                'x-api-key' => config('claude.api_key'),
                'anthropic-version' => config('claude.version'),
                'content-type' => 'application/json'
            ])->post(config('claude.url'), [
                'model' => config('claude.chat_model'),
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Translate to English and return only the translation without quotes, punctuation or additional text, no other text. Return ONLY the English translation (no articles, no explanations, no additional text): " . $prompt
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('Không thể kết nối tới Claude API.');
            }

            $responseData = $response->json();
            if (!isset($responseData['content'][0]['text'])) {
                throw new \Exception('Không nhận được phản hồi hợp lệ từ Claude API.');
            }

            return $responseData['content'][0]['text'];
        } catch (\Exception $e) {
            Log::error('Error translating with Claude: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateAestheticImage($prompt, $width, $height, $aspect_ratio='1:1')
    {
        // Dịch prompt sang tiếng Anh
        // dd($aspect_ratio);
            $dimensions = match($aspect_ratio) {
                '16:9' => ['width' => 1360, 'height' => 768],
                '9:16' => ['width' => 768, 'height' => 1360],
                default => ['width' => 1024, 'height' => 1024],
            };

            try {
                $englishPrompt = $this->translatePrompt($prompt);
            } catch (\Exception $e) {
                $englishPrompt = $prompt;
            }

            $apiUrl = 'https://api.replicate.com/v1/predictions';
            // dd($dimensions['width']);


            $requestData = [
                "version" => "a45f82a1382bed5c7aeb861dac7c7d191b0fdf74d8d57c4a0e6ed7d4d0bf7d24",
                'input' => [
                    'prompt' => $englishPrompt . "colorful oil painting",
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height'],
                    "disable_safety_checker" => true
                ]
            ];

            Log::info('Request data for Replicate:', $requestData);

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->replicateApiToken}",
                    'Content-Type' => 'application/json',
                    'Prefer' => 'wait',
                ])->post($apiUrl, $requestData);

                if ($response->failed()) {
                    throw new \Exception('Không thể kết nối tới API Replicate. Vui lòng thử lại sau.');
                }

                $responseData = $response->json();
                Log::info('Response from Replicate API Aesthetic:', $responseData);

                if (!isset($responseData['output'])) {
                    throw new \Exception('Không nhận được phản hồi hợp lệ từ Replicate.');
                }

                $get_url = $responseData['urls']['get'];
                $responseImg = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$this->replicateApiToken}",
                ])->get($get_url);

                $responseImgData = $responseImg->json();
                Log::info($responseImgData);
                $output = $responseImgData['output'];
                $imageContent = file_get_contents($output[0]);

                Auth::user()->chargeCredit('flux-schnell', null, null, null, null, 2, 2);
                return $imageContent;

            } catch (\Exception $e) {
                Log::error('Error generating image from Replicate: ' . $e->getMessage());
                throw $e;
            }
    }
    public function getMediaListByType(Request $request)
    {
        $model = $request->input('model');
        $type = $request->input('type', 'image');
        $perPage = $request->input('per_page', 20);

        $mediaList = $this->aIMediaService->getListMediaByModel($model, $type, $perPage);

        if ($mediaList) {
            return response()->json([
                'success' => true,
                'data' => $mediaList,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error retrieving media list.',
        ]);
    }

    public function faceSwap(Request $request)
    {
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        $imageUrl = $request->imageUrl;
        $targetUrl = $request->targetUrl;
        return Inertia::render('Client/AIImage/FaceSwap', ['ai_assistant' => $ai_assistant, 'imageUrl' => $imageUrl, 'targetUrl' => $targetUrl]);
    }

    function generateFaceSwap(Request $request) {
        try {
            // Get source and target images
            $sourceImage = $request->file('source_img');
            $targetImage = $request->file('target_img');
            if(!isset($targetImage)){
                $target_url = $request->target_url;
                if (filter_var($target_url, FILTER_VALIDATE_URL)) {
                    $imageFile = file_get_contents($target_url);
                    $tempFile = storage_path('app/public/image' . uniqid('img_') . '.png');
                    file_put_contents($tempFile, $imageFile);
                } else {
                    $localFile = public_path($target_url);

                    if (!file_exists($localFile)) {
                        throw new \Exception("File not found at path: $localFile");
                    }
                }
                // Create the UploadedFile object
                $targetImage = new \Illuminate\Http\UploadedFile(
                    !empty($tempFile) ? $tempFile : $localFile,
                    'image_template.png',
                    mime_content_type(!empty($tempFile) ? $tempFile : $localFile),
                    null,
                    true
                );

            }

            if(!$sourceImage){
                $source_url = $request->source_url;
                $localFile = public_path($source_url);

                $source_url = $request->target_url;
                if (filter_var($source_url, FILTER_VALIDATE_URL)) {
                    $imageFile = file_get_contents($source_url);
                    $tempFile = storage_path('app/public/image' . uniqid('img_') . '.png');
                    file_put_contents($tempFile, $imageFile);
                } else {
                    $localFile = public_path($source_url);
                    if (!file_exists($localFile)) {
                        throw new \Exception("File not found at path: $localFile");
                    }
                }

                $sourceImage = new \Illuminate\Http\UploadedFile(
                    !empty($tempFile) ? $tempFile : $localFile,
                    'image_template.png',
                    mime_content_type(!empty($tempFile) ? $tempFile : $localFile),
                    null,
                    true
                );
            }


            if (!$sourceImage || !$targetImage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng tải ảnh mà bạn muốn thay khuôn mặt'
                ], 400);
            }

            // Upload original images to S3
            $sourceS3Path = 'swapfaceimg/source/' . uniqid('source_', true) . '.' . $sourceImage->getClientOriginalExtension();
            $targetS3Path = 'swapfaceimg/target/' . uniqid('target_', true) . '.' . $targetImage->getClientOriginalExtension();

            try {
                // Upload source image
                Storage::disk('s3')->put($sourceS3Path, file_get_contents($sourceImage->getRealPath()));
                $sourceS3Url = Storage::disk('s3')->url($sourceS3Path);

                // Upload target image
                Storage::disk('s3')->put($targetS3Path, file_get_contents($targetImage->getRealPath()));
                $targetS3Url = Storage::disk('s3')->url($targetS3Path);

                Log::info('Original images uploaded to S3', [
                    'source' => $sourceS3Url,
                    'target' => $targetS3Url
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to upload original images to S3: ' . $e->getMessage());
                throw new \Exception('Failed to upload original images');
            }

            // Convert images to base64 for API request
            $sourceBase64 = base64_encode(file_get_contents($sourceImage->getRealPath()));
            $targetBase64 = base64_encode(file_get_contents($targetImage->getRealPath()));

            // Prepare data for API request
            $requestData = [
                'source_img' => $sourceBase64,
                'target_img' => $targetBase64,
                'input_faces_index' => 0,
                'source_faces_index' => 0,
                'face_restore' => 'codeformer-v0.1.0.pth',
                'base64' => true
            ];

            // Make API request to Segmind
            $response = Http::timeout(600)->withHeaders([
                'x-api-key' => config('segmind.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.segmind.com/v1/faceswap-v2', $requestData);

            if(!empty($tempFile)){
                unlink($tempFile);
            }

            if ($response->successful()) {
                $responseData = $response->json();
                $imageContent = base64_decode($responseData['image']);

                if ($imageContent === false) {
                    Log::error('Failed to decode base64 image');
                    throw new \Exception('Invalid image data received');
                }

                // Generate unique filename for result image
                $resultPath = 'swapfaceimg/results/' . uniqid('result_', true) . '.png';

                // Upload result to S3
                try {
                    Storage::disk('s3')->put($resultPath, $imageContent);
                    $resultS3Url = Storage::disk('s3')->url($resultPath);
                    Log::info('Result image uploaded to S3: ' . $resultS3Url);

                    // Save to database with all S3 paths
                    $aiGeneratedMedia = $this->aIMediaService->saveSwapFace(
                        $resultPath,    // Result image path
                        $sourceS3Path,  // Source image path
                        $targetS3Path   // Target image path
                    );

                    // Trừ tiền credit user
                    Auth::user()->chargeCredit('image_to_image', null, null, null, null, 7, 7);
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'image' => 'data:image/png;base64,' . $responseData['image'],
                            'source_path' => $sourceS3Url,
                            'target_path' => $targetS3Url,
                            'result_path' => $resultS3Url,
                            'aiGeneratedMedia' => $aiGeneratedMedia,
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::error('S3 upload or database save failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'image' => 'data:image/png;base64,' . $responseData['image']
                        ],
                        'warning' => 'Image generated successfully but storage operation failed'
                    ]);
                }
            }

            // Handle specific Segmind API errors
            $errorMessage = 'Có lỗi xảy ra từ dịch vụ xử lý ảnh';
            $errorDetails = $response->json();

            if (isset($errorDetails['error']) && str_contains($errorDetails['error'], 'No face found')) {
                $errorMessage = 'Không tìm thấy khuôn mặt trong ảnh. Vui lòng thử lại với ảnh khác.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => $errorDetails,
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('System error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra từ hệ thống, vui lòng thử lại sau hoặc liên hệ với chúng tôi.',
                'error' => [
                    'type' => 'system_error',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function historyfaceSwap()
    {
        try {
            $list = $this->aIMediaService->getFaceSwapHistories();
            return Inertia::render('Client/AIImage/HistoryFaceSwap', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function myAIImage(Request $request)
    {
        $user = auth('web')->user();
        // $this->updateMyAIImagesStatus();
        $ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        return Inertia::render('Client/AIImage/MyAI', ['ai_assistant' => $ai_assistant, 'my_ai_image_models' => $user->myAiImages]);
    }
    public function updateMyAIImagesStatus()
    {
        $user = auth('web')->user();

        $myAIImages = MyAIImage::where('user_id', $user->id)->get();
        Log::info("Call api update status fine-tune");
        foreach ($myAIImages as $image) {
            if ($image->status !== 'succeeded') {
                $apiUrl = "https://api.replicate.com/v1/predictions/{$image->replicate_id}";
                $apiToken = env('REPLICATE_API_TOKEN');

                try {
                    $response = Http::withToken($apiToken)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->get($apiUrl);

                    if ($response->successful()) {
                        $data = $response->json();
                        $status = $data['status'] ?? null;

                        $image->update([
                            'status' => $status,
                            'model_endpoint' => $data['version'] ?? null,
                            'end_time' => $status === 'succeeded' ? now() : $image->end_time,
                        ]);
                        Log::info("Update status fine-tune: ".json_encode($data));
                    } else {
                        Log::error("Failed to fetch status for replicate ID: {$image->replicate_id}");
                    }
                } catch (\Exception $e) {
                    Log::error("Error updating status for replicate ID: {$image->replicate_id} - {$e->getMessage()}");
                }
            }
        }
    }
    public function generateMyAIImage(Request $request)
    {
        try {
            Log::info("Bắt đầu quá trình tạo ảnh AI.");

            if ($request->input('is_use_mask') == true) {
                $response = $this->processImageGenerationByGPU($request);
            } else {
                $response = $this->processImageGeneration($request);
            }

            if ($response->getStatusCode() === 200) {
                Log::info(message: "Tạo ảnh AI thành công.");
                return $response;
            } else {
                Log::warning("Tạo ảnh AI thất bại. Status Code: " . $response->getStatusCode());
                return response()->json([
                    'message' => 'Quá trình tạo ảnh không thành công.',
                    'status_code' => $response->getStatusCode(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("Lỗi xảy ra trong quá trình tạo ảnh AI.", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình tạo ảnh AI.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function processImageGenerationByGPU(Request $request)
    {
        Log::info('Bắt đầu quá trình tạo ảnh AI.');
        $user = auth('web')->user();
        $myAIImage = MyAIImage::find($request->model_id);

        $validator = Validator::make($request->all(), [
            'aspect_ratio' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error('Validation thất bại.', $validator->errors()->toArray());
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Validation thành công.');

        $imageUrl = $request->input('avatar_url') ?: null;

        if ($request->hasFile('avatar_file')) {
            $avatarFile = $request->file('avatar_file');
            $avatarPath = $avatarFile->store('avatars', 'public');
            $imageUrl = Storage::disk('public')->url($avatarPath);
        }

        if (!$imageUrl) {
            return response()->json(['message' => 'avatar_file hoặc avatar_url là bắt buộc.'], 422);
        }

        $alias_id = $myAIImage->alias_name . '_' . uniqid();

        $defaultData = [
            'user_id' => $user->id,
            'description' => 'Processing data GPU',
            'style' => 'Tự động',
            'width' => 1024,
            'height' => 1024,
            's3_url' => '   ',
            'type' => 'image',
            'model' => 'my_ai_image',
            'alias_name' => $alias_id
        ];

        // Insert the record into the database
        $record = AIGeneratedMedia::create($defaultData);

        // Gọi API postPrompt
        $tensorUrl = $myAIImage->tensor_url;
        $strength_model = $request->input('strength_prompt') ?? 1;
        $denoise = $request->input('denoise') ?? 0.35;
        Log::info('Data: ' . $tensorUrl);
        try {
            $postResponse = $this->postPrompt($imageUrl, $tensorUrl, $myAIImage->alias_name , $alias_id, $strength_model, $denoise);
            $postResponseData = $postResponse->getData();

            if (isset($postResponseData->prompt_id)) {
                $promptId = $postResponseData->prompt_id;
                Log::info("Prompt ID received: {$promptId}");

                return response()->json([
                    'message' => 'Creating image.',
                    'prompt_id' => $promptId,
                    'alias_name' => $alias_id
                ], 200);


            } else {
                return response()->json(['message' => 'Failed to retrieve prompt ID.'], 500);
            }
        } catch (\Exception $e) {
            Log::error("Error during processImageGeneration: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred while generating the image.'], 500);
        }
    }

    public function postPrompt($url_img, $url_lora, $alias_name, $alias_id, $strength_model = 1, $denoise = 0.35)
    {
        Log::info('POST PROMPT');
        $filePath = storage_path('app/app.json');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Configuration file not found'], 404);
        }

        $jsonContent = file_get_contents($filePath);
        Log::info('Json: ' . $jsonContent);
        $data = json_decode($jsonContent, true);


        # có thể lấy tên hay đường dẫn của lora

        #seed cần random
        $seed = rand(100000, 999999);
        $data['435']['inputs']['seed'] = $seed;
        $data['970']['inputs']['denoise'] = $denoise;
        $data['1039']['inputs']['Url'] = $url_img;
        $data['1037']['inputs']['url'] = $url_lora;
        $data['1037']['inputs']['lora_name'] = $alias_name;
        $data['1037']['inputs']['strength_model'] = $strength_model;
        $data['1038']['inputs']['url'] = env('APP_URL') . 'api/gpu-webhook';
        $data['1038']['inputs']['id'] = $alias_id;
        Log::info('webhook: ' . env('APP_URL') . 'api/gpu-webhook');
        $response = Http::post('http://103.20.97.116:9000/prompt', ['prompt' => $data]);

        if ($response->successful()) {
            $json = $response->json();
            return response()->json(['prompt_id' => $json['prompt_id']]);
        }

        return response()->json(['error' => $response->body()], $response->status());
    }

    public function getQueue($prompt_id)
    {
        $response = Http::get('http://103.20.97.116:9000/queue');

        if ($response->successful()) {
            $json = $response->json();
            $queueRunning = $json['queue_running'] ?? [];
            $queuePending = $json['queue_pending'] ?? [];


            #kiểm tra xem task có đang chạy hay không
            foreach ($queueRunning as $queue) {
                if ($queue[1] === $prompt_id) {
                    return response()->json(['status' => 'queue_running']);
                }
            }

            #kiểm tra xem task có đang chờ hay không

            foreach ($queuePending as $queue) {

                if ($queue[1] === $prompt_id) {
                    return response()->json(['status' => 'queue_pending']);
                }
            }

            #tra về done

            return response()->json(['status' => 'done'], 404);
        }

        return response()->json(['error' => $response->body()], $response->status());
    }

    public function getHistory($prompt_id)
    {
        $response = Http::get("http://103.20.97.116:9000/history/" . $prompt_id);

        if ($response->successful()) {

            #tra về filename, subfolder, type

            return response()->json($response->json());
        }

        return response()->json(['error' => $response->body()], $response->status());
    }

    private function processImageGeneration(Request $request)
    {
        Log::info('Bắt đầu quá trình tạo ảnh AI.');
        $user = auth('web')->user();
        $myAIImage = MyAIImage::find($request->model_id);

        $validator = Validator::make($request->all(), [
            'aspect_ratio' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error('Validation thất bại.', $validator->errors()->toArray());
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Validation thành công.');

        $imageUrl = null;
        $content = '';

        if ($request->hasFile('avatar_file')) {
            $avatarFile = $request->file('avatar_file');
            Log::info('Ảnh được tải lên: ' . $avatarFile->getClientOriginalName());
            $avatarPath = $avatarFile->store('avatars', 'public');
            $imageUrl = Storage::disk('public')->path($avatarPath);
            Log::info('Ảnh đã được lưu tại: ' . $avatarPath);
            $filename = 'images/' . uniqid('image_', true) . '.png';
            Storage::disk('s3')->put($filename, file_get_contents($imageUrl));
            $imageUrl = Helpers::preSignedS3Url($filename);
        } elseif ($request->input('avatar_url')) {
            $imageUrl = $request->input('avatar_url');
            Log::info('Sử dụng URL ảnh: ' . $imageUrl);
        }

        if ($request->input('image_description')) {
            $content = $this->chatGPTService->getResponse('Dịch văn bản sau sang tiếng Anh: ' . $request->input('image_description'));
            Log::info('Dịch mô tả nội dung tạo ảnh sang tiếng Anh: '.$content);
        }
        else {
            Log::warning('Không có nội dung mô tả hình ảnh.');
            return response()->json([
                'message' => 'nội dung mô tả hình ảnh là bắt buộc.',
            ], 422);
        }

        if ($request->input('is_use_mask') == true) {
            $maskUrl = $this->generateMaskUrl($imageUrl);
            if (!$maskUrl) {
                return response()->json([
                    'message' => 'Xác định khuôn mặt không thành công. Xin vui lòng thử lại hoặc tải ảnh khác rõ mặt .',
                ], 400);
            }
        }

        if (empty($content)) {
            Log::warning('Không thể trích xuất nội dung từ nguồn đầu vào.');
            return response()->json(['message' => 'Không thể trích xuất nội dung từ ảnh hoặc mô tả.'], 422);
        }

        Log::info('---> '.$content);

        $generatedImages = [];

        if (!empty($content)) {
            $inputData = [
                'prompt' => '',
                'model' => 'dev',
                'go_fast' => false,
                'lora_scale' => 1,
                'megapixels' => '1',
                'num_outputs' => 1,
                'aspect_ratio' => $request->input('aspect_ratio'),
                'output_format' => 'jpg',
                'guidance_scale' => 3,
                'output_quality' => 80,
                'prompt_strength' => $myAIImage->prompt_strength,
                'extra_lora_scale' => 1,
                'num_inference_steps' => 35,
            ];

            $inputData['prompt'] = $myAIImage->use_keyword == 1 ?
                'a photo of ' . $myAIImage->trigger_word . ': ' . $content . '. Preserve the original lighting, background, and composition.' :
                $content . '. Preserve the original lighting, background, and composition.';
            Log::info('Dữ liệu đầu vào gửi tới API Replicate (không dùng mask): ', $inputData);

            $outputWithoutMask = $this->callReplicateAPI($inputData, $myAIImage->model_enpoint);
            if (isset($outputWithoutMask['output'])) {
                Log::info('API Replicate trả về thành công (không dùng mask).');
                $generatedImages[] = $this->processGeneratedImage($outputWithoutMask['output'][0], $user, $content);
            } else {
                Log::error('API Replicate trả về lỗi (không dùng mask): ', $outputWithoutMask);
            }

            if (!empty($maskUrl)) {
                $inputData['mask'] = $maskUrl;
                $inputData['image'] = $imageUrl;
                Log::info('Dữ liệu đầu vào gửi tới API Replicate (dùng mask): ', $inputData);

                $outputWithMask = $this->callReplicateAPI($inputData, $myAIImage->model_enpoint);
                if (isset($outputWithMask['output'])) {
                    Log::info('API Replicate trả về thành công (dùng mask).');
                    $generatedImages[] = $this->processGeneratedImage($outputWithMask['output'][0], $user, $content);
                } else {
                    Log::error('API Replicate trả về lỗi (dùng mask): ', $outputWithMask);
                }
            }
            if (count($generatedImages) === 2) {
                return $this->generateFaceSwapWithUrls($generatedImages[0]->s3_url, $generatedImages[1]->s3_url);
            }
            Auth::user()->chargeCredit('flux-schnell', null, null, null, null, 2, 2);
            return response()->json([
                'message' => 'Quá trình tạo ảnh hoàn tất.',
                'generated_images' => $generatedImages[0],
                'credits' => $user->credit ?? 0,
            ], 200);
        } else {
            Log::warning('Không thể trích xuất văn bản từ ảnh.');
            return response()->json([
                'message' => 'Không thể trích xuất văn bản từ ảnh.',
            ], 422);
        }
    }

    private function generateFaceSwapWithUrls($sourceUrl, $targetUrl)
    {
        $user = auth('web')->user();
        $sourceBase64 = base64_encode(file_get_contents($sourceUrl));
        $targetBase64 = base64_encode(file_get_contents($targetUrl));

        $requestData = [
            'source_img' => $sourceBase64,
            'target_img' => $targetBase64,
            'input_faces_index' => 0,
            'source_faces_index' => 0,
            'face_restore' => 'codeformer-v0.1.0.pth',
            'base64' => true,
        ];

        $response = Http::timeout(600)->withHeaders([
            'x-api-key' => config('segmind.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.segmind.com/v1/faceswap-v2', $requestData);

        if ($response->successful()) {
            $responseData = $response->json();
            $imageContent = base64_decode($responseData['image']);
            $resultPath = 'swapfaceimg/results/' . uniqid('result_', true) . '.png';

            Storage::disk('s3')->put($resultPath, $imageContent);
            $resultS3Url = Storage::disk('s3')->url($resultPath);

            $data = [
                'user_id' => $user->id,
                'description' => '',
                'style' => 'Tự động',
                'width' => 1024,
                'height' => 1024,
                's3_url' => $resultPath,
                'type' => 'image',
                'model' => 'my_ai_image'
            ];

            $record = $this->aIMediaService->storeMedia($data);

            return response()->json([
                'message' => 'Quá trình tạo ảnh hoàn tất.',
                'generated_images' => $record,
                'credits' => $user->credit ?? 0,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể thực hiện đổi khuôn mặt.',
        ], 500);
    }

    public function apiHistoryMyAIImage() {
        try {
            $list = $this->aIMediaService->getMyAIImageHistories();
            return response()->json(['data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processGeneratedImage($imageUrl, $user, $content)
    {
        $imageData = file_get_contents($imageUrl);
        $filename = 'images/' . uniqid('image_', true) . '.webp';
        Storage::disk('s3')->put($filename, $imageData);

        $s3Url = Storage::disk('s3')->url($filename);
        Log::info('S3 URL của ảnh được tạo: ' . $s3Url);

        $data = [
            'user_id' => $user->id,
            'description' => $content,
            'style' => 'Tự động',
            'width' => 1024,
            'height' => 1024,
            's3_url' => $filename,
            'type' => 'image',
            'model' => 'my_ai_image'
        ];

        $record = $this->aIMediaService->storeMedia($data);
        return $record;
    }

    private function generateMaskUrl($imageUrl)
    {
        $try = 1;
        $attempts = 3;

        while ($try <= $attempts) {
            try {
                $maskPredictionId = $this->createMaskPrediction($imageUrl);
                $maskUrl = $this->checkMaskStatus($maskPredictionId);
                if ($maskUrl) {
                    return $maskUrl;
                }
                $try++;
                Log::info('Thử tạo lại mask mới lần' . $try . '.');
            } catch (\Exception $e) {
                Log::error('Lỗi khi tạo mask: ' . $e->getMessage());
            }
        }

        return null;
    }

    private function createMaskPrediction($imageUrl)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
            'Content-Type' => 'application/json',
            'Prefer' => 'wait'
        ])
            ->timeout(61)
            ->post('https://api.replicate.com/v1/predictions', [
            'version' => 'b52b4833a810a8b8d835d6339b72536d63590918b185588be2def78a89e7ca7b',
            'input' => [
                'images' => $imageUrl,
                'blur_amount' => 1,
            ]
        ]);

        $prediction = $response->json();

        if (isset($prediction['id'])) {
            return $prediction['id'];
        }

        Log::error('Tạo mask không thành công, không nhận được ID prediction.');
        return null;
    }

    private function checkMaskStatus($predictionId)
    {
        if ($predictionId === null) {
            return null;
        }

        $maskUrl = null;
        $attempts = 3;
        $timeout = 20;
        $try = 1;

        while ($try <= $attempts) {
            $response = Http::timeout($timeout)->withHeaders([
                'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
            ])->get("https://api.replicate.com/v1/predictions/{$predictionId}");

            $prediction = $response->json();

            if (isset($prediction['status']) && $prediction['status'] === 'succeeded') {
                $maskUrl = $prediction['output'][0];
                break;
            }

            if (isset($prediction['status']) && $prediction['status'] === 'failed') {
                Log::error('Tạo mask không thành công, status = failed.');
                break;
            }

            sleep(5);
            $try++;
        }

        return $maskUrl;
    }

    private function callReplicateAPI($inputData, $version)
    {
        Log::info('Bắt đầu gọi API Replicate.');

        $url = "https://api.replicate.com/v1/predictions";

        Log::info('URL của API: ' . $url);

        $apiToken = env('REPLICATE_API_TOKEN');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json',
            'Prefer: wait'
        ]);

        $payload = json_encode([
            'version' => $version, // Version nhận từ tham số
            'input' => $inputData, // Dữ liệu đầu vào
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        Log::info('Dữ liệu gửi tới API Replicate: ', $inputData);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            Log::error('Lỗi khi gọi API Replicate: ' . $error);
            curl_close($ch);
            return response()->json(['message' => 'Có lỗi xảy ra khi gọi API Replicate', 'error' => $error], 500);
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        Log::info('Phản hồi từ API Replicate: ', $responseData);

        if (isset($responseData['error'])) {
            Log::error('API Replicate trả về lỗi: ', $responseData['error']);
            return response()->json(['message' => 'API Replicate trả về lỗi', 'error' => $responseData['error']], 500);
        }

        return $responseData;
    }

    public function historyMyAIImage()
    {
        try {
            $list = $this->aIMediaService->getMyAIImageHistories();
            return Inertia::render('Client/AIImage/HistoryMyAIImage', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showTrainModel() {
        return Inertia::render('Client/AIImage/FineTune/Index');
    }

    public function hasMyAIImage()
    {
        $user = auth('web')->user();
        $hasImage = MyAIImage::where('user_id', $user->id)->exists();
        return response()->json(['exists' => $hasImage]);
    }

    public function fineTune(Request $request) {
        try {
            $user = auth('web')->user();

            $hasFineTuning = MyAIImage::where('user_id', $user->id)
            ->where('status', '<>', 'failed')
            ->where('status', '<>', 'succeeded')
            ->exists();
            if($hasFineTuning) {
                return response()->json(['error' => 'Đang đào tạo mô hình vui lòng thử lại sau.'], status: 403);
            }

            if (!$request->hasFile('zip')) {
                return response()->json(['error' => 'File not uploaded'], 400);
            }

            $zipFile = $request->file('zip');
            $zipPath = $zipFile->getPathname();

            $uploadResponse = $this->uploadToReplicate($zipPath);

            if (!$uploadResponse || !isset($uploadResponse['urls']['get'])) {
                return response()->json(['error' => 'Failed to upload ZIP file'], 413);
            }

            Log::error('Upload to Replicate failed', ['response' => $uploadResponse]);
            $startTime = now();
            $trainingDataUrl = $uploadResponse['urls']['get'];
            $modelName = 'app_'.$user->id.'_'.uniqid();

            $modelResponse = $this->createModel($user->name, $modelName);

            if (!$modelResponse || !isset($modelResponse['url'], $modelResponse['owner'], $modelResponse['name'])) {
                return response()->json(['error' => 'Failed to create model'], 500);
            }

            $modelId = $modelResponse['owner'] . '/' . $modelResponse['name'];

            $triggerWord = $this->validateModelName($user->name);
            $fineTuneResponse = $this->fineTuneModel($modelId, $trainingDataUrl, $triggerWord);

            $myAiImage = MyAIImage::create(
                [
                    'user_id' => $user->id,
                    'alias_name' => $this->validateModelName($request->modelName),
                    'replicate_id' => $fineTuneResponse['id'],
                    'model_name' => 'app_'.$user->id.'_'.uniqid(),
                    'trigger_word' => $triggerWord,
                    'model_enpoint' => $fineTuneResponse['version'],
                    'status' => $fineTuneResponse['status'],
                    'start_time' => $startTime,
                ]
            );

            // CheckFinetuneStatus::dispatch($fineTuneResponse['id'], $fineTuneResponse['urls']['get']);

            $user->my_ai_image_id = $myAiImage->id;
            $user->save();

            Auth::user()->chargeCredit('my_ai_image', null, 1, null, null, 0, 0);

            return response()->json([
                'message' => 'Mô hình AI của bạn đang được huấn luyện, sẽ tốn một ít thời gian bạn vui lòng quay lại sau.',
                'model_id' => $modelId,
                'fine_tune_response' => $fineTuneResponse,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error finetune model: '.$e->getMessage());
            return response()->json(['error' => 'Failed to upload ZIP file'], 500);
        }
    }

    private function uploadToReplicate($zipPath) {
        $apiToken = env('REPLICATE_API_TOKEN');
        $url = "https://api.replicate.com/v1/files";

        $response = Http::withToken($apiToken)
            ->attach('content', file_get_contents($zipPath), 'data.zip')
            ->post($url);

        return $response->json();
    }

    private function createModel($username, $modelName) {
        $apiToken = env('REPLICATE_API_TOKEN');
        $url = "https://api.replicate.com/v1/models";

        $response = Http::withToken($apiToken)->post($url, [
            'owner' => env('REPLICATE_OWNER'),
            'name' => $modelName,
            'description' => $username.' fine-tuned model '.uniqid(),
            'visibility' => 'private',
            'hardware' => 'gpu-a100-large',
        ]);

        return $response->json();
    }

    private function fineTuneModel($modelId, $trainingDataUrl, $triggerWord) {
        $apiToken = env('REPLICATE_API_TOKEN');

        $url = "https://api.replicate.com/v1/models/ostris/flux-dev-lora-trainer/versions/d995297071a44dcb72244e6c19462111649ec86a9646c32df56daa7f14801944/trainings";

        $payload = [
            'destination' => $modelId,
            'input' => [
                'input_images' => $trainingDataUrl,
                'trigger_word' => $triggerWord,
                'steps' => 1000,
                'learning_rate' => 0.0002,
                'batch_size' => 2,
                'optimizer' => 'adamw8bit'
            ],
            'webhook' => env('APP_URL').'api/my-ai-images-webhook',
            'webhook_events_filter' => ["start", "completed"],
        ];

        $response = Http::withToken($apiToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('Fine-tune failed', [
                'url' => $url,
                'payload' => $payload,
                'response' => $response->json()
            ]);
            throw new Exception("Failed to fine-tune model: " . $response->body());
        }

        return $response->json();
    }

    private function validateModelName($modelName) {
        $modelName = strtolower($modelName);
        $modelName = preg_replace('/[^a-z0-9\-\_\.]/', '', $modelName);
        $modelName = trim($modelName, '-_.');

        if (empty($modelName)) {
            $modelName = 'default_model_name';
        }

        return $modelName.'_'. uniqid();
    }

    public function getMyAIImageTemplate(Request $request) {
        $category = $request->input('category');
        $perPage = $request->input('per_page', 12);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category is required',
            ], 400);
        }

        $templates = MyAIImageTemplate::where('category', $category)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
    public function webhookMyAIImage(Request $request) {
        try {
            $params = $request->all();

            Log::info('Webhook My AI Image: ' . json_encode($params));

            if (empty($params['id'])) {
                Log::warning('Thiếu thông tin id trong request.');
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin id.'
                ], 400);
            }

            if (!isset($params['status'])) {
                Log::warning('Thiếu thông tin status trong request.');
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin status.'
                ], 400);
            }

            $model = $params['id'];
            $status = $params['status'];
            Log::info("Tìm kiếm model với id: $model");

            $myAIModel = MyAIImage::where('replicate_id', $model)->select('*')->first();

            if (!$myAIModel) {
                Log::warning("Không tìm thấy model với id: $model");
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy model tương ứng.'
                ], 404);
            }

            Log::info("Tìm thấy model: $model. Kiểm tra trạng thái hiện tại.");

            if ($myAIModel->status !== $status) {
                Log::info("Trạng thái hiện tại của model: {$myAIModel->status}. Cập nhật thành: $status");
                $myAIModel->status = $status;

                if ($status === 'succeeded') {
                    Log::info("Trạng thái là succeeded. Cập nhật end_time.");
                    $myAIModel->end_time = now();

                    $replicateApiToken = env('REPLICATE_API_TOKEN');
                    $trainingApiUrl = "https://api.replicate.com/v1/trainings/$model";

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $trainingApiUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Authorization: Bearer $replicateApiToken",
                        "Accept: application/json"
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($response === false) {
                        $error = curl_error($ch);
                        Log::error("Lỗi khi gọi API: $error");
                    } else if ($httpCode !== 200) {
                        Log::warning("Gọi API không thành công. HTTP Code: $httpCode. Response: $response");
                    } else {
                        $trainingData = json_decode($response, true);

                        if (!empty($trainingData['output']['version'])) {
                            $fullVersion = $trainingData['output']['version'];

                            $versionParts = explode(':', $fullVersion);
                            $shortVersion = end($versionParts);

                            Log::info("Lấy được version từ API: $shortVersion");

                            $myAIModel->model_enpoint = $shortVersion;
                        } else {
                            Log::warning("Không tìm thấy version trong output.");
                        }
                    }

                    curl_close($ch);
                }

                $myAIModel->save();
                Log::info("Cập nhật trạng thái thành công cho model: $model");
            } else {
                Log::info("Trạng thái hiện tại của model đã là $status. Không cần cập nhật.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Xử lý trạng thái thành công.'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi trong webhookMyAIImage: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function hasFineTuneModel() {
        $userId = auth('web')->id();

        $hasFineTuning = MyAIImage::where('user_id', $userId)
            ->where('status', '<>', 'failed')
            ->where('status', '<>', 'succeeded')
            ->exists();

        return response()->json(['exists' => $hasFineTuning]);
    }

    public function getLatestMyAIImage()
    {
        $userId = auth('web')->id();

        $latestImage = MyAIImage::where('user_id', $userId)
            ->latest('created_at')
            ->first();

        if (!$latestImage) {
            return response()->json(['message' => 'Không tìm thấy ảnh AI gần nhất']);
        }

        return response()->json(['data' => $latestImage]);
    }

    public function getHistoryMyAIImage()
    {
        try {
            $list = $this->aIMediaService->getMyAIImageHistories();
            return response()->json(['histories' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function isMobile() {
        return preg_match('/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $_SERVER['HTTP_USER_AGENT']);
    }

    private function improveQualityImageByUrl($promptImage, $scale = 2, $faceEnhance = false) {
        $user = auth('web')->user();
        $requestData = [
            "version" => "660d922d33153019e8c263a3bba265de882e7f4f70396546b6c9c8f9d47a021a",
            "input" => [
                "image" => $promptImage,
            ]
        ];
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->replicateApiToken}",
            'Content-Type' => 'application/json',
        ])->timeout(1200)->post("https://api.replicate.com/v1/predictions", $requestData);
        if(!isset($response->json()["id"])) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình thực hiện'], 500);
        }
        $id = $response->json()["id"];
        $data = [
            'user_id' => $user->id,
            'model' => 'photo-beauty',
            'description' => "Làm đẹp ảnh",
            's3_url' => "",
            'task_id' => $id,
            's3_url_mobile' => "",
            'type' => 'image'
        ];
        $record = $this->aIMediaService->storeMedia($data);
        return $record->id;
    }

    public function getImageForFineTune($id) {
        try {
            $aiMedia = $this->aIMediaService->getOneById($id);
            if(empty($aiMedia)) {
                return ['success' => false, 'message' => 'Không tồn tại dữ liệu'];  // Trả về mảng thay vì JsonResponse
            }

            $task_id = $aiMedia->task_id;
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
            ])->timeout(1200)->get("https://api.replicate.com/v1/predictions/".$task_id);

            if ($response->successful()) {  // Kiểm tra nếu response thành công
                $responseData = $response->json();  // Giải mã JSON thành mảng
                Log::info('Response data improve quality image: '.json_encode($responseData));
                if (isset($responseData["status"]) && $responseData["status"] != "succeeded") {
                    return ['success' => false, 'message' => 'Đang trong quá trình tạo ảnh', 'status' => $responseData['status']];
                }

                $scaleSize = 1080;
                if ($this->isMobile()) {
                    $scaleSize = 720;
                }
                $filename = 'images/' . uniqid('image_', true) . '.png';
                $imgContent = file_get_contents($responseData["output"]);
                file_put_contents(storage_path('app/public/' . $filename), $imgContent);

                $filename_mobile = 'images/' . uniqid('image_', true) . '.png';
                FFmpegHelper::resizeImage(
                    storage_path('app/public/' . $filename),
                    storage_path('app/public/' . $filename_mobile),
                    $scaleSize
                );

                Storage::disk('s3')->put($filename_mobile, file_get_contents(storage_path('app/public/' . $filename_mobile)));
                $urlResize = Helpers::preSignedS3Url($filename_mobile);

                $paths[] = $filename_mobile;

                unlink(storage_path('app/public/' . $filename));
                unlink(storage_path('app/public/' . $filename_mobile));

                $aiMedia->update(["s3_url" => $filename_mobile]);
                Auth::user()->chargeCredit('photo-beauty', null, null, null, null, 24, 25);
                return ['s3_url' => $urlResize, 'path' => $filename_mobile, 'generate_file' => $aiMedia];
            } else {
                return ["success" => false, "message" => "Có lỗi xảy ra trong quá trình thực hiện"];
            }
        } catch(\Exception $ex) {
            return ["success" => false, "message" => "Có lỗi xảy ra trong quá trình thực hiện"];
        }
    }


    public function improveQualityImage(Request $request) {
        $imageUrl = $request->imageUrl;
        $taskId = $this->improveQualityImageByUrl($imageUrl, 0.7);
        $result = $this->getImageForFineTune($taskId);
        Log::info('Kết quả gọi làm đẹp ảnh: '.json_encode($result));

        $fineTuneAttempts = 0;
        $maxFineTuneAttempts = 20;

        while (isset($result['status']) && $result['status'] != "succeeded" && $fineTuneAttempts < $maxFineTuneAttempts) {
            $fineTuneAttempts++;
            Log::info("Attempt $fineTuneAttempts: Status is '{$result['status']}'");
            sleep(2);
            $result = $this->getImageForFineTune($taskId);
        }

        return response()->json($result, 200);
    }

    public function getImageDescription(Request $request) {
        if ($request->hasFile('avatar_file')) {
            $avatarFile = $request->file('avatar_file');
            Log::info('Ảnh được tải lên: ' . $avatarFile->getClientOriginalName());
            $avatarPath = $avatarFile->store('avatars', 'public');
            $imageUrl = Storage::disk('public')->path($avatarPath);
            Log::info('Ảnh đã được lưu tại: ' . $avatarPath);
            $filename = 'images/' . uniqid('image_', true) . '.png';
            Storage::disk('s3')->put($filename, file_get_contents($imageUrl));
            $imageUrl = Helpers::preSignedS3Url($filename);
        } elseif ($request->input('avatar_url')) {
            $imageUrl = $request->input('avatar_url');
            Log::info('Sử dụng URL ảnh: ' . $imageUrl);
        } else {
            Log::warning('Không có tệp avatar hoặc avatar_url.');
            return response()->json([
                'message' => 'avatar_file hoặc avatar_url là bắt buộc.',
            ], 422);
        }

        // Gửi yêu cầu cải thiện prompt đến o3-mini
        $content = $this->imageReaderService->extractTextEnglish($imageUrl);
        // $improvePromptRequest = "Refine the following prompt to:
        // - Enhance clarity and detail in the image description instructions.
        // - Ensure precise guidance for AI to focus on key aspects: colors, lighting, composition, main subjects, actions, and background.
        // - Improve AI ability to capture fine details, including facial expressions, postures, textures, and small objects.
        // - **IMPORTANT: Return only the improved prompt without any extra explanations.**
        // - **Maintain all original data while optimizing phrasing and structure.**
        // - **Avoid generic replacements like '(with all detailed content as provided)'.**
        // - **Response must be in English.**

        // Current Prompt: $content";

        // $content = $this->chatGPTService->getResponse3v2($improvePromptRequest, 'o3-mini');
        return response()->json([
            'content' => $content,
        ], 200);
    }

    public function webhookGPUMyAIImage(Request $request)
    {
        try {
            Log::info('Webhook GPU My AI Image payload: ', $request->all());

            $validator = Validator::make($request->all(), [
                'image' => 'required|string',
                'id' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed for webhook payload.', $validator->errors()->toArray());
                return response()->json(['error' => 'Invalid payload data.', 'details' => $validator->errors()], 400);
            }

            $imageBase64 = $request->input('image');
            $aliasName = $request->input('id');

            $mediaRecord = AIGeneratedMedia::where('alias_name', $aliasName)->first();

            if (!$mediaRecord) {
                Log::error("No matching record found in ai_generated_media for alias_name: {$aliasName}");
                return response()->json(['error' => 'Invalid alias_name provided.'], 404);
            }

            $imageData = base64_decode($imageBase64);

            if (!$imageData) {
                Log::error('Failed to decode base64 image.');
                return response()->json(['error' => 'Invalid image data.'], 400);
            }

            $filename = 'images/' . uniqid('gpu_image_', true) . '.png';
            Storage::disk('s3')->put($filename, $imageData);

            $s3Url = Storage::disk('s3')->url($filename);

            $mediaRecord->update([
                's3_url' => $filename,
                'description' => 'Generated image updated via GPU webhook',
                'type' => 'image',
                'model' => 'gpu_image_generation'
            ]);
            $mediaRecord->save();
            Log::info("Image successfully updated and stored. S3 URL: {$s3Url}");

            $user = $this->userService->getUserById($mediaRecord->user_id);

            $data = [
                's3_url' => Helpers::preSignedS3Url($mediaRecord->s3_url),
                'path' => $mediaRecord->s3_url,
                'generate_file' => $mediaRecord,
                'credits' => $user->credit ?? 0
            ];

            event(new MessageAIGeneratedMediaWebhookGPUMyAIImage($data));

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully.',
                'data' => [
                    's3_url' => $s3Url,
                    'record' => $mediaRecord
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in webhookGPUMyAIImage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getImageByPromptId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt_id' => 'required|string',
            'alias_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid prompt_id provided.', 'errors' => $validator->errors()], 422);
        }

        $promptId = $request->input('prompt_id');
        $alias_name = $request->input('alias_name');
        Log::info("Fetching status for prompt ID: $promptId");

        try {
            $response = Http::get("http://103.20.97.116:9000/history/" . $promptId);
            if ($response->failed()) {
                Log::error("Failed to fetch status for prompt ID: $promptId", ['response' => $response->body()]);
                return response()->json([
                    'message' => 'Failed to fetch status from the external API.',
                    'error' => $response->body(),
                ], 500);
            }
            $statusData = $response->json();
            Log::info('GPU history response data: ' . json_encode($statusData));
            foreach ($statusData as $key => $value) {
                if (isset($value['status']['status_str']) && $value['status']['status_str'] === 'success') {
                    $mediaRecord = AIGeneratedMedia::where('alias_name', $alias_name)->first();
                    if (!$mediaRecord) {
                        Log::warning("No media record found for prompt ID: $promptId");
                        return response()->json([
                            'message' => 'No record found for the given prompt ID.',
                        ], 404);
                    }
                    Log::info("Returning data for prompt ID: $promptId", ['media' => $mediaRecord]);
                    return response()->json([
                        's3_url' => Helpers::preSignedS3Url($mediaRecord->s3_url),
                        'path' => $mediaRecord->s3_url,
                        'generate_file' => $mediaRecord,
                        'credits' => $user->credit ?? 0
                    ], 200);
                }
            }
            foreach ($statusData as $key => $value) {
                if (isset($value['status']) && in_array($value['status']['status_str'], ['queue_running', 'queue_pending'])) {
                    Log::info("Prompt ID: $promptId is still processing. Status: {$value['status']['status_str']}");
                    return response()->json([
                        'message' => 'Image generation is still in progress.',
                        'status' => $value['status']['status_str'],
                    ], 202);
                }
            }

            Log::warning("Unexpected status for prompt ID: $promptId", ['statusData' => $statusData]);
            return response()->json([
                'message' => 'Unexpected status received for the prompt.',
                'status' => 'unknown',
            ], 500);
        } catch (\Exception $e) {
            Log::error("Error fetching status for prompt ID: $promptId", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'An error occurred while fetching prompt status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showImage(Request $request) {
        $s3_url = $request["s3_url"];
        if(!empty($s3_url)) {
            $imageData = base64_encode(file_get_contents($s3_url));
            return response()->json(["success" => true, "data" => $imageData], 200);
        } else {
            return "";
        }
    }
}

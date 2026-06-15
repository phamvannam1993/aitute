<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AIMediaService;
use App\Services\PebblelyVideoService;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Helper\Helpers;
use App\Services\ChatGPTService;
use App\Helper\ImageReaderService;
use Illuminate\Support\Facades\Storage;


class AIBackgroundController extends Controller
{
    private $aIMediaService;
    private $storageService;
    private $pebblelyService;
    private $chatGPTService;
    private $imageReaderService;
    public function __construct(AIMediaService $aIMediaService, StorageService $storageService, PebblelyVideoService $pebblelyService, ChatGPTService $chatGPTService, ImageReaderService $imageReaderService)
    {
        $this->aIMediaService = $aIMediaService;
        $this->storageService = $storageService;
        $this->pebblelyService = $pebblelyService;
        $this->chatGPTService = $chatGPTService;
        $this->imageReaderService = $imageReaderService;
    }

    public function index(Request $request)
    {
        $imageUrl = $request->imageUrl;
        $history = $this->pebblelyService->getHistories();
        return Inertia::render('Client/AIBackground/Index', ['history' => $history, 'imageUrl' => $imageUrl]);
    }
    public function indexV2(Request $request)
    {
        $imageUrl = $request->imageUrl;
        $history = $this->pebblelyService->getHistories();
        return Inertia::render('Client/AIBackground/IndexV2', ['history' => $history, 'imageUrl' => $imageUrl]);
    }
    public function history()
    {
        try {
            $list = $this->pebblelyService->getHistories();

            if (request()->wantsJson()) {
                return response()->json($list);
            }
            
            return Inertia::render('Client/AIBackground/History', ['list' => $list]);
        } catch (\Exception $e) {
            Log::error('Error generating image: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apiHistory()
    {
        try {
            $list = $this->pebblelyService->getHistories();
            return response()->json(["data" => $list]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateAiBackground(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
            'file' => 'nullable|file|image|max:5120',
            'imageUrl' => 'required_without:file',
            'isTransparentImage' => 'nullable',
        ]);

        try {
            $theme = $request->input('theme');
            $imageBase64 = null;
            $inputFileResult = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $inputFileResult = $this->storageService->putToS3($file, 'backgrounds-input');
                $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));
            } elseif ($request->filled('imageUrl')) {
                $imageUrl = $request->input('imageUrl');
                $tempFilePath = storage_path('app/temp_' . time() . '.jpg');
                $imageContent = file_get_contents($imageUrl);
                if ($imageContent === false) {
                    return response()->json([
                        'message' => 'Failed to fetch the image from the provided URL.',
                    ], 400);
                }
                file_put_contents($tempFilePath, $imageContent);
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempFilePath,
                    uniqid().'_image.png',
                    mime_content_type($tempFilePath),
                    null,
                    true
                );
                $inputFileResult = $this->storageService->putToS3($uploadedFile, 'backgrounds-input');
                $imageBase64 = base64_encode($imageContent);
                unlink($tempFilePath);
            }

            $isTransparent = $request->input('isTransparentImage', 'false');

            if ($isTransparent != 'true') {
                $removeBackgroundEndpoint = 'https://api.pebblely.com/remove-background/v1/';
                $apiKey = env('PEBBLELY_API_KEY');

                $removeBgResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Pebblely-Access-Token' => $apiKey,
                ])->post($removeBackgroundEndpoint, [
                    'image' => $imageBase64,
                ]);

                if (!$removeBgResponse->successful()) {
                    return response()->json([
                        'message' => 'Failed to remove background.',
                        'error' => $removeBgResponse->json(),
                    ], 400);
                }

                $imageBase64 = $removeBgResponse->json('data');
            }

            $createBackgroundEndpoint = 'https://api.pebblely.com/create-background/v2/';
            $apiKey = env('PEBBLELY_API_KEY');

            $createBgResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Pebblely-Access-Token' => $apiKey,
            ])->post($createBackgroundEndpoint, [
                'images' => [$imageBase64],
                'theme' => $theme,
                'transforms' => [
                    [
                        "scale_x" => 0.5,
                        "scale_y" => 0.5,
                        "x" => 0,
                        "y" => 0,
                        "angle" => 0
                    ],
                ],
            ]);

            if (!$createBgResponse->successful()) {
                return response()->json([
                    'message' => 'Failed to create background.',
                    'error' => $createBgResponse->json(),
                ], 400);
            }

            $generatedImageBase64 =$createBgResponse->json('data');

            $tempFile = storage_path('app/temp_' . time() . '.jpg');
            file_put_contents($tempFile, base64_decode($generatedImageBase64));
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'image_template.png',
                mime_content_type($tempFile),
                null,
                true
            );
            $s3Result = $this->storageService->putToS3($uploadedFile, 'backgrounds');

            unlink($tempFile);

            $data = [
                'theme' => $theme,
                'input_url' => $inputFileResult['path'],
                's3_url' => $s3Result['path']
            ];
            Log::info('Follow data ai background: '.json_encode($data));

            $record = $this->pebblelyService->store($data);
            Auth::user()->chargeCredit('pebblely', null, null, null, null, 21, 21);
            return response()->json([
                'message' => 'Background generated and uploaded successfully.',
                'path' => $s3Result['path'],
                's3_url' => $s3Result['url'],
                'id' => $record->id,
                'theme' => $theme,
                'credits' => Auth::user()->credit ?? 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function upscale($imageBase64)
    {
        $upscaleEndpoint = 'https://api.pebblely.com/upscale/v1/';
        $apiKey = env('PEBBLELY_API_KEY');
        $size = 2048;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Pebblely-Access-Token' => $apiKey,
        ])->post($upscaleEndpoint, [
            'image' => $imageBase64,
            'size' => $size,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to upscale image: ', $response->json());
            throw new \Exception('Failed to upscale image.');
        }

        return $response->json('data');
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->pebblelyService->deleteFileById($id);
        return $response;
    }
    public function generateAiBackgroundV2WithFile(Request $request)
    {
        // Xác thực đầu vào
        $request->validate([
            'prompt_background' => 'nullable|string',
        ]);

        // Kiểm tra xem ít nhất một trong 'prompt_background' hoặc 'backgroundImageFile' được cung cấp
        if (!$request->has('prompt_background') && !$request->hasFile('backgroundImageFile') && !$request->has('backgroundImageUrl')) {
            return response()->json([
                'message' => 'Bạn phải cung cấp mô tả hình nên hoặc chọn lựa chọn bối cảnh.',
            ], 400);
        }

        try {
            // Lấy giá trị từ request
            $prompt = $request->input('prompt_background');
            $imageFile = $request->file('imageFile');
            $imageUrl = $request->input('imageUrl');

            // Xử lý ảnh chính tải lên (nếu có)
            
            $imageFilePath = null;
            if (isset($imageFile)) {
                // Nếu có file, xử lý upload
                $imageFilePath = 'ai-background/' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                Storage::disk('s3')->put($imageFilePath, file_get_contents($imageFile));
                $imageUrl = Helpers::preSignedS3Url($imageFilePath);
            } elseif (isset($imageUrl)) {
                // Tải ảnh từ URL về server
                $imageContent = Http::get($imageUrl)->body();
                
                // Nếu có imageUrl, kiểm tra URL hợp lệ
                if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    return response()->json([
                        'message' => 'Đường dẫn ảnh không hợp lệ.',
                    ], 400);
                }
                // Lấy phần mở rộng của file từ URL
                $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $imageFilePath = 'ai-background/' . uniqid() . '.' . $extension;

                // Đẩy ảnh lên S3
                Storage::disk('s3')->put($imageFilePath, $imageContent);
                $imageUrl = Helpers::preSignedS3Url($imageFilePath);
                // $imageFilePath = $imageUrl;
            } else {
                return response()->json([
                    'message' => 'Bạn phải cung cấp ảnh.',
                ], 400);
            }
            $apiData = [
                'referenceBox' => 'originalImage',
                'imageUrl' => $imageUrl,
            ];
            // $imageSize = getimagesize($imageFile);
            // if (!$imageSize) {
            //     return response()->json([
            //         'message' => 'Không thể xác định kích thước của ảnh.',
            //     ], 400);
            // }

            // $width = $imageSize[0];
            // $height = $imageSize[1];

            // if ($width <= 512 && $height <= 512) {
            //     $apiData['upscale.mode'] = 'ai.slow';
            // } elseif ($width <= 1000 && $height <= 1000) {
            //     $apiData['upscale.mode'] = 'ai.fast';
            // }
            //case for prompt
            if($prompt){
              $content = $this->imageReaderService->promptWithImage($imageUrl,
              'Đây là prompt của người dùng muốn tạo phối cảnh cho hình tôi gửi lên cho bạn:' . $prompt.
              'Hãy kết prompt này với hình tôi gửi lên cho bạn để tạo prompt mới cho. Follow theo ý này:
                •	Chủ đề chính: Mô tả đối tượng hoặc cảnh bạn muốn tạo.
                •	Môi trường hoặc bối cảnh: Xác định nơi diễn ra cảnh, ví dụ như “trên bãi biển nhiệt đới” hoặc “trong một quán cà phê ấm cúng”.
                •	Thời gian và ánh sáng: Chỉ định thời điểm trong ngày và điều kiện ánh sáng, chẳng hạn như “vào buổi sáng sớm với ánh sáng mềm mại” hoặc “dưới ánh đèn neon vào ban đêm”.
                •	Phong cách nghệ thuật (nếu có): Nếu bạn muốn hình ảnh theo một phong cách cụ thể, hãy đề cập, ví dụ như “theo phong cách tranh sơn dầu” hoặc “phong cách hoạt hình”.
                Kết quả trả về là tiếng anh và chỉ trả về nội dung của prompt mới');
              $apiData['background.prompt'] = $content;
            }
            else{
              $backgroundImageUrl = $request->input('backgroundImageUrl') ?? null;
              if (!$backgroundImageUrl) {
                  $backgroundImageFile = $request->file('backgroundImageFile');
                  $backgroundFilePath = 'ai-background/' . uniqid() . '.' . $backgroundImageFile->getClientOriginalExtension();
                  Storage::disk('s3')->put($backgroundFilePath, file_get_contents($backgroundImageFile));
                  $backgroundImageUrl = Helpers::preSignedS3Url($backgroundFilePath);
              }
              $apiData['background.guidance.imageUrl'] = $backgroundImageUrl;
              $apiData['background.guidance.scale'] = 1;
            }
            $apiData['outputSize'] = '1080x608';
            Log::info('Follow data ai background: '.json_encode($apiData));
            // Gửi yêu cầu đến API
            $response = Http::withHeaders([
                'x-api-key' => env('PHOTOROOM_API_KEY'),
            ])
            ->asMultipart()
            ->timeout(300)
            ->post('https://image-api.photoroom.com/v2/edit', $apiData);

            // Kiểm tra phản hồi từ API
            if ($response->failed()) {
                return response()->json([
                    'message' => 'Lỗi khi gọi API Photoroom.',
                    'error' => $response->body(),
                ], $response->status());
            }
            $outputFilePath = 'ai-background/' . uniqid() . '.png';
            Storage::disk('s3')->put($outputFilePath, $response->body());
            $outputImageUrl = Helpers::preSignedS3Url($outputFilePath);
            // Lưu thông tin vào cơ sở dữ liệu và trừ điểm tín dụng của người dùng
            if (!$outputImageUrl) {
                return response()->json([
                    'message' => 'Phản hồi API không chứa đường dẫn ảnh kết quả.',
                ], 500);
            }
            // Lưu thông tin vào cơ sở dữ liệu và trừ điểm tín dụng của người dùng
            $record = $this->pebblelyService->store([
                'theme' => '',
                'input_url' => $imageFilePath,
                's3_url' => $outputFilePath,
            ]);

            Auth::user()->chargeCredit('pebblely', null, null, null, null, 21, 21);

            // Trả về phản hồi cho người dùng
            return response()->json([
                // 'message' => 'Tạo nền AI thành công.',
                // 'output_url' => $outputImageUrl,
                // 'id' => $record->id,
                // 'credits' => Auth::user()->credit ?? 0,
                'generate_file' => [
                    's3_url' => $outputImageUrl,
                ],
                'message' => 'Background generated and uploaded successfully.',
                's3_url' => $outputImageUrl,
                'id' => $record->id,
                'theme' => '',
                'credits' => Auth::user()->credit ?? 0,
            ]);
        } catch (\Exception $e) {
            // Xử lý ngoại lệ và trả về lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateAiBackgroundV2(Request $request)
    {
        // Xác thực đầu vào
        $request->validate([
            'prompt_background' => 'nullable|string',
        ]);

        // Kiểm tra xem ít nhất một trong 'prompt_background' hoặc 'backgroundImageFile' được cung cấp
        if (!$request->has('prompt_background') && !$request->hasFile('backgroundImageFile') && !$request->has('backgroundImageUrl')) {
            return response()->json([
                'message' => 'Bạn phải cung cấp mô tả hình nên hoặc chọn lựa chọn bối cảnh.',
            ], 400);
        }

        try {
            // Lấy giá trị từ request
            $prompt = $request->input('prompt_background');
            $imageFile = $request->file('imageFile');
            $imageUrl = $request->input('imageUrl');

            // Xử lý ảnh chính tải lên (nếu có)
            
            $imageFilePath = null;
            if (isset($imageFile)) {
                // Nếu có file, xử lý upload
                $imageFilePath = 'ai-background/' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                Storage::disk('s3')->put($imageFilePath, file_get_contents($imageFile));
                $imageUrl = Helpers::preSignedS3Url($imageFilePath);
            } elseif (isset($imageUrl)) {
                // Tải ảnh từ URL về server
                $imageContent = Http::get($imageUrl)->body();
                
                // Nếu có imageUrl, kiểm tra URL hợp lệ
                if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    return response()->json([
                        'message' => 'Đường dẫn ảnh không hợp lệ.',
                    ], 400);
                }
                // Lấy phần mở rộng của file từ URL
                $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $imageFilePath = 'ai-background/' . uniqid() . '.' . $extension;

                // Đẩy ảnh lên S3
                Storage::disk('s3')->put($imageFilePath, $imageContent);
                $imageUrl = Helpers::preSignedS3Url($imageFilePath);
                // $imageFilePath = $imageUrl;
            } else {
                return response()->json([
                    'message' => 'Bạn phải cung cấp ảnh.',
                ], 400);
            }
            $apiData = [
                'referenceBox' => 'originalImage',
                'imageUrl' => $imageUrl,
            ];
            // $imageSize = getimagesize($imageFile);
            // if (!$imageSize) {
            //     return response()->json([
            //         'message' => 'Không thể xác định kích thước của ảnh.',
            //     ], 400);
            // }

            // $width = $imageSize[0];
            // $height = $imageSize[1];

            // if ($width <= 512 && $height <= 512) {
            //     $apiData['upscale.mode'] = 'ai.slow';
            // } elseif ($width <= 1000 && $height <= 1000) {
            //     $apiData['upscale.mode'] = 'ai.fast';
            // }
            //case for prompt
            if($prompt){
              $content = $this->imageReaderService->promptWithImage($imageUrl,
              'Đây là prompt của người dùng muốn tạo phối cảnh cho hình tôi gửi lên cho bạn:' . $prompt.
              'Hãy kết prompt này với hình tôi gửi lên cho bạn để tạo prompt mới cho. Follow theo ý này:
                •	Chủ đề chính: Mô tả đối tượng hoặc cảnh bạn muốn tạo.
                •	Môi trường hoặc bối cảnh: Xác định nơi diễn ra cảnh, ví dụ như “trên bãi biển nhiệt đới” hoặc “trong một quán cà phê ấm cúng”.
                •	Thời gian và ánh sáng: Chỉ định thời điểm trong ngày và điều kiện ánh sáng, chẳng hạn như “vào buổi sáng sớm với ánh sáng mềm mại” hoặc “dưới ánh đèn neon vào ban đêm”.
                •	Phong cách nghệ thuật (nếu có): Nếu bạn muốn hình ảnh theo một phong cách cụ thể, hãy đề cập, ví dụ như “theo phong cách tranh sơn dầu” hoặc “phong cách hoạt hình”.
                Kết quả trả về là tiếng anh và chỉ trả về nội dung của prompt mới');
              $apiData['background.prompt'] = $content;
            }
            else{
              $backgroundImageUrl = $request->input('backgroundImageUrl') ?? null;
              if (!$backgroundImageUrl) {
                  $backgroundImageFile = $request->file('backgroundImageFile');
                  $backgroundFilePath = 'ai-background/' . uniqid() . '.' . $backgroundImageFile->getClientOriginalExtension();
                  Storage::disk('s3')->put($backgroundFilePath, file_get_contents($backgroundImageFile));
                  $backgroundImageUrl = Helpers::preSignedS3Url($backgroundFilePath);
              }
              $apiData['background.guidance.imageUrl'] = $backgroundImageUrl;
              $apiData['background.guidance.scale'] = 1;
            }
            Log::info('Follow data ai background: '.json_encode($apiData));
            // Gửi yêu cầu đến API
            $response = Http::withHeaders([
                'x-api-key' => env('PHOTOROOM_API_KEY'),
            ])
            ->asMultipart()
            ->timeout(300)
            ->post('https://image-api.photoroom.com/v2/edit', $apiData);

            // Kiểm tra phản hồi từ API
            if ($response->failed()) {
                return response()->json([
                    'message' => 'Lỗi khi gọi API Photoroom.',
                    'error' => $response->body(),
                ], $response->status());
            }
            $outputFilePath = 'ai-background/' . uniqid() . '.png';
            Storage::disk('s3')->put($outputFilePath, $response->body());
            $outputImageUrl = Helpers::preSignedS3Url($outputFilePath);
            // Lưu thông tin vào cơ sở dữ liệu và trừ điểm tín dụng của người dùng
            if (!$outputImageUrl) {
                return response()->json([
                    'message' => 'Phản hồi API không chứa đường dẫn ảnh kết quả.',
                ], 500);
            }
            // Lưu thông tin vào cơ sở dữ liệu và trừ điểm tín dụng của người dùng
            $record = $this->pebblelyService->store([
                'theme' => '',
                'input_url' => $imageFilePath,
                's3_url' => $outputFilePath,
            ]);

            Auth::user()->chargeCredit('pebblely', null, null, null, null, 21, 21);

            // Trả về phản hồi cho người dùng
            return response()->json([
                // 'message' => 'Tạo nền AI thành công.',
                // 'output_url' => $outputImageUrl,
                // 'id' => $record->id,
                // 'credits' => Auth::user()->credit ?? 0,
                'generate_file' => [
                    's3_url' => $outputImageUrl,
                ],
                'message' => 'Background generated and uploaded successfully.',
                's3_url' => $outputImageUrl,
                'id' => $record->id,
                'theme' => '',
                'credits' => Auth::user()->credit ?? 0,
            ]);
        } catch (\Exception $e) {
            // Xử lý ngoại lệ và trả về lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

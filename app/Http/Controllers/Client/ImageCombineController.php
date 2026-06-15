<?php

namespace App\Http\Controllers\Client;

use App\Helper\ImageReaderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helpers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\AIMediaService;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Services\ChatGPTService;

class ImageCombineController extends Controller
{
    private $aIMediaService;
    private $imageReaderService;
    private $chatGPTService;
    public function __construct(AIMediaService $aIMediaService, ImageReaderService $imageReaderService, ChatGPTService $chatGPTService)
    {
        $this->aIMediaService = $aIMediaService;
        $this->imageReaderService = $imageReaderService;
        $this->chatGPTService = $chatGPTService;
    }
    public function combineImages(Request $request)
    {
        $request->validate([
            'image_model' => 'required|max:10240',
            'image_product' => 'required|max:10240',
        ]);
        $user = Auth::user();
        $mode = request('mode');

        // Lưu ảnh model lên S3
        $imageModelFile = $request->file('image_model');
        $imageModelPath = 'uploaded_images/' . uniqid() . '.' . $imageModelFile->getClientOriginalExtension();
        Storage::disk('s3')->put($imageModelPath, file_get_contents($imageModelFile));
        $imageModelS3Url = Storage::disk('s3')->url($imageModelPath);
        $imageModelUrl = Helpers::preSignedS3Url($imageModelPath);

        // Lưu ảnh sản phẩm lên S3
        $imageProductFile = $request->file('image_product');
        $imageProductPath = 'uploaded_images/' . uniqid() . '.' . $imageProductFile->getClientOriginalExtension();
        Storage::disk('s3')->put($imageProductPath, file_get_contents($imageProductFile));
        $imageProductS3Url = Storage::disk('s3')->url($imageProductPath);
        $imageProductUrl = Helpers::preSignedS3Url($imageProductPath);

        $ratio = $request->input("ratio", "16:9");
        $defaultPrompt = "Tạo một bức ảnh với một người mẫu đang cầm một sản phẩm, đặt sản phẩm lên tay người mẫu.";
        $prompt_kh = $request->input("prompt_kh") ? $request->input("prompt_kh") : $defaultPrompt;

        $prompt_kh .= match ($ratio) {
            '1:1' => ' Khung ảnh vuông',
            '9:16' => ' Khung ảnh dọc',
            default => ' Khung ảnh ngang',
        };

        $translatedPrompt = $this->chatGPTService->getResponse("Translate the following text to English: \"$prompt_kh\", just give me the text in English, DO NOT explain ANYELSE.", null, 'gpt-4o-mini', $user);
        $replicateToken = env("REPLICATE_API_KONTEXT");
        $apiEndpoint = "https://api.replicate.com/v1/models/flux-kontext-apps/multi-image-kontext-pro/predictions";

        try {
            $initResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $replicateToken,
                'Content-Type' => 'application/json',
            ])->post($apiEndpoint, [
                "input" => [
                    "prompt" => $translatedPrompt,
                    "aspect_ratio" => $ratio,
                    "input_image_1" => $imageProductUrl,
                    "input_image_2" => $imageModelUrl,
                    "safety_tolerance" => 2,
                    "output_format" => "png",
                    "prompt_upsampling" => true,
                ]
            ]);

            Log::info("Replicate init response: " . $initResponse->body());

            if (!$initResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Không thể gửi yêu cầu đến Replicate']);
            }

            $initData = $initResponse->json();
            $getUrl = $initData['urls']['get'] ?? null;

            if (!$getUrl) {
                return response()->json(['success' => false, 'message' => 'Không có URL để truy vấn kết quả']);
            }

            $outputImageUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $replicateToken,
                ])->get($getUrl);

                if (!$statusResponse->successful()) break;

                $statusData = $statusResponse->json();
                Log::info("Replicate status check: " . json_encode($statusData));
                if ($statusData['status'] === 'succeeded' && !empty($statusData['output'])) {
                    $outputImageUrl = $statusData['output'];
                    break;
                }

                if ($statusData['status'] === 'failed') {
                    return response()->json(['success' => false, 'message' => 'Replicate xử lý thất bại']);
                }
            }

            if ($outputImageUrl) {
                $imageContent = file_get_contents($outputImageUrl);
                $resultPath = 'generated_images/' . uniqid() . '.png';

                // Lưu ảnh kết quả lên S3
                Storage::disk('s3')->put($resultPath, $imageContent);
                $resultS3Url = Storage::disk('s3')->url($resultPath);

                // Lưu vào database với đường dẫn cả 3 ảnh
                $aiGeneratedMedia = $this->aIMediaService->saveSwapFace(
                    $resultPath,          // Ảnh kết quả
                    $imageModelPath,      // Ảnh model gốc
                    $imageProductPath,     // Ảnh sản phẩm gốc
                    $mode
                );

                // Trừ tiền credit người dùng
                Auth::user()->chargeCredit('image_combine', null, null, null, null, 37, 37);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'result_image_url' => $resultS3Url,
                        'model_image_url' => $imageModelS3Url,
                        'product_image_url' => $imageProductS3Url,
                        'aiGeneratedMedia' => $aiGeneratedMedia,
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Không lấy được ảnh kết quả sau khi chờ']);

        } catch (\Exception $e) {
            \Log::error("Replicate error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống']);
        }
    }

    public function generatePromptImage(Request $request) {
        // Validate input
        $validator = Validator::make($request->all(), [
            'image_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            //$prompt = "Bạn là một chuyên gia trong lĩnh vực nhiếp ảnh, hãy lên ý tưởng và xây dựng bố cục chụp ảnh sản phẩm đẹp và chuyên nghiệp. Sử dụng Không gian âm (Negative space) khi cần thiết Bạn có thể đã nghe nói về kỹ thuật phối hợp này, nó được gọi là Không gian âm. Nói một cách dễ hiểu, khoảng trống âm đang để lại một phần của khung ảnh hoặc hầu hết không được lấp đầy. Đây là một trong những kiểu bố cục chụp ảnh sản phẩm mạnh mẽ nhất mà một nhiếp ảnh gia sử dụng, tạo ra sức nặng thị giác lên đối tượng và thu hút sự chú ý vào đối tượng. Vì vậy rất phổ biến trong ngành Chụp ảnh Sản phẩm và quảng cáo. Trong Chụp ảnh sản phẩm, để tạo Đồ họa thông tin hoặc để thêm biểu trưng thương hiệu và thông điệp của thương hiệu, các Công ty yêu cầu bố cục không gian âm. Họ có thể cho bạn biết trước để Cung cấp cho họ Một số Hình ảnh với không gian trống để họ có thể lấp đầy nó bởi nhà thiết kế đồ họa của họ. Sử dụng các thành phần khác nhau trong hình ảnh của bạn; trước tiên hãy xem bố cục chụp ảnh sản phẩm đã giống như bản sketch hay chưa, sau đó đa dạng hóa bằng các phương pháp bố cục, sáng tạo khác. Bạn không nên sử dụng 1 bố cục cho 1 sản phẩm nhé. Đặt chủ thể sản phẩm vào bên phải Con người mà chúng ta thích nhìn vào bên phải hơn. Ví dụ lớn nhất sẽ chứng minh là bạn đang đọc bài viết này từ trái sang phải. Đôi mắt của chúng ta dễ dàng bị thu hút vào chủ thể được đặt ở phần bên phải của Hình ảnh. Trong Chụp ảnh Sản phẩm, bạn có thể sử dụng kỹ thuật này theo mọi cách có thể. Nếu bạn đang chụp ảnh một sản phẩm theo phong cách sống, thì việc đặt nó ở bên phải sẽ rất có lợi cho bạn. Hình ảnh của bạn sẽ thu hút sự chú ý của người xem hơn, vì điều đầu tiên họ chú ý đến sẽ là SẢN PHẨM.Đây là lý do tại sao hiệu quả hơn trong các bức ảnh sản phẩm phong cách sống, vì đặt sản phẩm ở phía bên phải của hình ảnh tất cả các đối tượng hỗ trợ khác trên các khu vực tổng thể. Điều này sẽ tạo ra một ảo giác về sự hoàn chỉnh, người xem sẽ thấy rất vui mắt.Giữ đường chân trời thẳng (Horizon straight) Giữ đường chân trời của bạn thẳng là một trong những quy tắc quan trọng nhất khi set up bố cục Chụp ảnh Sản phẩm đẹp. Và nó rất dễ dàng để kiểm tra. Hầu hết thời gian, bạn sẽ gắn máy ảnh của mình trên tripod. Mặt khác, máy ảnh của bạn cũng có thể có đồng hồ đo Độ ngang, hãy kiểm tra để xem trong sách hướng dẫn nếu bạn vẫn không tìm thấy nó. Vì mục đích sáng tạo, bạn có thể phá vỡ quy tắc không san bằng đường chân trời này nhưng hãy đảm bảo rằng bạn làm điều đó có chủ ý. Bạn chỉ cần nghiêng máy ảnh của bạn đến một điểm nhất định để nó tạo ra một điểm quan sát thú vị. Cố gắng duy trì sự cân bằng trong một hình ảnh sản phẩm Để cân bằng một cách chính xác, trọng lượng thị giác nên được phân bổ đều trong hình ảnh. Bạn có thể thấy nó có các yếu tố hình ảnh phụ nằm ngoài tiêu điểm ở góc trên cùng bên phải của hình ảnh, cân bằng giữa hai cây kem. Vì vậy, bạn có thể thấy Hình ảnh trông đẹp mắt vì sự cân bằng giữa các yếu tố tiêu cự phụ khác nhau và chủ thể chính. Đôi khi set up bố cục Chụp Sản phẩm đơn giản nhất có thể làm cho hình ảnh trở nên tuyệt vời. Chỉ với Sản phẩm trong ảnh, nó có thể là Bố cục hoàn hảo hơn là sử dụng bất kỳ thứ gì khác. Đôi khi đó là một yêu cầu duy nhất mà khách hàng của bạn yêu cầu cho trang web Thương mại điện tử. Bạn phải chụp ảnh trên phông nền trắng trơn và chỉ những ảnh chụp Sản phẩm đơn lẻ mới được khách hàng đồng ý.";
            $prompt = "Dựa trên hình ảnh sản phẩm này, hãy tạo một câu prompt chi tiết để tạo background hoàn hảo cho sản phẩm. Prompt cần mô tả: (1) Môi trường phù hợp với loại sản phẩm (như spa, bãi biển, rừng nhiệt đới, không gian sang trọng), (2) Ánh sáng và màu sắc hài hòa với sản phẩm, (3) Các yếu tố bổ sung tạo cảm giác cao cấp. Viết prompt dưới dạng một câu mô tả chi tiết, có thể sử dụng ngay cho AI tạo hình ảnh mà không cần chỉnh sửa thêm.";

            $response = $this->imageReaderService->promptWithFileImageV2($request->image_url, $prompt);

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Lỗi khi xử lý ảnh với AI.'
            ], 500);
        }
    }

    public function generatePromptImageWithFile(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240', // File phải là ảnh, tối đa 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $imageFile = $request->file('image');
            //$prompt = "Bạn là một chuyên gia trong lĩnh vực nhiếp ảnh, hãy lên ý tưởng và xây dựng bố cục chụp ảnh sản phẩm đẹp và chuyên nghiệp. Sử dụng Không gian âm (Negative space) khi cần thiết Bạn có thể đã nghe nói về kỹ thuật phối hợp này, nó được gọi là Không gian âm. Nói một cách dễ hiểu, khoảng trống âm đang để lại một phần của khung ảnh hoặc hầu hết không được lấp đầy. Đây là một trong những kiểu bố cục chụp ảnh sản phẩm mạnh mẽ nhất mà một nhiếp ảnh gia sử dụng, tạo ra sức nặng thị giác lên đối tượng và thu hút sự chú ý vào đối tượng. Vì vậy rất phổ biến trong ngành Chụp ảnh Sản phẩm và quảng cáo. Trong Chụp ảnh sản phẩm, để tạo Đồ họa thông tin hoặc để thêm biểu trưng thương hiệu và thông điệp của thương hiệu, các Công ty yêu cầu bố cục không gian âm. Họ có thể cho bạn biết trước để Cung cấp cho họ Một số Hình ảnh với không gian trống để họ có thể lấp đầy nó bởi nhà thiết kế đồ họa của họ. Sử dụng các thành phần khác nhau trong hình ảnh của bạn; trước tiên hãy xem bố cục chụp ảnh sản phẩm đã giống như bản sketch hay chưa, sau đó đa dạng hóa bằng các phương pháp bố cục, sáng tạo khác. Bạn không nên sử dụng 1 bố cục cho 1 sản phẩm nhé. Đặt chủ thể sản phẩm vào bên phải Con người mà chúng ta thích nhìn vào bên phải hơn. Ví dụ lớn nhất sẽ chứng minh là bạn đang đọc bài viết này từ trái sang phải. Đôi mắt của chúng ta dễ dàng bị thu hút vào chủ thể được đặt ở phần bên phải của Hình ảnh. Trong Chụp ảnh Sản phẩm, bạn có thể sử dụng kỹ thuật này theo mọi cách có thể. Nếu bạn đang chụp ảnh một sản phẩm theo phong cách sống, thì việc đặt nó ở bên phải sẽ rất có lợi cho bạn. Hình ảnh của bạn sẽ thu hút sự chú ý của người xem hơn, vì điều đầu tiên họ chú ý đến sẽ là SẢN PHẨM.Đây là lý do tại sao hiệu quả hơn trong các bức ảnh sản phẩm phong cách sống, vì đặt sản phẩm ở phía bên phải của hình ảnh tất cả các đối tượng hỗ trợ khác trên các khu vực tổng thể. Điều này sẽ tạo ra một ảo giác về sự hoàn chỉnh, người xem sẽ thấy rất vui mắt.Giữ đường chân trời thẳng (Horizon straight) Giữ đường chân trời của bạn thẳng là một trong những quy tắc quan trọng nhất khi set up bố cục Chụp ảnh Sản phẩm đẹp. Và nó rất dễ dàng để kiểm tra. Hầu hết thời gian, bạn sẽ gắn máy ảnh của mình trên tripod. Mặt khác, máy ảnh của bạn cũng có thể có đồng hồ đo Độ ngang, hãy kiểm tra để xem trong sách hướng dẫn nếu bạn vẫn không tìm thấy nó. Vì mục đích sáng tạo, bạn có thể phá vỡ quy tắc không san bằng đường chân trời này nhưng hãy đảm bảo rằng bạn làm điều đó có chủ ý. Bạn chỉ cần nghiêng máy ảnh của bạn đến một điểm nhất định để nó tạo ra một điểm quan sát thú vị. Cố gắng duy trì sự cân bằng trong một hình ảnh sản phẩm Để cân bằng một cách chính xác, trọng lượng thị giác nên được phân bổ đều trong hình ảnh. Bạn có thể thấy nó có các yếu tố hình ảnh phụ nằm ngoài tiêu điểm ở góc trên cùng bên phải của hình ảnh, cân bằng giữa hai cây kem. Vì vậy, bạn có thể thấy Hình ảnh trông đẹp mắt vì sự cân bằng giữa các yếu tố tiêu cự phụ khác nhau và chủ thể chính. Đôi khi set up bố cục Chụp Sản phẩm đơn giản nhất có thể làm cho hình ảnh trở nên tuyệt vời. Chỉ với Sản phẩm trong ảnh, nó có thể là Bố cục hoàn hảo hơn là sử dụng bất kỳ thứ gì khác. Đôi khi đó là một yêu cầu duy nhất mà khách hàng của bạn yêu cầu cho trang web Thương mại điện tử. Bạn phải chụp ảnh trên phông nền trắng trơn và chỉ những ảnh chụp Sản phẩm đơn lẻ mới được khách hàng đồng ý.";
            $prompt = "Dựa trên hình ảnh sản phẩm này, hãy tạo một câu prompt chi tiết để tạo background hoàn hảo cho sản phẩm. Prompt cần mô tả: (1) Môi trường phù hợp với loại sản phẩm (như spa, bãi biển, rừng nhiệt đới, không gian sang trọng), (2) Ánh sáng và màu sắc hài hòa với sản phẩm, (3) Các yếu tố bổ sung tạo cảm giác cao cấp. Viết prompt dưới dạng một câu mô tả chi tiết, có thể sử dụng ngay cho AI tạo hình ảnh mà không cần chỉnh sửa thêm.";

            $response = $this->imageReaderService->promptWithFileImage($imageFile, $prompt);

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Lỗi khi xử lý ảnh với AI.'
            ], 500);
        }
    }

    public function createImage(Request $request) {
        $url = "http://103.20.97.116:7979/generate";

        $imageModelFile = $request->file('image_model');
        $imageProductFile = $request->file('image_product');

        $imageModelBase64 = base64_encode(file_get_contents($imageModelFile));
        $imageProductBase64 = base64_encode(file_get_contents($imageProductFile));
        $ratio = $request["ratio"] ? $request["ratio"] : '16:9';
        $prompt_kh = $request->prompt_kh ?  $request->prompt_kh : '';
        $prompt_kh =  $prompt_kh. $ratio == '16:9' ? 'Khung ảnh ngang' : ($ratio == '1:1' ? 'Khung ảnh vuông' : 'Khung ảnh dọc');
        $dataReq = [
            "image_model" => $imageModelBase64,
            "image_production" => $imageProductBase64,
            "prompt_kh" => $prompt_kh,
            "token" => "707e4914f9b8930f16764772e87ac450d0c7a4bec9dc7453c2488ce4a9e47c5c",
            "face_swap_enchance" => true
        ];

        try {
            $response = Http::timeout(120)->post($url, $dataReq);
            if ($response->successful()) {
                $response = $response->json();
                $images = $response["images"];
                $s3ImageUrls = [];
                foreach($images as $image) {
                    $imagePath = 'images/' . Str::uuid() . '.jpg';
                    Storage::disk('s3')->put($imagePath, base64_decode($image));
                    $s3ImageUrl = Helpers::preSignedS3Url($imagePath);
                    $s3ImageUrls[] = $s3ImageUrl;
                    $this->cleanupFiles([$imagePath]);
                }
                Auth::user()->chargeCredit('image_combine', null, null, null, null, 37, 37);
                return response()->json(["s3_url" => $s3ImageUrls[0], 'success' => true]);
            } else {
                return response()->json(["s3_url" => "", 'success' => false]);
            }
        } catch(Exception $ex) {
            return response()->json(["s3_url" => "", 'success' => false]);
        }
    }

    public function crateImageBackgroundWithReplica()
    {
        $user = Auth::user();
        $image = request("image");
        $prompt_kh = request("prompt_kh", 'Tạo một background hoàn hảo cho sản phẩm này');
        $ratio = request("ratio", "16:9");
        $output_format = 'jpg';

        $translatedPrompt = $this->chatGPTService->getResponse("Translate the following text to English: \"$prompt_kh\", just give me the text in English, DO NOT explain ANYELSE.", null, 'gpt-4o-mini', $user);

        $replicateToken = env("REPLICATE_API_KONTEXT");
        $apiEndpoint = "https://api.replicate.com/v1/models/black-forest-labs/flux-kontext-pro/predictions";
        try {
            $initResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $replicateToken,
                'Content-Type' => 'application/json',
            ])->post($apiEndpoint, [
                "input" => [
                    "prompt" => $translatedPrompt,
                    "input_image" => $image,
                    "output_format" => $output_format,
                    "aspect_ratio" => $ratio,
                    "safety_tolerance" => 2,
                    "prompt_upsampling" => true,
                ]
            ]);

            Log::info("Replicate init response: " . $initResponse->body());

            if (!$initResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Không thể gửi yêu cầu đến Replicate']);
            }

            $initData = $initResponse->json();
            $getUrl = $initData['urls']['get'] ?? null;

            if (!$getUrl) {
                return response()->json(['success' => false, 'message' => 'Không có URL để truy vấn kết quả']);
            }

            $outputImageUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $replicateToken,
                ])->get($getUrl);

                if (!$statusResponse->successful()) break;

                $statusData = $statusResponse->json();
                Log::info("Replicate status check: " . json_encode($statusData));
                if ($statusData['status'] === 'succeeded' && !empty($statusData['output'])) {
                    $outputImageUrl = $statusData['output'];
                    break;
                }

                if ($statusData['status'] === 'failed') {
                    return response()->json(['success' => false, 'message' => 'Replicate xử lý thất bại']);
                }
            }

            if ($outputImageUrl) {
                $imageContent = file_get_contents($outputImageUrl);
                $generatedImagePath = 'images/' . Str::uuid() . '.jpg';
                Storage::disk('s3')->put($generatedImagePath, $imageContent);
                $s3Url = Helpers::preSignedS3Url($generatedImagePath);

                Auth::user()->chargeCredit('generate-image-background', null, null, null, null, 37, 37);

                return response()->json(['success' => true, 's3_url' => $s3Url]);
            }

            return response()->json(['success' => false, 'message' => 'Không lấy được ảnh kết quả sau khi chờ']);

        } catch (\Exception $e) {
            \Log::error("Replicate error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống']);
        }
    }

    public function createImageReplicate(Request $request)
    {
        $user = Auth::user();
        $imageModelUrl = $request->input("image_model");
        $imageProductUrl = $request->input("image_product");
        $ratio = $request->input("ratio", "16:9");
        $defaultPrompt = "Tạo một bức ảnh với một người mẫu đang cầm một sản phẩm, đặt sản phẩm lên tay người mẫu.";
        $prompt_kh = $request->input("prompt_kh") ? $request->input("prompt_kh") : $defaultPrompt;

        $prompt_kh .= match ($ratio) {
            '1:1' => ' Khung ảnh vuông',
            '9:16' => ' Khung ảnh dọc',
            default => ' Khung ảnh ngang',
        };

        $translatedPrompt = $this->chatGPTService->getResponse("Translate the following text to English: \"$prompt_kh\", just give me the text in English, DO NOT explain ANYELSE.", null, 'gpt-4o-mini', $user);
        $replicateToken = env("REPLICATE_API_KONTEXT");
        $apiEndpoint = "https://api.replicate.com/v1/models/flux-kontext-apps/multi-image-kontext-pro/predictions";

        try {
            $initResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $replicateToken,
                'Content-Type' => 'application/json',
            ])->post($apiEndpoint, [
                "input" => [
                    "prompt" => $translatedPrompt,
                    "aspect_ratio" => $ratio,
                    "input_image_1" => $imageProductUrl,
                    "input_image_2" => $imageModelUrl,
                    "safety_tolerance" => 2,
                    "output_format" => "png",
                    "prompt_upsampling" => true,
                ]
            ]);

            Log::info("Replicate init response: " . $initResponse->body());

            if (!$initResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Không thể gửi yêu cầu đến Replicate']);
            }

            $initData = $initResponse->json();
            $getUrl = $initData['urls']['get'] ?? null;

            if (!$getUrl) {
                return response()->json(['success' => false, 'message' => 'Không có URL để truy vấn kết quả']);
            }

            $outputImageUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $replicateToken,
                ])->get($getUrl);

                if (!$statusResponse->successful()) break;

                $statusData = $statusResponse->json();
                Log::info("Replicate status check: " . json_encode($statusData));
                if ($statusData['status'] === 'succeeded' && !empty($statusData['output'])) {
                    $outputImageUrl = $statusData['output'];
                    break;
                }

                if ($statusData['status'] === 'failed') {
                    return response()->json(['success' => false, 'message' => 'Replicate xử lý thất bại']);
                }
            }

            if ($outputImageUrl) {
                $imageContent = file_get_contents($outputImageUrl);
                $generatedImagePath = 'images/' . Str::uuid() . '.jpg';
                Storage::disk('s3')->put($generatedImagePath, $imageContent);
                $s3Url = Helpers::preSignedS3Url($generatedImagePath);

                Auth::user()->chargeCredit('image_combine', null, null, null, null, 37, 37);

                return response()->json(['success' => true, 's3_url' => $s3Url]);
            }

            return response()->json(['success' => false, 'message' => 'Không lấy được ảnh kết quả sau khi chờ']);

        } catch (\Exception $e) {
            \Log::error("Replicate error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống']);
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

    public function generateImageBackgroundWithFile(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $file = $request->file('image');

        $originalFilename = 'uploaded_images/' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('s3')->put($originalFilename, file_get_contents($file));
        $s3Url = Storage::disk('s3')->url($originalFilename);

        $imageContentFromS3 = Storage::disk('s3')->get($originalFilename);
        $imageBase64 = base64_encode($imageContentFromS3);

        $inputs = [
            'image' => $imageBase64,
            'token' => '9ec498132534d37182436163eb5bb054123fd5ec08262df8dce6710b0a71b9fe',
            'prompt_kh' => $request->prompt_kh ?? 'Tạo một background hoàn hảo cho sản phẩm này',
            "image_size" => 1024,
            "padding" => True
        ];
        $s3UrlFull = Helpers::preSignedS3Url($originalFilename);
        try {
            $response = Http::timeout(90)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post("http://103.20.97.116:9090/generate", $inputs);
            if ($response->successful()) {
                $res = $response->json();

                if (isset($res['images'][0])) {
                    $base64Image = $res['images'][0];
                    $imageData = base64_decode($base64Image);

                    // Tạo tên file và lưu lên S3
                    $filename = 'uploaded_images/' . uniqid() . '.png';
                    Storage::disk('s3')->put($filename, $imageData);

                    $s3Url = Storage::disk('s3')->url($filename);
                    $imageUrl = Helpers::preSignedS3Url($filename);

                    Auth::user()->chargeCredit('generate-image-background', null, null, null, null, 37, 37);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Ảnh đã được tạo và lưu trên S3!',
                        's3_url' => $imageUrl,
                    ]);
                } else {
                    return $this->callAPIRep($request->prompt_kh ?? 'Tạo một background hoàn hảo cho sản phẩm này', $s3UrlFull);
                }
            } else {
                return $this->callAPIRep($request->prompt_kh ?? 'Tạo một background hoàn hảo cho sản phẩm này', $s3UrlFull);
            }
        } catch (\Exception $e) {
            return $this->callAPIRep('Tạo một background hoàn hảo cho sản phẩm này '.$request->prompt_kh ?? 'Tạo một background hoàn hảo cho sản phẩm này', $s3UrlFull);
        }
    }

    public function callAPIRep($prompt, $input_image) {
        $prompt = $this->chatGPTService->getResponse2('Dịch văn bản sau sang tiếng Anh: ' . $prompt);
        $apiUrl = 'https://api.replicate.com/v1/models/black-forest-labs/flux-kontext-pro/predictions';
        $requestData = [
            'input' => [
                'prompt' => $prompt,
                'input_image' => $input_image,
                'aspect_ratio' => 'match_input_image',
                'output_format' => 'png',
                'safety_tolerance' => 2,
            ]
        ];

        $replicateApiToken = env('REPLICATE_API_TOKEN');
        try {
            // Gửi yêu cầu POST đến API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$replicateApiToken}",
                'Content-Type' => 'application/json',
                'Prefer' => 'wait',
            ])->post($apiUrl, $requestData);

            // Kiểm tra lỗi phản hồi
            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'API lỗi: ' . $response->status(),
                ], $response->status());
            }

            $responseData = $response->json();

            if (!isset($responseData['urls']['get'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không nhận được phản hồi hợp lệ từ Replicate.',
                ], $response->status());
            }

            // Lấy URL kết quả từ phản hồi ban đầu
            $getUrl = $responseData['urls']['get'];

            // Gửi yêu cầu GET để lấy dữ liệu hình ảnh
            $responseImg = Http::withHeaders([
                'Authorization' => "Bearer {$replicateApiToken}",
            ])->get($getUrl);

            $responseImgData = $responseImg->json();

            if (!isset($responseImgData['output'][0])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không nhận được URL hình ảnh hợp lệ từ API.',
                ], $response->status());
            }

            // Tải nội dung hình ảnh
            $imageUrl = $responseImgData['output'];
            $imageContent = file_get_contents($imageUrl);
            $filename = 'uploaded_images/' . uniqid() . '.png';
            Storage::disk('s3')->put($filename, $imageContent);
            $imageUrl = Helpers::preSignedS3Url($filename);
            Auth::user()->chargeCredit('generate-image-background', null, null, null, null, 37, 37);

            return response()->json([
                'status' => 'success',
                'message' => 'Ảnh đã được tạo và lưu trên S3!',
                's3_url' => $imageUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi gọi API: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function generateImageBackground(Request $request)
    {
        $request->validate([
            'image_url' => 'required',
        ]);

        $imageBase64 = base64_encode(file_get_contents($request->image_url));

        $inputs = [
            'image' => $imageBase64,
            'token' => '9ec498132534d37182436163eb5bb054123fd5ec08262df8dce6710b0a71b9fe',
            "image_size" => 1024,
            "padding" => True
        ];

        try {
            $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post("http://103.20.97.116:9090/generate", $inputs);
            if ($response->successful()) {
                $res = $response->json();

                if (isset($res['images'][0])) {
                    $base64Image = $res['images'][0];
                    $imageData = base64_decode($base64Image);

                    // Tạo tên file và lưu lên S3
                    $filename = 'uploaded_images/' . uniqid() . '.png';
                    Storage::disk('s3')->put($filename, $imageData);

                    $s3Url = Storage::disk('s3')->url($filename);
                    $imageUrl = Helpers::preSignedS3Url($filename);

                    Auth::user()->chargeCredit('generate-image-background', null, null, null, null, 37, 37);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Ảnh đã được tạo và lưu trên S3!',
                        's3_url' => $imageUrl,
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'API không trả về ảnh.',
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'API lỗi: ' . $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi gọi API: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function upscaleImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);
        $user = Auth::user();
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filename = 'images/' . $filename;
        Storage::disk('s3')->put($filename, file_get_contents($file));
        $imageUrl =Helpers::preSignedS3Url($filename);
        $replicateToken = env("REPLICATE_API_KONTEXT");
        $apiEndpoint = "https://api.replicate.com/v1/predictions";

        try {
            $initResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $replicateToken,
                'Content-Type' => 'application/json',
            ])->post($apiEndpoint, [
                "version" => "philz1337x/clarity-upscaler:99c3e8b7d14d698e6c5485143c3c3fdf92d3c5e80e9c8f1b76b4ad41a0325a14",
                "input" => [
                    "seed" => 1337,
                    "image" => $imageUrl,
                    "prompt" => "masterpiece, best quality, highres, <lora:more_details:0.5> <lora:SDXLrender_v2.0:1>",
                    "dynamic" => 6,
                    "scheduler" => "DPM++ 3M SDE Karras",
                    "creativity" => 0.35,
                    "resemblance" =>0.6,
                    "scale_factor" =>2,
                    "negative_prompt" =>"(worst quality, low quality, normal quality:2) JuggernautNegative-neg",
                    "num_inference_steps" =>18
                ]
            ]);

            Log::info("Replicate init response: " . $initResponse->body());

            if (!$initResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Không thể gửi yêu cầu đến Replicate']);
            }

            $initData = $initResponse->json();
            $getUrl = $initData['urls']['get'] ?? null;

            if (!$getUrl) {
                return response()->json(['success' => false, 'message' => 'Không có URL để truy vấn kết quả']);
            }

            $outputImageUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $replicateToken,
                ])->get($getUrl);

                if (!$statusResponse->successful()) break;

                $statusData = $statusResponse->json();
                Log::info("Replicate status check: " . json_encode($statusData));
                if ($statusData['status'] === 'succeeded' && !empty($statusData['output'])) {
                    $outputImageUrl = $statusData['output'];
                    break;
                }

                if ($statusData['status'] === 'failed') {
                    return response()->json(['success' => false, 'message' => 'Replicate xử lý thất bại']);
                }
            }
            if ($outputImageUrl[0]) {
                $imageContent = file_get_contents($outputImageUrl[0]);
                $generatedImagePath = 'images/' . Str::uuid() . '.jpg';
                Storage::disk('s3')->put($generatedImagePath, $imageContent);
                $s3Url = Helpers::preSignedS3Url($generatedImagePath);

                Auth::user()->chargeCredit('upscale-image', null, null, null, null, 37, 40);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Ảnh đã được làm nét.',
                    's3_url' => $imageUrl,
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Không lấy được ảnh kết quả sau khi chờ',
            ], 500);

        } catch (\Exception $e) {
            \Log::error("Replicate error: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Lỗi hệ thống']);
        }
    }
//     public function upscaleImage(Request $request) {
//         $request->validate([
//             'image' => 'required|image|max:10240',
//         ]);
//
//         $file = $request->file('image');
//
//         $originalFilename = 'uploaded_images/' . uniqid() . '.' . $file->getClientOriginalExtension();
//         Storage::disk('s3')->put($originalFilename, file_get_contents($file));
//         $s3Url = Storage::disk('s3')->url($originalFilename);
//
//         $imageContentFromS3 = Storage::disk('s3')->get($originalFilename);
//         $imageBase64 = base64_encode($imageContentFromS3);
//
//         $inputs = [
//             'image_base64' => $imageBase64,
//             'token' => '9ec498132534d37182436163eb5bb054123fd5ec08262df8dce6710b0a71b9fe',
//         ];
//
//         try {
//             $response = Http::timeout(120)->post('http://103.20.97.116:8668/generate', $inputs);
//
//             if ($response->successful()) {
//                 $res = $response->json();
//
//                 if (isset($res['images'][0])) {
//                     $base64Image = $res['images'][0];
//                     $imageData = base64_decode($base64Image);
//
//                     // Tạo tên file và lưu lên S3
//                     $filename = 'uploaded_images/' . uniqid() . '.png';
//                     Storage::disk('s3')->put($filename, $imageData);
//
//                     $s3Url = Storage::disk('s3')->url($filename);
//                     $imageUrl = Helpers::preSignedS3Url($filename);
//
//                     Auth::user()->chargeCredit('upscale-image', null, null, null, null, 37, 40);
//
//                     return response()->json([
//                         'status' => 'success',
//                         'message' => 'Ảnh đã được làm nét.',
//                         's3_url' => $imageUrl,
//                     ]);
//                 } else {
//                     return response()->json([
//                         'status' => 'error',
//                         'message' => 'API không trả về ảnh.',
//                     ], 500);
//                 }
//             } else {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'API lỗi: ' . $response->status(),
//                 ], $response->status());
//             }
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => 'Lỗi khi gọi API: ' . $e->getMessage(),
//             ], 500);
//         }
//     }
}

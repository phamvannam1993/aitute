<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Services\AIProjectExpertService;
use App\Services\ChatGPTService;
use App\Services\TokenCounter;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\AIProjectExpert;
use App\Models\FacebookContent;
use Inertia\Inertia;
use App\Services\ChatGPTStreamingService;
use App\Constants\AIModel;
use Illuminate\Http\Client\RequestException;
use App\Exceptions\UsageLimitExceededException;

class AIProjectExpertController extends Controller
{
    private $aiProjectExpertService;
    private $chatGPTService;

    public function __construct(AIProjectExpertService $aiProjectExpertService, ChatGPTService $chatGPTService)
    {
        $this->aiProjectExpertService = $aiProjectExpertService;
        $this->chatGPTService = $chatGPTService;
    }

    public function createProject(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required',
                'description' => 'nullable|string',
                'answers' => 'nullable|string',
                'url' => 'nullable|string',
                'image_product' => 'nullable',
                'model_product' => 'nullable',
                'files' => 'nullable'
            ], [
                'title.required' => 'Vui lòng nhập tiêu đề dự án',
                'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
                'files.json' => 'Định dạng files không hợp lệ'
            ]);

            $project = $this->aiProjectExpertService->store($validatedData);

            return response()->json([
                'message' => 'Tạo dự án thành công.',
                'data' => $project
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo dự án.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateProject(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
            ]);
            $id = $validatedData['id'];

            $project = $this->aiProjectExpertService->update($id, $request->all());

            return response()->json([
                'message' => 'Cập nhật dự án thành công.',
                'data' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật dự án.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getListProject()
    {
        try {
            $projects = $this->aiProjectExpertService->getListProjects();
            return response()->json([
                'message' => 'Danh sách dự án đã được lấy thành công.',
                'data' => $projects
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách dự án.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProjectById(Request $request)
    {
        try {
            $id = $request->projectId;
            $project = AIProjectExpert::where('id', '=', $id)->first();
            return response()->json([
                'message' => 'Success.',
                'data' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy dự án.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadDocument(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'file' => 'required'
            ]);
            $file = $validatedData['file'];
            $path = 'business-project/' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put($path, file_get_contents($file));
            $url = Helpers::preSignedS3Url($path);
            return response()->json([
                'message' => 'Tải tài liệu lên thành công.',
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tải tài liệu lên.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function calculateStreamCredit(Request $request)
    {
        try {
            $content = $request->input('content');
            if (is_string($content) && $this->isJson($content)) {
                $content = json_decode($content, true);
            }
            if (is_array($content)) {
                $firstItem = reset($content);
                if (is_array($firstItem)) {
                    $firstKey = array_key_first($firstItem);
                    $values = array_column($content, $firstKey);
                    $content = implode(',', array_filter($values));
                }
            }


            $tokenCounter = new TokenCounter();
            $model_code = 'claude-3-5-sonnet-20240620';
            $input_tokens = $tokenCounter->countTokens($content, $model_code);
            $output_tokens = $tokenCounter->estimateOutputTokens($input_tokens, $model_code, 'translation');
            if ($output_tokens > 0 ||  $input_tokens > 0) {
                Auth::user()->chargeCredit(
                    'business',
                    '',
                    null,
                    'ai-business',
                    '',
                    30,
                    30,
                    $input_tokens,
                    $output_tokens
                );
            }

            return response()->json([
                'message' => 'Thành công.',
                'data' => [
                    'input_tokens' => $input_tokens,
                    'output_tokens' => $output_tokens,
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Stream credit calculation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error calculating stream credit'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $project = AIProjectExpert::findOrFail($id);
            FacebookContent::where("project_id",$project->id)->delete();
            $project->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa dự án thành công'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dự án này'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete project error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa dự án'
            ], 500);
        }
    }

    public function getDetail($id) {
        $project = AIProjectExpert::where("id", $id)->first();
        $imagesDefault =  [
            [
                "imageRef" =>  ""
            ],
            [
                "imageRef" =>  ""
            ],
            [
                "imageRef" =>  ""
            ],
            [
                "imageRef" =>  ""
            ],
        ];
        if($project) {
            $project->buss_info = $project->buss_info  ? json_decode($project->buss_info) : "";
            $project->answers = $project->answers  ? json_decode($project->answers) : "";
            $project->image_product = $project->image_product  ?  Helpers::preSignedS3Url($project->image_product) : "";
            $project->analysis_sections = $project->analysis_sections  ? json_decode($project->analysis_sections) : "";
            $results =  $project->results ? json_decode($project->results, true) : [];
            if(!$results) {
                $results = [
                    "content" => "",
                    "images" => $imagesDefault,
                    "video_url" => ""
                ];
            } else {
                $results["images"] = isset($results["images"]) && !empty($results["images"]) ? json_decode($results["images"]) : $imagesDefault;
            }
            $project->results = $results;
        }
        return response()->json(["project" => $project]);
    }

    public function summarizeContent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'content' => 'required',
                'project_name' => 'required',
            ]);
            $content = $validatedData['content'];
            $project_name = $validatedData['project_name'];
            $summary = $this->chatGPTService->summarizeContent($content, $project_name);
            return response()->json($summary, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tóm tắt nội dung.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function gradeContent(Request $request)
    {
        $content = $request->content;
        $promptTemplate = config('chatgpt.grade_prompt');
        $prompt = str_replace(
            [':content'],
            [$content],
            $promptTemplate
        );
        $response = $this->chatGPTService->getResponse3($prompt, 'o3-mini');
        Log::info("response o3-mini: " . $response);
        return preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
    }

    public function cleanContent(Request $request)
    {
        $content = $request->content;
        $promptTemplate = config('chatgpt.clean_prompt');
        $prompt = str_replace(
            [':content'],
            [$content],
            $promptTemplate
        );
        $response = $this->chatGPTService->getResponse3($prompt, 'o3-mini');
        Log::info("response o3-mini: " . $response);
        return preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
    }

    public function improveContent(Request $request)
    {
        $content = $request->content;
        $promptTemplate = config('chatgpt.improve_prompt');
        $prompt = str_replace(
            [':content'],
            [$content],
            $promptTemplate
        );
        $response = $this->chatGPTService->getResponse3($prompt, 'o3-mini');
        Log::info("response o3-mini: " . $response);
        return preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
    }

    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function musicForBusiness()
    {
        return Inertia::render('Client/MyAssistants/MusicForBusiness');
    }

    private function send($event, $data)
    {
        try {
            if (ob_get_level() == 0) {
                ob_start();
            }
            echo "event: {$event}\n";
            echo 'data: ' . $data . "\n\n";
            echo "\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function handleAIBusinessStream(Request $request) {
        $params = $request->all();

        $queryData = json_decode($params['query'] ?? '{}', true);

        // Mẫu prompt gốc
        $systemPrompt = 'Bạn là một trợ lý tạo dữ liệu mô phỏng chuyên nghiệp, có khả năng phân tích và cung cấp thông tin chi tiết. Nhiệm vụ của bạn là tạo ra dữ liệu mô phỏng đầy đủ, chi tiết, và cụ thể cho các trường thông tin được cung cấp. Các trường thông tin cần được mô tả theo thứ tự sau:
        [Số thứ tự]. Tiêu đề:
        [Nội dung chi tiết]
        Chú ý quan trọng:
        - Kết quả trả về tuyệt đối không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown. Chỉ sử dụng thẻ <b> nếu cần in đậm.
        - Kết quả trả về theo dạng HTML, nhưng không cần cho vào thẻ HTML như ví dụ sau:
        <b>1. Tên Sản phẩm/Dịch vụ:</b>
        <p style="line-height: 1.2;"></p>
        <p><p>
        - Đảm bảo in đậm tiêu đề của mỗi mục.
        - Sử dụng một thẻ <hr> để phân tách nội dung của từng mục.
        - Hãy trình bày nội dung một cách rõ ràng, với mỗi mục trên một dòng riêng biệt. Sử dụng dấu gạch đầu dòng hoặc biểu tượng phù hợp để làm nổi bật từng mục. Tuyệt đối không để các mục dính liền vào nhau trong cùng một dòng.
        - Không thêm bất kỳ nội dung thừa nào ngoài nội dung yêu cầu.
        - Không xác nhận, không nhắc lại đề bài, không giải thích.
        - Dữ liệu tạo ra phải mang tính logic, liên kết và mô tả chi tiết, cụ thể.
        Các trường thông tin cần được mô tả theo thứ tự sau:
        1. Tên Sản phẩm/Dịch vụ:
        [Tên sản phẩm/dịch vụ cụ thể, hấp dẫn, có thể có tagline ngắn gọn kèm theo]
        2. Giới thiệu Sản phẩm/Dịch Vụ:
        [Mô tả chi tiết về sản phẩm/dịch vụ, bao gồm bản chất, công dụng tổng quan, và mục đích sử dụng. Đưa ra bối cảnh hoặc câu chuyện ngắn gọn về sự ra đời của sản phẩm/dịch vụ.]
        3. Đối tượng mục tiêu:
        [Mô tả chi tiết về đối tượng khách hàng mục tiêu, bao gồm nhân khẩu học (tuổi, giới tính, thu nhập, địa lý), tâm lý học (sở thích, hành vi, nhu cầu), và bối cảnh sử dụng sản phẩm/dịch vụ. Nếu có nhiều phân khúc, liệt kê rõ ràng từng phân khúc.]
        4. Chứng nhận và thông tin tin cậy:
        [Liệt kê các chứng nhận, giải thưởng, kiểm định chất lượng mà sản phẩm/dịch vụ đã đạt được. Đề cập đến các thông tin đáng tin cậy khác, ví dụ: đánh giá từ chuyên gia, phản hồi của khách hàng, hoặc nghiên cứu khoa học liên quan.]
        5. Tính năng và giải quyết vấn đề:
        [Liệt kê chi tiết các tính năng của sản phẩm/dịch vụ và làm rõ cách từng tính năng giải quyết các vấn đề hoặc đáp ứng các nhu cầu cụ thể của khách hàng. Nên có ví dụ minh họa cụ thể cho từng tính năng.]
        6. Cách sử dụng sản phẩm:
        [Mô tả chi tiết các bước sử dụng sản phẩm/dịch vụ một cách dễ hiểu, có thể bao gồm hình ảnh minh họa (nếu phù hợp). Nhấn mạnh tính tiện lợi và dễ sử dụng của sản phẩm/dịch vụ.]
        7. Ưu điểm và giá trị độc đáo (USP):
        [Nêu bật các ưu điểm vượt trội của sản phẩm/dịch vụ so với các đối thủ cạnh tranh. Xác định rõ giá trị độc đáo (USP) khiến khách hàng chọn sản phẩm/dịch vụ này thay vì các sản phẩm/dịch vụ khác. Giải thích lý do USP này quan trọng đối với khách hàng mục tiêu.]
        8. Lợi ích cho khách hàng:
        [Liệt kê các lợi ích cụ thể mà khách hàng nhận được khi sử dụng sản phẩm/dịch vụ, không chỉ về mặt chức năng mà còn cả về mặt cảm xúc và trải nghiệm. Nhấn mạnh lợi ích này liên quan đến việc đáp ứng nhu cầu và mong muốn của khách hàng.]
        9. Giá cả:
        [Nêu rõ mức giá của sản phẩm/dịch vụ, có thể bao gồm các gói dịch vụ hoặc các phiên bản khác nhau với mức giá khác nhau. Giải thích rõ chính sách giá (nếu có) và các yếu tố ảnh hưởng đến giá.]
        10. Câu chuyện về Thương hiệu:
        [Kể một câu chuyện hấp dẫn về sự hình thành và phát triển của thương hiệu. Nêu bật giá trị cốt lõi, sứ mệnh, và tầm nhìn của thương hiệu. Tạo sự kết nối cảm xúc với khách hàng thông qua câu chuyện.]
        11. Xu hướng và Mục tiêu Thương hiệu:
        [Mô tả xu hướng thị trường mà sản phẩm/dịch vụ đang nhắm đến, và cách thương hiệu dự định phát triển để đáp ứng xu hướng đó. Nêu rõ các mục tiêu cụ thể và có thể đo lường của thương hiệu trong tương lai.]
        12. Liên hệ:
        [Viết là : Chưa có thông tin]';

        $ten_du_an = $queryData['ten_san_pham_dich_vu'] . ', ' . $queryData['gioi_thieu_san_pham_dich_vu'];
        $userPrompt = 'Hãy tạo ra dữ liệu mô phỏng dựa cho sản phẩm, dịch vụ này: ' . $ten_du_an;

        $inputModel = 'GPT-4o';
        $model = AIModel::getModel($inputModel);
        $messages = [
            [
                'role' => 'user',
                'content' => $userPrompt
            ]
        ];

        $aiBusinessProject = AIProjectExpert::find($params['project_id']);
        $aiBusinessProject->update(['is_use_backup_flow' => true]);


        return response()->stream(function () use ($messages, $systemPrompt, $model, $inputModel) {
            $retryCount = 0;
            $maxRetries = 3;
            $retryDelay = 1000; // 1 giây
            $result_text = "";
            while ($retryCount < $maxRetries) {
                try {
                    $user = auth()->user();
                    $user->decrementUsage(config('constant.assistant_types.text'));
                    $stream = $this->chatGPTService->streamContentResponse($messages, $systemPrompt, $model);
                    foreach ($stream as $response) {
                        $text = $response->choices[0]->delta->content ?? '';
                        Log::info('Nhận được văn bản: ' . $text);

                        if (connection_aborted()) {
                            Log::info('Kết nối đã bị hủy.');
                            break 2;
                        }

                        $data = [
                            'event' => 'message',
                            'answer' => $text,
                        ];
                        $this->send("message", json_encode($data));

                        $result_text .= $text;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }

                    $this->send("workflow_finished", json_encode([
                        'event' => 'workflow_finished'
                    ]));
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    break;

                } catch (RequestException $e) {
                    Log::error('RequestException: ' . $e->getMessage());
                    if ($e->getCode() == 529) {
                        $retryCount++;
                        if ($retryCount >= $maxRetries) {
                            echo "data: " . json_encode(['error' => "Không thể lấy dữ liệu sau {$maxRetries} lần thử do giới hạn tỉ lệ."]) . "\n\n";
                        } else {
                            echo "data: " . json_encode(['retry' => "Giới hạn tỉ lệ đã vượt quá. Thử lại sau " . ($retryDelay / 1000) . " giây..."]) . "\n\n";
                            usleep($retryDelay * 1000);
                            $retryDelay *= 2;
                        }
                        ob_flush();
                        flush();
                    }
                } catch (UsageLimitExceededException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 403);
                } catch (\Exception $e) {
                    Log::error('Unexpected Exception: ' . $e->getMessage());
                    if (ob_get_level() > 0) {
                        echo "data: " . json_encode(['error' => 'Đã xảy ra lỗi không mong muốn.']) . "\n\n";
                        ob_flush();
                        flush();
                    }
                    break;
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function  extractTagsFromContent(Request $request) {
        $htmlContent = $request->content;
        $texts = preg_split('/<strong>.*?<\/strong>/i', $htmlContent);
        $texts = array_filter($texts, 'strlen'); // Loại bỏ các phần tử rỗng
        $texts = array_values($texts); // Reset lại index của mảng
        $textArr = [];
        for($i = 0; $i < count($texts); $i++) {
            if($i > 0) {
                $textArr[] = str_replace(['<p>', '</p>', '<hr>', '<ul>', '</ul>', '<li>', '</li>'], "", $texts[$i]);
            }
        }

        if(count($textArr) == 17) {
            return response()->json(["contents" => $textArr, "success" => true]);
        } else {
            return response()->json(["message" => "Nội dung không đủ thông tin", "success" => false]);
        }
    }
}

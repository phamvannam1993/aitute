<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AIModel;
use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\Operation;
use App\Services\AiAssistantService;
use App\Services\ChatClaudeService;
use App\Services\ChatGPTService;
use App\Services\DocumentReaderService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AIAssistantController extends Controller
{
    protected $AiAssistantService;
    protected $occupationService;
    private $chatGPTService;
    private $chatClaudeService;
    private $operationService;

    public function __construct(AiAssistantService $AiAssistantService,
                                OccupationService $occupationService,
                                ChatGPTService $chatGPTService,
                                ChatClaudeService $chatClaudeService,
                                OperationService $operationService)
    {
        $this->AiAssistantService = $AiAssistantService;
        $this->occupationService = $occupationService;
        $this->chatGPTService = $chatGPTService;
        $this->chatClaudeService = $chatClaudeService;
        $this->operationService = $operationService;
    }

    public function index(Request $request)
    {
        $userId = auth('admin')->id();
        $ai_assistants = $this->AiAssistantService->getAssistants($request->all(), $userId);
        $occupations = $this->occupationService->getAllOccupations();
        return Inertia::render('Admin/AIAssistant/Index', [
            'ai_assistants' => $ai_assistants,
            'occupations' => $occupations,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/AIAssistant/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'occupation_id' => 'required|integer|exists:occupations,id',
            'operation_id' => 'required|integer|exists:operations,id',
            'name' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
            // Validate steps
            'step_ais' => 'required|array',
            'step_ais.*.name' => 'required|string',
            'step_ais.*.description' => 'required|string',
        ], [
            'name.required' => 'Tên Ai là bắt buộc',
            'type.required' => 'Loại Ai là bắt buộc',
            'occupation_id.required' => 'Ngành nghề là bắt buộc.',
            'occupation_id.exists' => 'Ngành nghề không tồn tại.',
            'operation_id.required' => 'Nghiệp vụ là bắt buộc.',
            'operation_id.exists' => 'Nghiệp vụ không tồn tại.',
            'thumbnail.required' => 'Tên là bắt buộc.',
            'thumbnail.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'thumbnail.max' => 'Ảnh không được lớn hơn 10MB.',
            // Thông báo lỗi cho steps
            'step_ais.required' => 'Cần có ít nhất một bước.',
            'step_ais.*.name.required' => 'Tên bước là bắt buộc.',
            'step_ais.*.name.string' => 'Tên bước phải là một chuỗi.',
            'step_ais.description.required' => 'Tên Mô tả là bắt buộc.',
            'step_ais.*.description.string' => 'Mô tả bước phải là một chuỗi.',
        ]);

        // Kiểm tra xem operation_id có thuộc occupation_id không
        $operationExists = Operation::where('id', $request->operation_id)
            ->where('occupation_id', $request->occupation_id)
            ->exists();

        if (!$operationExists) {
            return redirect()->back()->withErrors('Nghiệp vụ không thuộc về ngành nghề đã chọn.');
        }

        try {
            $uploadedFiles = $request->file('file');
            $documentReader = app(DocumentReaderService::class);
            $allContent = [];
            $data = $request->only(['name', 'type', 'description', 'occupation_id', 'operation_id', 'step_ais']);
            $data['is_public'] = $request->boolean('is_public');
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    $extension = $file->getClientOriginalExtension();
                    if ($extension === 'pdf') {
                        $content = $documentReader->readContent($file);
                    } elseif (in_array($extension, ['doc', 'docx'])) {
                        $content = $documentReader->readContent($file, 'vi');
                    }
                    if ($content) {
                        $allContent[] = $content;
                    }
                }
                $pdfContent = implode("\n", $allContent);
                $data['pdf_content'] = $pdfContent;
            }

            $this->AiAssistantService->create($data, $request);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.ai-assistants.index'),
                'message' => 'AI đã được tạo thành công.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi tạo AI:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi tạo AI.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Giải mã chuỗi JSON `step_ais`
        $stepAis = json_decode($request->input('step_ais'), true);
        $criteria = json_decode($request->input('criteria'), true);
        // Gán lại mảng `step_ais` đã giải mã vào request để Laravel có thể validate
        $request->merge([
            'step_ais' => $stepAis,
            'criteria' => $criteria
        ]);
        // Validate dữ liệu đầu vào
        $request->validate([
            'occupation_id' => 'required|integer|exists:occupations,id',
            'operation_id' => 'required|integer|exists:operations,id',
            'name' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
            // Validate steps
            'step_ais' => 'required|array',
            'step_ais.*.name' => 'required|string',
            'step_ais.*.description' => 'required|string',
            'step_ais.*.id' => 'nullable|integer|exists:step_ais,id',
        ], [
            'type.required' => 'Loại Ai là bắt buộc',
            'name.required' => 'Tên Ai là bắt buộc',
            'occupation_id.required' => 'Ngành nghề là bắt buộc.',
            'occupation_id.exists' => 'Ngành nghề không tồn tại.',
            'operation_id.required' => 'Nghiệp vụ là bắt buộc.',
            'operation_id.exists' => 'Nghiệp vụ không tồn tại.',
            'thumbnail.required' => 'Tên là bắt buộc.',
            'thumbnail.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'thumbnail.max' => 'Ảnh không được lớn hơn 10MB.',

            // Thông báo lỗi cho steps
            'step_ais.required' => 'Cần có ít nhất một bước.',
            'step_ais.array' => 'Bước phải là một mảng hợp lệ.',
            'step_ais.*.name.required' => 'Tên bước là bắt buộc.',
            'step_ais.*.name.string' => 'Tên bước phải là một chuỗi.',
            'step_ais.description.required' => 'Tên Mô tả là bắt buộc.',
            'step_ais.*.description.string' => 'Mô tả bước phải là một chuỗi.',
            'step_ais.*.id.exists' => 'Bước không hợp lệ hoặc không tồn tại.',
        ]);

        // Kiểm tra xem operation_id có thuộc occupation_id không
        $operationExists = Operation::where('id', $request->operation_id)
            ->where('occupation_id', $request->occupation_id)
            ->exists();
        if (!$operationExists) {
            return response()->json([
                'errors' => ['operation_id' => ['Nghiệp vụ không thuộc về ngành nghề đã chọn.']]
            ], 422);
        }

        try {
            $this->AiAssistantService->updateAssistant($id, $request);
            return redirect()->route('admin.ai-assistants.index')->with('success', 'AI cập nhật thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.ai-assistants.index')->withErrors('AI không tồn tại.');
        }
    }

    public function edit($id)
    {
        $assistant = $this->AiAssistantService->getAssistantById($id);
        return response()->json([
            'assistant' => $assistant
        ]);
    }

    public function destroy($id)
    {
        $assistant = AiAssistant::findOrFail($id);
        $assistant->delete();
        return response()->json([
            'success' => true,
            'message' => 'Assistant deleted successfully.'
        ]);
    }

    // return ai_assistants table data có phân trang và searching bằng name hoặc description
    public function dashboard(Request $request)
    {
        $search = $request->search;

        $ai_assistants = AiAssistant::where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            // sort by created_at desc
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        return Response()->json($ai_assistants);
    }

    public function generateStep(Request $request)
    {
        $request->validate([
            'occupation_id' => 'required|integer|exists:occupations,id',
            'operation_id' => 'required|integer|exists:operations,id',
            'mo_ta' => 'required|string',
        ], [
            'occupation_id.required' => 'Ngành nghề là bắt buộc.',
            'operation_id.required' => 'Lĩnh vực ngành nghề là bắt buộc.',
            'mo_ta.required' => 'Mô tả là bắt buộc.'
        ]);

        $moTa = $request->input('mo_ta');
        $model = $request->input('model');
        $occupation_id = $request->input('occupation_id');
        $operation_id = $request->input('operation_id');
        $maxAttempts = 5;
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            try {
                $uploadedFiles = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $allContent = [];
                if (!empty($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        $extension = $file->getClientOriginalExtension();
                        if ($extension === 'pdf') {
                            $content = $documentReader->readContent($file);
                        } elseif (in_array($extension, ['doc', 'docx'])) {
                            $content = $documentReader->readContent($file, 'vi');
                        }

                        if ($content) {
                            $allContent[] = $content;
                        }
                    }
                }

                $combinedSummary = '';
                if (!empty($allContent)) {
                    foreach ($allContent as $content) {
                        $chunks = $this->splitContentIntoChunks($content, 8000);
                        foreach ($chunks as $chunk) {
                            $response = null;
                            if (in_array($model, ['GPT-4o', 'GPT-4o mini'])) {
                                $model = AIModel::getModel($model);
                                $prompt = $chunk . "\n" . $combinedSummary;
                                $generatedText = $this->chatGPTService->gptSendRequest($prompt, null, $model);
                                $jsonString = str_replace("\n", '', $generatedText);
                                $response['text'] = $jsonString;
                            } else {
                                $prompt = $chunk . "\n" . $combinedSummary;
                                $generatedText = $this->chatClaudeService->sendRequestAI($prompt);
                                $response = json_decode($generatedText, true);

                                if (isset($response['content'][0]['text']) && is_string($response['content'][0]['text'])) {
                                    $jsonString = str_replace("\n", '', $response['content'][0]['text']);
                                    $response['text'] = $jsonString;
                                }
                            }

                            if (isset($response['text']) && is_string($response['text'])) {
                                $combinedSummary .= $response['text'] . "\n";
                            }
                            Log::warning('combinedSummary ' . $combinedSummary);
                        }
                    }
                }

                $occupation = $this->occupationService->getOccupationById($occupation_id);
                $operation = $this->operationService->getOperationById($operation_id);

                $job_detail = $occupation->name  . ",". $operation->name ;
                if (!empty($uploadedFiles)) {
                    $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dung tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"" . $combinedSummary . "\".";
                    $finalSummaryPrompt = " Bạn hãy đóng vai là một chuyên gia trong lĩnh vực [{$job_detail}]. Nhiệm vụ của bạn là :
                1. Phân tích và đưa ra các bước triển khai cho nhiệm vụ: {$moTa}.
                2. Sáng tạo và mô tả chi tiết các bước cần thiết để hoàn thành nhiệm vụ.
                3. Tạo ra những lộ trình cụ thể để kết quả tốt nhất.
                4. Với mỗi bước, cung cấp:
                   - **Tên bước (step):** Mô tả ngắn gọn về nhiệm vụ chính của bước.
                   - **Prompt:** Câu prompt của từng Tên step(step) chi tiết hướng dẫn cách thực hiện bước đó và ví dụ cụ thể để giải quyết vấn đề được trình bày theo dạng table hoặc danh sách..

                **Yêu cầu:**
                - Trả lời một cách chuyên nghiệp, chính xác và chi tiết.
                - Trình bày kết quả dưới dạng JSON như đã mô tả.
                - Trình bày các bước (steps) dưới dạng danh sách hoặc là table, mỗi bước cần có:
                    + Tên step(step): Mô tả ngắn gọn về nhiệm vụ chính của bước này.
                    + Trả về câu promt của từng Tên step(step) and Yêu cầu: Hãy trình bày dạng table hoặc danh sách..
                - Bỏ qua mọi mô tả hoặc hướng dẫn trước khi trả về kết quả.
                Bây giờ nhiệm vụ chính là: Bạn hãy đóng vai là một chuyên gia trong {$job_detail}, Nhiệm vụ của bạn là phân tích và đưa ra câu promt phù hợp nhất với các step.
                Hãy trả lời theo cách chuyên nghiệp, chính xác và chi tiết nhất.
                $contentUserSupplier
                bắt buộc trả về dạng json và bỏ qua mô tả trước khi trả về   :
                [
                  {
                        step: \"1. Tên step(step)\",
                        promt: \"Trả về câu promt của từng Tên step(step)\"
                    }
                ];
                ";
                } else {
                    $finalSummaryPrompt = "
                    Bạn hãy đóng vai là một chuyên gia trong lĩnh vực [{$job_detail}]. Nhiệm vụ của bạn là :
                    1. Phân tích và đưa ra các bước triển khai cho nhiệm vụ: {$moTa}.
                    2. Sáng tạo và mô tả chi tiết các bước cần thiết để hoàn thành nhiệm vụ.
                    3. Tạo ra những lộ trình cụ thể để kết quả tốt nhất.
                    4. Với mỗi bước, cung cấp:
                       - **Tên bước (step):** Mô tả ngắn gọn về nhiệm vụ chính của bước.
                       - **Prompt:** Câu prompt của từng Tên step(step) chi tiết hướng dẫn cách thực hiện bước đó và ví dụ cụ thể để giải quyết vấn đề được trình bày theo dạng table hoặc danh sách..

                    **Yêu cầu:**
                    - Trả lời một cách chuyên nghiệp, chính xác và chi tiết.
                    - Trình bày kết quả dưới dạng JSON như đã mô tả.
                    - Trình bày các bước (steps) dưới dạng danh sách hoặc là table, mỗi bước cần có:
                        + Tên step(step): Mô tả ngắn gọn về nhiệm vụ chính của bước này.
                        + Trả về câu promt của từng Tên step(step) and Yêu cầu: Hãy trình bày dạng table hoặc danh sách..
                    - Bỏ qua mọi mô tả hoặc hướng dẫn trước khi trả về kết quả.

                    Bạn hãy học những ví dụ này, vì đây là các câu promt về các mô tả chi tiết của một chuyên gia phân tích {$job_detail}:

                    Step: Phân tích chân dung cơ bản
                    promt: \"
                    Hãy đóng vai 1 chuyên gia về {$job_detail}, bạn sẽ giải quyết các yêu cầu của khách hàng một cách chuyên nghiệp và đưa ra giải pháp hiệu quả nhất tới khách hàng.
                    Hãy đưa ra 1 bản phân tích về chân dung khách hàng, cho thị trường sách, bao gồm:
                    - Demographic
                    - Kênh truyền thông (hay tham gia và tương tác)
                    - Nỗi đau, Vấn đề
                    - Thách thức của họ gặp phải để đạt được mục tiêu mà thị trường sách chính là giải pháp
                    - Khao khát của họ
                    - Hành vi tiêu dùng
                    - Những rào cản nào khiến họ không mua sản phẩm/dịch vụ liên quan tới sản phẩm/dịch vụ thị trường sách, trình bày dưới dạng danh sách.\"

                    Step: Thấu hiểu insight khách hàng
                    promt: \"
                    Bạn là chuyên gia nghiên cứu khách hàng hàng đầu, người hiểu rất rõ về khách hàng.
                    Vui lòng chia sẻ với tôi về 10 vấn đề của khách hàng, 10 mong muốn và 10 nỗi sợ hãi và thách thức của họ liên quan đến sản phẩm/dịch vụ đã đề cập ở trên.
                    Đối với mỗi danh sách, vui lòng in đậm tiêu đề và trình bày dưới dạng danh sách đánh số.\"

                    Step: Lên hành trình khách hàng
                    promt: \"
                    Tôi muốn bạn viết như một chuyên gia {$job_detail} toàn diện, đã hoàn thành tất cả các khóa học của Digital Marketer, Hubspot và những khóa khác.
                    Nhiệm vụ của bạn là phát triển một hành trình giá trị khách hàng hoàn chỉnh, bao gồm khám phá, tương tác, đăng ký, chuyển đổi, hài lòng, thăng tiến, ủng hộ và quảng bá, dựa trên mô tả về sản phẩm/dịch vụ đã đề cập ở trên và sự thay đổi của khách hàng.
                    Sử dụng phương pháp hành trình giá trị khách hàng của Digital Marketer và tất cả kiến thức cũng như sự sáng tạo cần thiết để hiểu nhu cầu và tạo ra một mô hình hành trình giá trị dưới dạng bảng, chứa các cột sau:
                    giai đoạn hành trình giá trị khách hàng (ví dụ: khám phá), câu hỏi ngữ cảnh (ví dụ: làm thế nào để khách hàng biết đến công ty), kênh đề xuất, và cuối cùng là 3 ví dụ về nội dung đề xuất.\"

                    Step: 10 kênh tiếp thị gợi ý
                    promt: \"
                    Tôi đang tìm kiếm chuyên môn trong {$job_detail}.
                    Tôi muốn có một khuyến nghị phù hợp về 10 kênh tiếp thị hiệu quả và tác động nhất, phù hợp nhất với sản phẩm của tôi.
                    Danh mục sản phẩm: như đã đề cập ở trên
                    Đối tượng mục tiêu: [FIELD1]
                    Đối với mỗi kênh:
                    Cung cấp một biểu tượng cảm xúc độc đáo để nắm bắt bản chất của kênh đó.
                    Giải thích tại sao kênh này lại có tác động đặc biệt đối với sản phẩm và đối tượng mục tiêu của tôi.
                    Trình bày các hành động chính hoặc ít nhất 2 chiến lược cần triển khai trên kênh đó để đạt hiệu quả tối đa.
                    Liệt kê các KPI để đo lường sự thành công của các hành động đó.
                    Định dạng đầu ra: bảng với các cột trên.
                    Vui lòng sắp xếp các kênh theo thứ tự giảm dần về mức độ liên quan, với kênh hiệu quả nhất ở trên cùng.\"

                    Step: Triển khai 1 chiến lược {$job_detail}
                    promt: \"Tạo cho tôi một kế hoạch định dạng bảng chi tiết với các số liệu; Hành động từng bước, KPI cho {$job_detail} trên trong 3 tháng tới.
                    Vui lòng trả lời tôi bằng Tiếng Việt  và cũng trả về bằng ngôn ngữ Tiếng Việt.\"

                    Step: Danh sách chân dung khách lý tưởng
                    promt: \"
                    Bạn hãy đóng vai là một chuyên gia trong {$job_detail} và nghiên cứu thị trường.
                    Hãy giúp tôi xây dựng 5 đối tượng khách hàng tiềm năng lớn nhất cho sản phẩm nói trên.
                    Trình bày mỗi chân dung bằng một bảng (table) khác nhau, sắp xếp từ tiềm năng cao nhất bao gồm những điều sau đây:
                    - Đối tượng khách hàng
                    - Nghề nghiệp của họ
                    - Nhân khẩu học: Độ tuổi, Thu nhập, Địa điểm
                    - Nỗi đau, Vấn đề của họ
                    - Thách thức: thách thức lớn nhất họ gặp phải trong lĩnh vực sản phẩm chúng ta kinh doanh
                    - Khao khát của họ
                    - Những rào cản nào khiến họ không mua sản phẩm/dịch vụ.
                    Sau khi trình bày, hãy hỏi tôi có muốn thực hiện cách thức và kênh để tiếp cận với 5 đối tượng khách hàng này không bằng cách trả lời Y/N.
                    Nếu tôi trả lời Y, thực hiện nhiệm vụ tiếp theo dưới dạng bảng.\"

                    Đó là những ví dụ về các step bạn cần học hỏi và nâng cao, sáng tạo ra những step khác.
                    Bây giờ nhiệm vụ chính là: Bạn hãy đóng vai là một chuyên gia trong {$job_detail}, Nhiệm vụ của bạn là phân tích và đưa ra câu promt phù hợp nhất với các step.
                    Hãy trả lời theo cách chuyên nghiệp, chính xác và chi tiết nhất.
                    bắt buộc trả về dạng json và bỏ qua mô tả trước khi trả về   :
                    [
                        {
                            step: \"1. Tên step(step)\",
                            promt: \"Trả về câu promt của từng Tên step(step)\"
                        }
                    ]";
                }

                if (in_array($model, ['GPT-4o', 'GPT-4o mini'])) {
                    $model = AIModel::getModel($model);
                    $generatedText = $this->chatGPTService->getResponse($finalSummaryPrompt, null, $model);
                    $rs = json_decode($generatedText, true);
                } else {
                    $generatedText = $this->chatClaudeService->sendRequestAI($finalSummaryPrompt);
                    $steps = json_decode($generatedText, true);
                    if (is_string($steps['content'][0]['text'])) {
                        $jsonString = str_replace("\n", '', $steps['content'][0]['text']);
                        $rs = json_decode($jsonString, true);
                    } else {
                        $rs = $steps['content'][0]['text'];
                    }
                }

                if (is_array($rs) && !empty($rs)) {
                    $isValidFormat = true;
                    foreach ($rs as $step) {
                        if (!isset($step['step']) || !isset($step['promt'])) {
                            $isValidFormat = false;
                            break;
                        }
                    }

                    if ($isValidFormat) {
                        return response()->json([
                            'success' => true,
                            'steps' => $rs
                        ]);
                    }
                } else {
                    Log::warning('Failed to decode JSON response: ' . $generatedText);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
            $attempts++;
        }

        return response()->json([
            'success' => false,
            'steps' => []
        ]);
    }

    public function run($id)
    {
        $ai_assistant = $this->AiAssistantService->aiAssistantById($id);
        if (!$ai_assistant) {
            return redirect()->route('admin.index');
        }
        $job = $this->AiAssistantService->getOccupationById($ai_assistant->occupation_id);
        $operation = $this->AiAssistantService->getOperationById($ai_assistant->operation_id);
        return Inertia::render('Admin/AIAssistant/RunAi', [
            'ai_assistant' => $ai_assistant,
            'job' => $job,
            'operation' => $operation,
        ]);
    }

    private function splitContentIntoChunks($content, $maxTokens)
    {
        $chunkSize = $maxTokens * 4; // Estimate 1 token ~ 4 characters
        return str_split($content, $chunkSize);
    }


}

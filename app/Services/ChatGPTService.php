<?php

namespace App\Services;

use App\Exceptions\DomainException;
use Exception;
use Illuminate\Http\Response;
use App\Helper\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenAI;
use GuzzleHttp\Client;
class ChatGPTService
{
    protected $client;
    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }

    public function streamResponse($messages, $model = 'gpt-4o-mini')
    {
        try {
            $system_prompt = "
                - Sử dụng cấu trúc đơn giản, tránh các ký tự đặc biệt và markdown phức tạp
                - Chỉ sử dụng các thẻ HTML cơ bản: <p>, <br>, <ul>, <li>, <h1> đến <h6>
                - Tách các đoạn văn bản bằng thẻ <p>
                - Sử dụng <br> cho xuống dòng đơn
                - Danh sách dùng <ul> và <li>
                - Không sử dụng các ký tự như \\, **, ##, \\[, \\]
                - Trả lời trực tiếp bằng tiếng Việt
                - Tránh sử dụng các công thức toán học phức tạp
                - Mỗi câu phải kết thúc bằng dấu chấm và thẻ <br>
                - Không được viết nhiều câu liên tục trên cùng một dòng
                - Mỗi ý mới phải bắt đầu bằng thẻ <p> mới
                - Với danh sách, mỗi mục phải nằm trong thẻ <li> riêng biệt
                - Tự động xuống dòng sau mỗi dấu chấm câu (.!?)
                - Đảm bảo việc xuống dòng không làm ngắt quãng từ hoặc ý nghĩa của câu
                ";
            $messages = array_merge([
                [
                    'role' => 'system',
                    'content' => $system_prompt
                ]
            ], $messages);
            $stream = $this->client->chat()->createStreamed([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 8000,
                'stream' => true
            ]);
            return $stream;
        } catch (\Exception $e) {
            throw new DomainException("Error Processing Request: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function getResponse(string $prompt, string $thread = null, string $model = 'gpt-4o-mini', User|null $user = null, $customCharge = false): string
    {
        if (!$user) {
            $user = Auth::user();
        }

        try {
            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            $user = auth()->user();
            Log::error('Credit check failed for user ID: ' . $user->id ?? '' . ', remaining credit: ' . $user->credit ?? '' . '. Error: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.';
        }
    }

    public function getResponse2(string $prompt, string $thread = null, string $model = 'gpt-4o-mini', User|null $user = null, $customCharge = false): string
    {

        try {
            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'yêu cầu Chỉ trả về nội dung . không có kí tự đặc biệt nào ngoài text',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);


            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            $user = auth()->user();
            Log::error('Credit check failed for user ID: ' . $user->id ?? '' . ', remaining credit: ' . $user->credit ?? '' . '. Error: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.';
        }
    }

     public function getResponse3(string $prompt, string $model = 'o3-mini'): string
    {
        try {
            $client = new Client([
                'timeout' => 120,
            ]);

            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer " . env('OPENAI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Chỉ trả về nội dung văn bản, không có ký tự đặc biệt.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ],
                'timeout' => 120,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['choices'][0]['message']['content'] ?? 'Không có phản hồi từ AI.';
        } catch (\Exception $e) {
            Log::error('Error calling OpenAI: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn.';
        }
    }


    /**
     * Summary of getImage
     * @param string $prompt
     * @param int $n   Số ảnh muốn tạo ra
     * @param string $size
     * @throws \App\Exceptions\DomainException
     * @return array<int, array{url?: string, b64_json?: string, revised_prompt?: string}>
     *
     */
    public function getImage(string $prompt, int $n = 1, string $size = '1024x1024'): array
    {
        try {
            $response = $this->client->images()->create([
                'prompt' => $prompt,
                'n' => $n,
                'size' =>  $size,
            ]);

            $response = $response->toArray();
            if (!isset($response['data'])) {
                throw new DomainException('Tạo ảnh không thành công. Vui lòng kiểm tra lại yêu cầu.', Response::HTTP_BAD_REQUEST);
            }

            return $response['data'];
        } catch (\Exception $e) {
            throw new DomainException("Error Processing Request: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function getResponseNoCredit(string $prompt, string $thread = null, string $model = 'gpt-4o-mini'): string
    {
        try {
            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            throw new DomainException("Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function summary(string $content): ?string
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Bạn là một trợ lý thông minh, người dùng sẽ nhập vào một đoạn nội dung, nhiệm vụ của bạn là trả về một tiêu đề ngắn gọn cho đoạn nội dung đó. Chỉ trả về nội dung tiêu đề và không giải thích thêm.',
                ],
                [
                    'role' => 'user',
                    'content' => "Đặt tiêu đề ngắn gọn cho nội dung sau: `$content`",
                ],
            ],
        ]);

        return str_replace(['"', "'"], '', $response->choices[0]->message->content);
    }

    public function gptSendRequest(string $prompt, string $thread = null, string $model = 'gpt-4o-mini'): string
    {
        try {
            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            $user = auth()->user();
            Log::error('Credit check failed for user ID: ' .$user->id ?? '' . ', remaining credit: ' . $user->credit ?? '' . '. Error: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.';
        }
    }

    public function streamMessage(string $prompt, string $thread = null, string $model = 'gpt-4o-mini'): \Generator
    {
        try {
            $stream = $this->client->chat()->createStream([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);

            foreach ($stream as $response) {
                yield $response;
            }
        } catch (\Exception $e) {
            throw new DomainException("Error Processing Request: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function generateTranslate(string $sentences, string $locale): string
    {
        $language = config("chatgpt.languages.{$locale}");
        $promptTemplate = config('chatgpt.prompt_translate');
        $prompt = str_replace([':language', ':sentence'], [$language, $sentences], $promptTemplate);
        return $this->gptSendRequest($prompt);
    }

    public function getResponseChatBot(string $prompt, string $thread = null, string $model = 'gpt-4o-mini', User|null $user = null, $customCharge = false): string
    {
        if (!$user) {
            $user = Auth::user();
        }

        try {
            // Thêm system prompt để định dạng output
            $systemPrompt = <<<EOT
                Hãy trả lời với format đơn giản như một cuộc trò chuyện:

                1. CẤU TRÚC TRẢ LỜI:
                   Tiêu đề chính (nếu cần)

                   Phần giới thiệu ngắn về chủ đề

                   1. Phần chính
                   • Điểm thứ nhất
                   • Điểm thứ hai
                   • Điểm thứ ba

                2. LƯU Ý:
                   - Trả lời tự nhiên như đang trò chuyện
                   - Dùng ngôn ngữ đơn giản, dễ hiểu
                   - Phân đoạn rõ ràng để dễ đọc
                   - Tránh mọi ký tự đặc biệt ([], **, {}, <>...)
            EOT;

            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'thread' => $thread,
                    ],
                ],
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            $user = auth()->user();
            Log::error('Credit check failed for user ID: ' . $user->id ?? '' . ', remaining credit: ' . $user->credit ?? '' . '. Error: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.';
        }
    }

        public function streamResponseChatAI($messages, $model = 'gpt-4o-mini', $lang)
{
    try {
        // 1. Initialize context analyzer
        $contextAnalyzer = new ContextAnalyzerService($messages);
        $context = $contextAnalyzer->detect();
        Log::info('$contextAnalyzer: ' . $context);
        // 2. Load base prompts
        $base_prompt = $this->getBasePrompt($context);
        $format_prompt = $this->getFormatPrompt();
        Log::info('$format_prompt: ' . $format_prompt);
        // 3. Get context-specific additions
        $contextPrompt = $this->getContextSpecificPrompt($context);

        // 4. Build system messages
        $systemMessages = $this->buildSystemMessages($base_prompt, $format_prompt, $contextPrompt, $lang);

        // 5. Merge with user messages
        $finalMessages = array_merge($systemMessages, $messages);

        // 6. Get optimal parameters
        $parameters = $this->getOptimalParameters($context, $model);
        // Log arrays properly
        Log::info('Final messages:', [
            'messages' => json_encode($finalMessages, JSON_PRETTY_PRINT),
            'parameters' => json_encode($parameters, JSON_PRETTY_PRINT)
        ]);
        // 7. Create and return stream
        return $this->createStream($finalMessages, $parameters);

    } catch (Exception $e) {
        $this->handleError($e);
    }
}

    private function getBasePrompt($context)
{
    return "
            Bạn là trợ lý AI thông minh, có khả năng:

            1. NHẬN DIỆN & PHÂN TÍCH:
            - Tự động nhận diện chủ đề từ câu hỏi của người dùng
            - Điều chỉnh cách trả lời phù hợp với từng chủ đề
            - Trả lời theo cấu trúc phù hợp nhất với chủ đề

            2. NGUYÊN TẮC TRẢ LỜI:
            - Nếu là dự báo/phân tích: Đưa ra phân tích chi tiết, số liệu, bảng biểu
            - Nếu là câu hỏi kiến thức: Giải thích rõ ràng, ví dụ minh họa
            - Nếu là tư vấn: Đưa ra các lựa chọn và lý do
            - Nếu là chat casual: Trò chuyện tự nhiên, thân thiện
            - Với câu hỏi về giá/thị trường: Luôn phân tích nhiều kịch bản

            3. PHONG CÁCH:
            - Ngắn gọn với câu hỏi đơn giản
            - Chi tiết, chuyên sâu với câu hỏi phân tích
            - Thân thiện, dễ hiểu
            - Luôn đưa ra ví dụ cụ thể khi cần

            4. TƯƠNG TÁC & HỖ TRỢ:
            - Chủ động làm rõ khi câu hỏi chưa rõ ràng
            - Gợi ý thông tin liên quan hữu ích
            - Sẵn sàng giải thích sâu hơn khi được yêu cầu

            5. NGÔN NGỮ:
            - Sử dụng ngôn ngữ phù hợp với $context
            - Tránh từ ngữ technical với người dùng phổ thông
            - Giải thích các thuật ngữ chuyên môn khi cần thiết";
}

    private function getFormatPrompt()
{
    return "
            ĐỊNH DẠNG HIỂN THỊ CHI TIẾT:

            1. Container chính (phần tổng quan):
            <div style='background:#f8f9fa; padding:15px; border-radius:8px; margin:8px 0;'>
                <h3 style='margin:12px 0 8px; color:#333;'>Tổng quan phân tích</h3>
                <p style='margin:8px 0; line-height:1.5;'>[Tối thiểu 200 từ giới thiệu tổng quan]</p>
            </div>

            2. Bảng dữ liệu (nếu cần):
            <div style='overflow-x:auto;'>
                <table style='width:100%; border-collapse:collapse; background:white; margin:10px 0;'>
                    <thead>
                        <tr style='background:#f0f2f5;'>
                            <th style='padding:10px; border:1px solid #ddd; text-align:left;'>[Header 1]</th>
                            <th style='padding:10px; border:1px solid #ddd; text-align:left;'>[Header 2]</th>
                            <th style='padding:10px; border:1px solid #ddd; text-align:left;'>[Header 3]</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style='padding:10px; border:1px solid #ddd;'>[Dữ liệu 1]</td>
                            <td style='padding:10px; border:1px solid #ddd;'>[Dữ liệu 2]</td>
                            <td style='padding:10px; border:1px solid #ddd;'>[Dữ liệu 3]</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            3. Text blocks (phân tích chi tiết):
            <div style='background:#f8f9fa; padding:15px; border-radius:8px; margin:8px 0;'>
                <h3 style='margin:12px 0 8px; color:#333;'>[Tiêu đề phần 1]</h3>
                <p style='margin:8px 0; line-height:1.5;'>[Tối thiểu 300 từ phân tích]</p>

                <h3 style='margin:12px 0 8px; color:#333;'>[Tiêu đề phần 2]</h3>
                <p style='margin:8px 0; line-height:1.5;'>[Tối thiểu 300 từ phân tích]</p>

                <ul style='margin:8px 0; padding-left:20px;'>
                    <li><strong>Điểm chính 1:</strong> [Chi tiết]</li>
                    <li><strong>Điểm chính 2:</strong> [Chi tiết]</li>
                    <li><strong>Điểm chính 3:</strong> [Chi tiết]</li>
                </ul>
            </div>

            4. Highlights (ưu nhược điểm):
            <div style='margin:12px 0;'>
                <h3 style='margin:12px 0 8px; color:#333;'>Đánh giá chi tiết</h3>
                <p style='margin:8px 0; line-height:1.5;'>
                    <strong>Ưu điểm:</strong><br>
                    [Liệt kê và phân tích ưu điểm]
                </p>
                <p style='margin:8px 0; line-height:1.5;'>
                    <strong>Nhược điểm:</strong><br>
                    [Liệt kê và phân tích nhược điểm]
                </p>
            </div>

            5. Kết luận:
            <div style='background:#f8f9fa; padding:15px; border-radius:8px; margin:8px 0;'>
                <h3 style='margin:12px 0 8px; color:#333;'>Kết luận và Khuyến nghị</h3>
                <p style='margin:8px 0; line-height:1.5;'>[Tối thiểu 200 từ kết luận]</p>
                <p style='margin-top:8px; font-style:italic; color:#666;'>
                    * Lưu ý: [Các ghi chú quan trọng]
                </p>
            </div>

            Yêu cầu về nội dung:
            - Tổng độ dài tối thiểu 1500 từ
            - Mỗi phần phân tích phải chi tiết và đầy đủ
            - Sử dụng <strong> cho điểm quan trọng
            - Sử dụng <em> cho thuật ngữ chuyên môn
            - Đảm bảo spacing theo quy định";
}

    private function getContextSpecificPrompt($context)
{
    $contextPrompts = [
        'analysis' => "
                HƯỚNG DẪN PHÂN TÍCH:
                1. Luôn bắt đầu với tổng quan
                2. Phân tích các yếu tố chính
                3. Đưa ra số liệu cụ thể
                4. Dự báo các kịch bản
                5. Kết luận và khuyến nghị",

        'advice' => "
                HƯỚNG DẪN TƯ VẤN:
                1. Xác định rõ mục tiêu
                2. Liệt kê pros/cons
                3. Đề xuất các phương án
                4. Phân tích rủi ro
                5. Khuyến nghị cụ thể",

        'knowledge' => "
                HƯỚNG DẪN GIẢI THÍCH:
                1. Định nghĩa đơn giản
                2. Ví dụ thực tế
                3. Giải thích chi tiết
                4. Liên hệ thực tiễn
                5. Tài liệu tham khảo",

        'creative' => "
                HƯỚNG DẪN SÁNG TẠO:
                1. Đa dạng ý tưởng
                2. Tính thực tế
                3. Tính độc đáo
                4. Khả năng áp dụng
                5. Hướng phát triển"
    ];

    return $contextPrompts[$context] ?? "";
}

    private function buildSystemMessages($base_prompt, $format_prompt, $context_prompt, $lang)
{
    $messages = [
        [
            'role' => 'system',
            'content' => "Reply only in " . $lang . " language without any translations or explanations in other languages"
        ],
        [
            'role' => 'system',
            'content' => $base_prompt
        ],
        [
            'role' => 'system',
            'content' => $format_prompt
        ]
    ];

    if (!empty($context_prompt)) {
        $messages[] = [
            'role' => 'system',
            'content' => $context_prompt
        ];
    }

    return $messages;
}

    private function getOptimalParameters($context, $model)
{
    $baseParams = [
        'model' => $model,
        'stream' => true,
        'max_tokens' => 8000,
    ];

    $contextParams = [
        'analysis' => [
            'temperature' => 0.3,
            'presence_penalty' => 0.1,
            'frequency_penalty' => 0.2
        ],
        'advice' => [
            'temperature' => 0.5,
            'presence_penalty' => 0.2,
            'frequency_penalty' => 0.2
        ],
        'knowledge' => [
            'temperature' => 0.3,
            'presence_penalty' => 0.1,
            'frequency_penalty' => 0.1
        ],
        'creative' => [
            'temperature' => 0.8,
            'presence_penalty' => 0.3,
            'frequency_penalty' => 0.3
        ],
        'casual' => [
            'temperature' => 0.7,
            'presence_penalty' => 0.2,
            'frequency_penalty' => 0.2
        ]
    ];

    return array_merge(
        $baseParams,
        $contextParams[$context] ?? $contextParams['casual']
    );
}

    private function createStream($messages, $parameters)
{
    return $this->client->chat()->createStreamed($parameters + ['messages' => $messages]);
}

    private function handleError(Exception $e)
{
    Log::error('ChatAI Error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'context' => $this->getCurrentContext()
    ]);

    throw new DomainException(
        "Error Processing Request: " . $e->getMessage(),
        Response::HTTP_BAD_REQUEST,
        $e
    );
}

    private function getCurrentContext()
{
    try {
        return [
            'timestamp' => now(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
    } catch (Exception $e) {
        return [];
    }
}

    public function summarizeContent(string $content, string $project_name): ?string
    {
        $response = $this->client->chat()->create([
            'model' => 'o3-mini',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'As a professional AI assistant, your task is to convert the user-provided content into a concise, accurate, and relevant image generation prompt. Only return the prompt in English, keeping brand names unchanged, and without any additional explanation',
                ],
                [
                    'role' => 'user',
                    'content' => "
                        Convert the following content into a precise, professional, and concise image generation prompt focusing solely on the product: `$content`. Here’s an in-depth, step-by-step explanation.

                        Start by detailing the kind of art you want. Decide if it’s a photograph, drawing, sketch, oil painting, or 3D rendering. Then, clearly define the main focus of the piece. It could be a person, animal, object, or abstract concept.

                        Next, describe the specifics of the main elements of your image. Include information on colors, shapes, sizes, and textures to guide the tool in generating accurate details. Provide details on the artistic art form and style you have in mind by using keywords like “abstract,” “minimalist,” “expressionist,” or “surreal” to describe the aesthetics.

                        Finally, define the composition in as much detail as possible. Specify the school of art, lighting style, aspect ratio, ratios, and camera angles. Focus on the image, limiting text or characters appearing in the picture as much as possible. Otherwise, you can focus on the background and the context.

                        Say you require an image of a Viking placed against a modern background. Let’s look at the stages involved in writing the right prompt for this example:

                        Specify the format: Generate a photograph…
                        Describe the subject: Generate a photograph of a Viking warlord…
                        Add details: Generate a photograph of a Viking warlord holding an axe…
                        Describe the style: Generate a pop art style photograph of a Viking warlord holding an axe in a monochrome long shot…
                        Add additional details: Generate a pop art style photograph of a Viking warlord holding up an axe in a monochrome long shot standing in front of Times Square, New York.

                        Only return the image prompt without any unnecessary details including text inside. The results must be used entirely in English.",
                ],
            ],
        ]);

        return str_replace(['"', "'"], '', $response->choices[0]->message->content);
    }

    public function callGpt($systemPrompt, $userPrompt)
    {
        try {
            $response = $this->client->chat()->create([
                        'model' => 'gpt-4o',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemPrompt
                            ],
                            [
                                'role' => 'user',
                                'content' => $userPrompt
                            ]
                        ],
                        'temperature' => 0.7,
                    ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            report($e);
            throw new DomainException("Lỗi khi gọi chatGpt: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function streamContentResponse($messages, $system_prompt, $model = 'gpt-4o-mini')
    {
        try {
            $messages = array_merge([
                [
                    'role' => 'system',
                    'content' => $system_prompt
                ]
            ], $messages);
            $stream = $this->client->chat()->createStreamed([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 8000,
                'stream' => true
            ]);
            return $stream;
        } catch (\Exception $e) {
            throw new DomainException("Error Processing Request: " . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function generateTranslateStream(string $sentences, string $locale)
    {
        $language = config("chatgpt.languages.{$locale}");

        // Tạo prompt để yêu cầu chuyển đổi HTML sang Markdown và sau đó dịch
        $prompt = "Convert the following HTML content to Markdown format and then translate it into {$language}.

    IMPORTANT INSTRUCTION:
    1. First, convert the HTML content to well-formatted Markdown
    2. Then translate the Markdown content to {$language}
    3. Preserve all formatting including headings, bold, italic, links, lists, etc.
    4. Keep the original structure intact
    5. Return only the translated Markdown content (NOT HTML)

    HTML content to convert and translate:

    {$sentences}";

        try {
            $stream = $this->client->chat()->createStreamed([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a specialized HTML-to-Markdown converter and translator. You first convert HTML to proper Markdown format, then translate the content while maintaining all Markdown formatting. Your output should be clean, well-formatted Markdown that can be processed by the marked library.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ]
                ],
                'temperature' => 0.3,
                'stream' => true
            ]);

            return $stream;
        } catch (\Exception $e) {
            $user = auth()->user();
            Log::error('Translation stream failed for user ID: ' .$user->id ?? '' . '. Error: ' . $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Anthropic\Anthropic;
use Anthropic\Client;
use App\Helper\Helpers;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditHistory;
use Illuminate\Support\Facades\Storage;

class ChatClaudeService
{
    private $url = "";
    private $api_key = "";

    private $model = "";

    private $version = "";

    private Client $anthropic;

    public function __construct()
    {
        $this->url = trim(config('claude.url'));
        $this->api_key =  trim(config('claude.api_key'));
        $this->model =  trim(config('claude.chat_model'));
        $this->version =  trim(config('claude.version'));

        $headers = [
            'anthropic-version' => $this->version,
            'content-type' => 'application/json',
            'x-api-key' => $this->api_key
        ];

        $this->anthropic = Anthropic::factory()
            ->withHeaders($headers)
            ->make();
    }

    public function sendMessageStream(string $message, string $description, string $field, string $language, string $limit)
    {
        try {
            $stream = $this->anthropic->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Bạn là chuyên gia trong lĩnh vực " . $field . ". Hãy hỗ trợ tạo văn bản cho người dùng bằng cách trả lời chính xác theo chủ đề và mô tả yêu cầu. Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Trình bày dưới dạng liệt kê dễ nhìn, hết câu hãy xuống dòng dùng thẻ <br> dính sát với ký tự sau nó và không được có khoảng cách, tuyệt đối không được dùng ký tự \n. Chủ đề là " . $message . ". Nội dung cần mô tả là " . $description . ". Giới hạn nội dung không quá " . $limit . " từ và hãy trả lời bằng " . $language . ""
                    ]
                ]
            ]);

            return $stream;
        } catch (\Exception $e) {
            // Xử lý lỗi ở đây
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getResponseStream(string $prompt)
    {
        $stream = $this->anthropic->chat()->createStreamed([
            'model' => $this->model,
            'temperature' => 1,
            'max_tokens' => 8192,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);
        return $stream;
    }

    public function sendMessage(string $message, string $description, string $field, string $language, string $limit)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                [
                    "model" => $this->model,
                    "max_tokens" => 4000,
                    "temperature" => 0.7,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Bạn là chuyên gia trong lĩnh vực " . $field . ". Hãy hỗ trợ tạo văn bản cho người dùng bằng cách trả lời chính xác theo chủ đề và mô tả yêu cầu. Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Những phần cần liệt kê thì trình bày dưới dạng liệt kê dễ nhìn. hết câu, trước khi liệt kê và sau các ý liệt kê phải xuống dòng dùng thẻ <br> dính sát với ký tự sau nó và không được có khoảng cách, . Chủ đề là " . $message . ". Nội dung cần mô tả là " . $description . ". Giới hạn nội dung không quá " . $limit . " từ và hãy trả lời bằng " . $language . ""
                        ]
                    ]
                ]
            ),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->api_key,
                'anthropic-version: ' . $this->version,
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }


    public function sendRequestAI(string $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                [
                    "model" => $this->model,
                    "max_tokens" => 4000,
                    "temperature" => 0,
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => $message
                        ]
                    ]
                ]
            ),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->api_key,
                'anthropic-version: ' . $this->version,
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function streamMessage(string $promt)
    {
        try {
            $stream = $this->anthropic->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $promt
                    ]
                ]
            ]);

            return $stream;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function streamChat(array $messages)
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
            $stream = $this->anthropic->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => 0.7,
                'max_tokens' => 8000,
                'messages' => $messages,
                'system' => $system_prompt
            ]);

            return $stream;
        } catch (\Exception $e) {
            throw $e;
            //            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMessageCustom(string $promt, $featureId = 0, $sreenId = 0)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                [
                    "model" => $this->model,
                    "max_tokens" => 4000,
                    "temperature" => 0.7,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $promt
                        ]
                    ]
                ]
            ),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->api_key,
                'anthropic-version: ' . $this->version,
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $responseData = json_decode($response);
        curl_close($curl);
        Log::info(json_encode($response));
        $user = Auth::user();
        $totalCredit = Helpers::calculateCredit($this->model, $responseData->usage->input_tokens, $responseData->usage->output_tokens, 0, 0);

        if ($user->credit >= $totalCredit) {
            $user->credit -= $totalCredit;
        } else {
            $user->credit = 0;
        }
        $user->save();
        CreditHistory::create(
            [
                "user_id" => $user->id,
                "screen_id" => $sreenId,
                "credit" => $totalCredit,
                'feature_id' => $featureId
            ]
        );
        return $response;
    }

    public function sendMessageStreamVideo(string $topic, int $page)
    {
        try {
            $stream = $this->anthropic->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => 0.7,
                'max_tokens' => 8192,
                'messages' => [
                    [
                        "role" => "user",
                        "content" => 'Bạn sẽ đóng vai trò là một trợ lý AI thông minh, có khả năng tìm kiếm và tạo nội dung bài giảng. Bạn sẽ cung cấp cho người dùng nội dung bài giảng theo từng trang bằng tiếng Việt.
                        Người dùng sẽ cung cấp một đoạn nội dung liên quan đến bài giảng, bạn hãy điều chỉnh, bổ sung các thông tin liên quan nếu cần thiết.
                        Hãy trả về theo format của json array, trong đó mỗi phần tử là một object đại diện cho một slide với cấu trúc như sau:
                        {
                            "title": string,
                            "descriptions": [
                                {
                                    "sub_title": string,
                                    "description": string
                                },
                                ...
                            ],
                            "keyword": string
                        }
                        Mỗi slide chỉ có 1 trong mảng "descriptions", keyword phải là tiếng Anh. Nội dung người dùng cần tạo slide là: ```' . $topic . '``` với ' . (string)$page . 'slide. Hãy trả về kết quả theo đúng format json đã yêu cầu và không cần mô tả gì thêm.'
                    ]
                ]
            ]);

            return $stream;
        } catch (\Exception $e) {
            // Xử lý lỗi ở đây
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMessageStreamVideoV2(string $topic, int $page, $des = '')
    {
        try {
            $stream = $this->anthropic->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => 0.7,
                'max_tokens' => 8192,
                'messages' => [
                    [
                        "role" => "user",
                        "content" => 'Bạn đóng vai trò là 1 trợ lý AI thông minh có khả năng viết kịch bản video và tạo ra các phân cảnh bằng tiếng việt,
                        Người dùng sẽ cung cấp một đoạn nội dung liên quan đến kịch bản video, hãy điều chỉnh bổ sung các thông tin liên quan.
                        Hãy trả về theo format json array, trong đó mỗi phần tử là một object đại diện cho một phân cảnh với cấu trúc như sau:
                        {
                            "title": string,
                            "descriptions": [
                                {
                                    "sub_title": yêu cầu nó mô tả chi tiết khoảng 200 từ,
                                    "description": string
                                },
                                ...
                            ],
                            "keyword": string
                        }
                        Mỗi slide chỉ có 1 trong mảng "descriptions",' . $des . ' keyword phải là tiếng Anh. Nội dung người dùng cần tạo slide là: ```' . $topic . '``` với ' . (string)$page . 'slide. Hãy trả về kết quả theo đúng format json đã yêu cầu và không cần mô tả gì thêm.'
                    ]
                ]
            ]);

            return $stream;
        } catch (\Exception $e) {
            // Xử lý lỗi ở đây
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMessageClaude($promt, $system_prompt = '')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                [
                    "model" => $this->model,
                    "max_tokens" => 4000,
                    "temperature" => 0.7,
                    'messages' => $promt,
                    'system' => $system_prompt,
                ]
            ),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->api_key,
                'anthropic-version: ' . $this->version,
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function claudeAsk($messages)
    {
        try {
            $system_prompt = "Chỉ trả về nội dung, không có kí tự đặc biệt nào ngoài text";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    "model" => $this->model,
                    "max_tokens" => 4000,
                    "temperature" => 0.7,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $messages
                        ]
                    ],
                    'system' => $system_prompt,
                ]),
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: ' . $this->api_key,
                    'anthropic-version: ' . $this->version,
                    'content-type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            $result = json_decode($response, true);
            $content = $result['content'][0]['text'];
            curl_close($curl);
            return $content;
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

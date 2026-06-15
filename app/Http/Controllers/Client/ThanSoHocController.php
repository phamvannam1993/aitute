<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ThanSoHocService;
use App\Services\ChatGPTStreamingService;
use App\Exceptions\UsageLimitExceededException;
use App\Services\ChatClaudeService;

class ThanSoHocController extends Controller
{
    protected $thanSoHocService;
    protected $chatGPTStreamingService;
    protected $chatClaudeService;

    public function __construct(ThanSoHocService $thanSoHocService, ChatGPTStreamingService $chatGPTStreamingService, ChatClaudeService $chatClaudeService)
    {
        $this->thanSoHocService = $thanSoHocService;
        $this->chatGPTStreamingService = $chatGPTStreamingService;
        $this->chatClaudeService = $chatClaudeService;
    }

    private function send($event, $data)
    {
        try {
            echo "data: " . json_encode([
                'type' => $event,
                'content' => $data
            ]) . "\n\n";
            ob_flush();
            flush();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function getThanSoHocInfo(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'birthdate' => 'required|date_format:d/m/Y',
        ], [
            'fullname.required' => 'Họ tên là bắt buộc',
            'fullname.string' => 'Họ tên phải là chuỗi ký tự',
            'fullname.max' => 'Họ tên không được vượt quá 255 ký tự',
            'birthdate.required' => 'Ngày sinh là bắt buộc',
            'birthdate.date_format' => 'Ngày sinh phải có định dạng dd/mm/yyyy',
        ]);

        return response()->stream(function () use ($validated) {
            try {
                $user = auth()->user();
                // Add authentication check
                if (!$user) {
                    $this->send('error', 'Vui lòng đăng nhập để sử dụng tính năng này.');
                    return;
                }
                // Get numerology data
                $numerologyData = $this->thanSoHocService->getThanSoHocInfo(
                    $validated['fullname'],
                    $validated['birthdate']
                );

                $thanSoHoc = $this->thanSoHocService->saveNumerologyResult(
                    $validated['fullname'],
                    $validated['birthdate'],
                    $numerologyData['message'],
                    $user->id
                );

                // Build prompt for ChatGPT
                $prompt = $this->thanSoHocService->buildChatGPTPrompt(
                    $numerologyData['message'],
                    $validated['fullname'],
                    $validated['birthdate']
                );

                Log::info($prompt);

                // Get streaming response from ChatGPT
                // $stream = $this->chatGPTStreamingService->streamMessage($prompt);
                $stream = $this->chatClaudeService->getResponseStream($prompt);

                // Send start message
                $this->send('start', "Đang phân tích dữ liệu thần số học của bạn...\n\n");

                // Stream the content
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content ?? '';
                    if (strlen($text) > 0) {
                        $this->send('content', $text);
                    }
                }

                // Send end message
                $this->send('end', "\n\nPhân tích hoàn tất!");
            } catch (UsageLimitExceededException $e) {
                $this->send('error', 'Bạn đã hết lượt sử dụng. Vui lòng nâng cấp tài khoản.');
            } catch (\Exception $e) {
                Log::error('Error in ThanSoHocController:', [
                    'message' => $e->getMessage(),
                    'fullname' => $validated['fullname'],
                    'birthdate' => $validated['birthdate']
                ]);
                $this->send('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    public function chat(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'message' => 'required|string',
            'context' => 'required|array',
            'context.fullName' => 'required|string',
            'context.birthDate' => 'required|string',
            'context.numerologyData' => 'nullable'
        ]);

        try {
            // Tạo prompt với context
            $prompt = "Dựa trên thông tin thần số học của {$validated['context']['fullName']}
                      (sinh ngày {$validated['context']['birthDate']}),
                      hãy trả lời câu hỏi sau: {$validated['message']}

                      Thông tin thần số học: " . json_encode($validated['context']['numerologyData']);

            // Sử dụng streaming service để trả về response
            return response()->stream(function () use ($prompt) {
                // Start message
                echo "data: " . json_encode([
                    'type' => 'start',
                    'content' => "Đang xử lý câu hỏi của bạn...\n\n"
                ]) . "\n\n";
                ob_flush();
                flush();

                // Get streaming response from ChatGPT
                $stream = $this->chatClaudeService->getResponseStream($prompt);
                // chat with claude
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content ?? '';
                    if (strlen($text) > 0) {
                        echo "data: " . json_encode([
                            'type' => 'content',
                            'content' => $text
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }

                // End message
                echo "data: " . json_encode([
                    'type' => 'end',
                    'content' => "\n\nĐã trả lời xong!"
                ]) . "\n\n";
                ob_flush();
                flush();
            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ThanSoHoc chat:', [
                'message' => $e->getMessage(),
                'user_message' => $validated['message'],
                'context' => $validated['context']
            ]);

            return response()->stream(function () use ($e) {
                echo "data: " . json_encode([
                    'type' => 'error',
                    'content' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]) . "\n\n";
            }, 500, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AIPricing;
use App\Models\User;
use App\Repositories\VideoToTextHistoryRepository;
use App\Services\ChatGPTService;
use App\Services\TokenCounter;
use GuzzleHttp\Client;use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\VideoToTextHistory;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\Helper\DocReaderService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoToTextController extends Controller
{

    protected $apiKey;
    protected $baseUrl;
    private $chatGPTService;
    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->apiKey = config('services.dify_video_to_text.api_key');
        $this->baseUrl = config('services.dify_video_to_text.base_url');
        $this->chatGPTService = $chatGPTService;
    }

    public function sendChatStreaming(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'query' => 'required|string',
                'files.*' => 'file|max:10240',
                'conversation_id' => 'nullable|string',
                'local_conversation_id' => 'nullable|string',
                'inputs' => 'nullable|string'
            ]);

            $difyConversationId = $request->input('conversation_id') ?? '';
            $queryInput = $request->input('query');
            $queryData = null;
            try {
                $queryData = json_decode($queryInput, true);
                if ($queryData === null && $queryInput !== null) {
                    $queryData = $queryInput;
                }
            } catch (\Exception $e) {
                $queryData = $queryInput;
            }

            return response()->stream(
                function () use ($request, $queryData, $difyConversationId) {
                    $client = new \GuzzleHttp\Client();
                    $buffer = '';
                    $fullT = '';

                    try {
                        $response = $client->post($this->baseUrl . '/v1/chat-messages', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $this->apiKey,
                                'Content-Type' => 'application/json',
                                'Accept' => 'text/event-stream'
                            ],
                            'json' => [
                                'inputs' => new \stdClass(),
                                'query' => $queryData,
                                'user' => "UserVideoToText",
                                'conversation_id' => $difyConversationId ?? "",
                                'response_mode' => 'streaming',
                            ],
                            'stream' => true,
                            'timeout' => 300,
                            'decode_content' => true,
                        ]);

                        $body = $response->getBody();
                        while (!$body->eof()) {
                            $chunk = $body->read(1024);
                            if (empty($chunk)) continue;
                            $buffer .= $chunk;
                            while (($pos = strpos($buffer, "data: ")) !== false) {
                                $endPos = strpos($buffer, "\n\n", $pos);
                                if ($endPos === false) break;

                                $line = substr($buffer, $pos, $endPos - $pos);
                                $buffer = substr($buffer, $endPos + 2);

                                if (str_starts_with($line, 'data: ')) {
                                    $jsonStr = preg_replace('/^data:\s*/', '', $line);
                                    $data = json_decode($jsonStr, true);

                                    if (isset($data['event'])) {
                                        echo $line . "\n\n";
                                        if (ob_get_level() > 0) ob_end_flush();
                                        flush();

                                        if ($data['event'] === 'message' && isset($data['answer'])) {
                                            $fullT .= $data['answer'];
                                        } else if ($data['event'] === 'workflow_finished') {
                                            if (isset($data['data']['outputs']['answer'])) {
                                                // todo
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        echo "data: " . json_encode([
                                'event' => 'error',
                                'message' => 'API Error: ' . $e->getMessage()
                            ]) . "\n\n";

                        if (ob_get_level() > 0) ob_end_flush();
                        flush();

                        // Log the error for server-side debugging
                        Log::error('Dify API error: ' . $e->getMessage(), [
                            'conversation_id' => $conversation->id ?? '',
                        ]);
                        throw $e;
                    }
                },
                200,
                [
                    'Content-Type' => 'text/event-stream',
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Connection' => 'keep-alive',
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('SendChatStreaming error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function translateContent(Request $request)
    {
        $content = $request->input('content') ?? '';
        $language = $request->input('language');
        try {
            return $this->chatGPTService->generateTranslate($content, $language);
        } catch (Exception $e) {
            return [
                'message' => 'Lỗi khi translate.',
            ];
        }
    }

    public function translateContentStream(Request $request)
    {
        $content = $request->input('content') ?? '';
        $language = $request->input('language');

        try {
            $stream = $this->chatGPTService->generateTranslateStream($content, $language);

            return response()->stream(function () use ($stream) {
                // Bắt đầu output buffering
                ob_start();

                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content ?? '';
                    if (strlen($text) > 0) {
                        echo $text;

                        // Đảm bảo buffer đã được bắt đầu trước khi flush
                        if (ob_get_level() > 0) {
                            ob_flush();
                            flush();
                        } else {
                            flush();
                        }
                    }
                }

                // Kết thúc và xóa bất kỳ buffer nào còn sót lại
                if (ob_get_level() > 0) {
                    ob_end_flush();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi translate: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
    */
    public function exportToDoc(DocReaderService $docReaderService, Request $request):BinaryFileResponse|JsonResponse {
        try {
            $content = $request->get('content');
            $tempFile = $docReaderService->exportToDoc($content);

            return response()->download($tempFile)->deleteFileAfterSend();
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function saveHistory(VideoToTextHistoryRepository $videoToTextHistoryRepository, Request $request) {
        $data = $request->only(['content', 'youtube_url', 'video_title', 'audio_url']);
        $data['user_id'] = $request->user()->id;

        return $videoToTextHistoryRepository->create($data);
    }

    public function getListHistory(VideoToTextHistoryRepository $videoToTextHistoryRepository, Request $request) {
        return response()->json(
            $videoToTextHistoryRepository->getListByUserId($request->user()->id),
            200,
            [],
            JSON_INVALID_UTF8_IGNORE
        );
    }

    public function delete($id) {
        $user_id =  auth()->id();
        $record = VideoToTextHistory::where('id', $id)->where('user_id', $user_id)->first();
        if($record) {
            VideoToTextHistory::where('id', $record->id)->delete();
            return response()->json(['success' => true, 'message' => 'Xóa link video thành công']);
        } else {
            return response()->json(['success' => true, 'message' => 'Không tồn tại dữ liệu']);
        }
    }
    private function getYouTubeVideoID($url) {
        if (preg_match('/(?:youtu\.be\/|(?:www\.|m\.)?youtube\.com\/(?:(?:v|e|u|embed|shorts)\/|.*[?&]v=))([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        parse_str(parse_url($url, PHP_URL_QUERY), $query);

        return isset($query['v']) ? $query['v'] : null;
    }

    private function getShortYouTubeVideoID($url)
    {
        $regex = '/^(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?.*)?$/';
        if (preg_match($regex, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getVideoId($url)
    {
        $id = $this->getYouTubeVideoID($url);
        if (!$id) {
            $id = $this->getShortYouTubeVideoID($url);
        }

        return $id;
    }

    public function convertMp3() {
        $client = new Client();
        try {
            $response = $client->request('GET', env('RAPIDAPI_API_URL') . request('url'), [
                'headers' => [
                    'x-rapidapi-host' => env('RAPIDAPI_HOST'),
                    'x-rapidapi-key' => env('RAPIDAPI_KEY'),
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);

            return response()->json(['url' => $result['downloadUrl'] ?? '', 'title' => $result['title'] ?? '']);
        } catch(Exception $e) {
            $videoId = $this->getYouTubeVideoID(request('url'));
            $response = $client->request('GET', env('RAPIDAPI_API_URL_BACKUP') . $videoId, [
                'headers' => [
                    'x-rapidapi-host' => env('RAPIDAPI_HOST_BACKUP'),
                    'x-rapidapi-key' => env('RAPIDAPI_KEY_BACKUP'),
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);

            return response()->json(['url' => $result['linkDownload'] ?? '', 'title' => $result['title'] ?? '']);
        }
    }

    public function downloadMp3()
    {
        $remoteUrl = request('downloadUrl');
        $fileName =  request('title') . 'mp3';

        $response = Http::withOptions(['stream' => true])->get($remoteUrl);

        if ($response->successful()) {
            return new StreamedResponse(function () use ($response) {
                echo $response->body();
            }, 200, [
                'Content-Type' => $response->header('Content-Type', 'application/octet-stream'),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        return response('Không tải được file.', 500);
    }
}

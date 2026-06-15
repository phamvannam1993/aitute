<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AIBusinessProject;
use App\Repositories\AIBusinessProjectRepository;
use App\Repositories\SendChatHistoriesRepository;
use App\Services\Dify\DifyService;
use App\Services\DifyBackup\DifyBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AIDifyController extends Controller
{

    protected $apiKey;
    protected $apiKeyV2;
    protected $baseUrl;

    public function __construct(
        private readonly DifyBackupService $difyBackupService,
        private readonly DifyService $difyService,
        private readonly SendChatHistoriesRepository $sendChatHistories,
        private readonly AIBusinessProjectRepository $aiBusinessProjectRepository)
    {
        $this->apiKey = config('services.dify.api_key');
        $this->apiKeyV2 = config('services.dify.api_key_v2');
        $this->baseUrl = config('services.dify.base_url');
    }

    public function index()
    {
        return Inertia::render('Client/AiDify/Index');
    }

    public function sendChat(Request $request)
    {
        $user = auth()->user();
        $inputs = empty($request->input('inputs')) ? new \stdClass() : $request->input('inputs');
        try {
            $params = [
                'inputs' => $inputs,
                'query' => $request->input('query', ''),
                'files' => $request->input('files', []),
                'response_mode' => 'blocking',
                'user' => 'user_' . $user->id ?? 0,
                'conversation_id' => $request->input('conversation_id'),
            ];

            $projectId = $request->input('project_id');
            $aiBusinessProject = AIBusinessProject::query()->where('id', $projectId)->first();
            $query = json_decode($request->input('query', ''), true, 512, JSON_THROW_ON_ERROR);
            $this->difyBackupService->updateMetadata($aiBusinessProject, $query);
            $currentStep = $query['currentStep'] ?? '';

            $attempts = 0;
            $maxAttempts = 1;
            if ($currentStep === 'buoc3') {
                $maxAttempts = 2;
            }

            if ($this->exitSendChatHistories($params['conversation_id'])) {
                $maxAttempts = 0; // Khi dùng backup flow thì không gọi dify
            }

            $response = null;
            while ($attempts < $maxAttempts) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . config('services.self_host_dify.api_key'),
                        'Content-Type' => 'application/json; charset=utf-8',
                    ])
                        ->timeout(300)
                        ->post(config('services.self_host_dify.base_url') . '/v1/chat-messages', $params);

                    if ($response->successful()) {
                        Log::info('Call dify thanh cong', [
                            $response
                        ]);
                        // Lấy total_tokens từ response
                        $totalTokens = $response->json('metadata.usage.total_tokens', 0);
                        $totalInputTokens = $response->json('metadata.usage.prompt_tokens', 0);
                        $totalOutputTokens = $response->json('metadata.usage.completion_tokens', 0);
                        Log::info('Dify metadata:', [
                            'answer' =>  $response->json('answer'),
                            'params' => json_encode($params),
                            'messages' => $response->json('metadata.usage')
                        ]);
                        // Trừ credit cho user
                        //                if ($totalTokens > 0) {
                        //                    Auth::user()->chargeCredit('business', '', null, 'ai-business', '', 30, 30, $totalInputTokens, $totalOutputTokens);
                        //                }
                        // $this->difyBackupService->updateMetadata($aiBusinessProject, $query, $response->json('answer'));

                        return response()->json([
                            'answer' => $response->json('answer'),
                            'conversation_id' => $response->json('conversation_id'),
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::error('Dify error:', [
                        'message' => $exception->getMessage(),
                    ]);
                }

                $attempts++;
            }

            if (!$response?->successful()) {
                Log::info('Dify params:', [
                    'params' => json_encode($params, JSON_THROW_ON_ERROR),
                ]);
//                $aiBusinessProject->update(['is_use_backup_flow' => true]);
//
//                $answer = $this->difyBackupService->handle($query, $aiBusinessProject);
                    $this->sendChatHistories->updateOrCreate(
                        [
                            'dify_conversation_id' => $params['conversation_id'],
                        ],
                        [
                            'dify_conversation_id' => $params['conversation_id'],
                            'project_id' => $projectId,
                            'is_dify_backup' => true,
                        ]
                    );
                    $project = $this->aiBusinessProjectRepository->find($projectId);
                    if ($project) {
                        $params['conversation_id'] = null;
                        $params['query'] = $this->difyService->getDataSenChat($params['query'], $project);
                    }
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json; charset=utf-8',
                    ])
                        ->timeout(300)
                        ->post($this->baseUrl . '/v1/chat-messages', $params);
                    $answer = $response->successful() ? $response->json('answer') : null;
                // $this->difyBackupService->updateMetadata($aiBusinessProject, $query, $answer);
                return response()->json([
                    'answer' => $answer,
                    'conversation_id' => $request->input('conversation_id')
                ]);
            }

            return response()->json([
                'error' => $response?->body()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exitSendChatHistories($conversationId)
    {
        return $this->sendChatHistories->findByFilter(['dify_conversation_id' => $conversationId, 'is_dify_backup' => true]);
    }

    public function sendChatStreaming(Request $request)
    {
        $user = auth()->user();
        $inputs = empty($request->input('inputs')) ? new \stdClass() : $request->input('inputs');
        try {
            $params = [
                'inputs' => $inputs,
                'query' => $request->input('query', ''),
                'files' => $request->input('files', []),
                'user' => 'user_' . $user->id ?? 0,
                'conversation_id' => $request->input('conversation_id'),
                'response_mode' => 'streaming',
                'project_id' => request('project_id'),
            ];

            $projectId = $request->input('project_id');
            $aiBusinessProject = AIBusinessProject::query()->where('id', $projectId)->first();
            $this->difyBackupService->generateMetadata($aiBusinessProject);
            return response()->stream(
                function () use ($params) {
//                    $client = new \GuzzleHttp\Client();
//                    $response = $client->post($this->baseUrl . '/v1/chat-messages', [
//                        'headers' => [
//                            'Authorization' => 'Bearer ' . $this->apiKey,
//                            'Content-Type' => 'application/json',
//                        ],
//                        'json' => $params,
//                        'stream' => true,
//                        'timeout' => 300,
//                    ]);
                    $options = [
                        'json' => $params,
                        'stream' => true,
                        'timeout' => 300,
                    ];
                    if ($this->exitSendChatHistories($params['conversation_id'])) {
                        $response = $this->difyService->sendChatSelfHost($options);
                    } else {
                        $response = $this->difyService->sendChat($options);
                    }

                    if (ob_get_level() === 0) {
                        ob_start();
                    }

                    $body = $response['response']->getBody();
                    $buffer = '';
                    $isSaveSendChatHistories = true;
                    while (!$body->eof()) {
                        $line = $body->read(1024);
                        $buffer .= $line;
                        echo $line;
                        while (($pos = strpos($buffer, "data: ")) !== false) {
                            $endPos = strpos($buffer, "\n\n", $pos);
                            if ($endPos === false) break;

                            $line = substr($buffer, $pos, $endPos - $pos);
                            $buffer = substr($buffer, $endPos + 2);

                            if (str_starts_with($line, 'data: ')) {
                                $jsonStr = preg_replace('/^data:\s*/', '', $line);
                                $data = json_decode($jsonStr, true);

                                if (isset($data['conversation_id']) && $isSaveSendChatHistories) {
                                    $this->sendChatHistories->updateOrCreate(
                                        [
                                            'dify_conversation_id' => $data['conversation_id'],
                                        ],
                                        [
                                            'dify_conversation_id' => $data['conversation_id'],
                                            'project_id' => $params['project_id'],
                                            'is_dify_backup' => $response['meta_data']['is_dify_backup'] ?? false,
                                        ]
                                    );
                                    $isSaveSendChatHistories = false;
                                }
                            }
                        }
                        ob_flush();
                        flush();
                    }

                    if (ob_get_level() > 0) {
                        ob_end_flush();
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
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendChatStreamingExpert(Request $request)
    {
        $user = auth()->user();
        try {
            $params = [
                'inputs' => $request->input('inputs', []),
                'query' => $request->input('query', ''),
                'files' => $request->input('files', []),
                'user' => 'user_' . $user->id ?? 0,
                'conversation_id' => $request->input('conversation_id'),
                'response_mode' => 'streaming',
                'project_id' => request('project_id'),
            ];

            return response()->stream(
                function () use ($params) {
//                    $client = new \GuzzleHttp\Client();
//                    $response = $client->post($this->baseUrl . '/v1/chat-messages', [
//                        'headers' => [
//                            'Authorization' => 'Bearer ' . $this->apiKeyV2,
//                            'Content-Type' => 'application/json',
//                        ],
//                        'json' => $params,
//                        'stream' => true,
//                        'timeout' => 300,
//                    ]);
                    $options = [
                        'json' => $params,
                        'stream' => true,
                        'timeout' => 300,
                        'is_expert' => true,
                    ];
                    if ($this->exitSendChatHistories($params['conversation_id'])) {
                        $response = $this->difyService->sendChatSelfHost($options);
                    } else {
                        $response = $this->difyService->sendChat($options);
                    }

                    if (ob_get_level() === 0) {
                        ob_start();
                    }

                    $body = $response['response']->getBody();
                    while (!$body->eof()) {
                        echo $body->read(1024);
                        ob_flush();
                        flush();
                    }

                    if (ob_get_level() > 0) {
                        ob_end_flush();
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
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    // Method nay ap dung o local doi voi 1 so may ko chay dc streaming o local
    public function sendChatStreaming_Local(Request $request)
    {
        $user = auth()->user();
        try {
            $params = [
                'inputs' => $request->input('inputs', []),
                'query' => $request->input('query', ''),
                'files' => $request->input('files', []),
                'user' => 'user_' . $user->id ?? 0,
                'conversation_id' => $request->input('conversation_id'),
                'response_mode' => 'streaming',
            ];

            // Set headers trước khi bắt đầu stream
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            // Disable output buffering
            if (ob_get_level()) ob_end_clean();
            while (@ob_end_flush());

            // Set unlimited time limit
            set_time_limit(0);
            ignore_user_abort(true);

            $client = new \GuzzleHttp\Client([
                'timeout' => 0, // Disable timeout
                'verify' => false // Skip SSL verify if needed
            ]);

            $response = $client->post($this->baseUrl . '/v1/chat-messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'text/event-stream'
                ],
                'json' => $params,
                'stream' => true,
                'decode_content' => true,
                'buffer' => false
            ]);

            $body = $response->getBody();

            // Stream response trực tiếp
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                if (empty($chunk)) continue;

                echo $chunk;
                flush();

                if (connection_aborted()) {
                    break;
                }
            }

            return response('', 200); // Return empty response when done

        } catch (\Exception $e) {
            Log::error('Streaming error: ' . $e->getMessage());

            // Return error as SSE format
            echo "data: " . json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]) . "\n\n";
            flush();

            return response('', 200);
        }
    }
}

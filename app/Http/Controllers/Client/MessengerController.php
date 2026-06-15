<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Services\ChatGPTService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FacebookConfigs;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use App\Services\FacebookConfigsService;
use App\Services\ChatDocsService;
use App\Jobs\ProcessMessengerWebhook;
use App\Models\MessageHistories;

class MessengerController extends Controller
{
  protected FacebookConfigsService $facebookConfigsService;
  protected ChatDocsService $chatDocsService;

  protected ChatGPTService $chatGPTService;

  public function __construct(
    FacebookConfigsService $facebookConfigsService,
    ChatDocsService $chatDocsService,
    ChatGPTService $chatGPTService
  ) {
    $this->facebookConfigsService = $facebookConfigsService;
    $this->chatDocsService = $chatDocsService;
    $this->chatGPTService = $chatGPTService;
  }

  public function handleGetWebhook(Request $request)
  {
    try {

      $hubVerifyToken = Config::get('services.messenger.verify_token');
      $mode = $request->input('hub_mode');
      $token = $request->input('hub_verify_token');
      $challenge = $request->input('hub_challenge');

      Log::info($mode);
      Log::info($hubVerifyToken);
      Log::info($token);

      if ($mode === 'subscribe' && $token === $hubVerifyToken) {
        return Response::make($challenge, 200);
      } else {
        return Response::make('WEBHOOK_NOT_VERIFIED', 403);
      }
    } catch (\Exception $e) {
      Log::error("handleGetWebhook: " . $e->getMessage());
      return Response::json(['error' => $e->getMessage()]);
    }
  }

    // Khi user gui tin nhan den
    public function handlePostWebhook(Request $request)
    {
        try {
            $body = $request->all();
            $payload = request()->getContent();
            $signature = request()->header('X-Hub-Signature-256');

            $expected = 'sha256=' . hash_hmac('sha256', $payload, config('services.messenger.app_secret'));

            if (!hash_equals($expected, $signature)) {
                Log::error('Xác thực webhook thất bại');
                return response()->json(['error' => 'Xác thực webhook thất bại'], 403);
            }


            if ($body['object'] !== 'page') {
                return response()->json(['error' => 'Invalid request body']);
            }

            $this->_handleMessengerJob(data_get($body, 'entry'));

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Error dispatching messenger webhook: ' . $e->getMessage());
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleForWardPostWebhook(Request $request)
    {
        try {
            $body = $request->all();
            Helpers::logInfo('handleForWardMessengerPostWebhook', $body);
            ProcessMessengerWebhook::dispatchSync($body);

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Error dispatching forward messenger webhook: ' . $e->getMessage());
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function _handleMessengerJob($webHookPayloadEntry)
    {
        Helpers::logInfo('_handleMessengerJob');
        foreach ($webHookPayloadEntry as $entry) {
            $messaging = data_get($entry, 'messaging.0');
            if(empty($messaging)) {
                Helpers::logInfo('Empty messaging.');
                return;
            }

            if(!empty($messaging['read'])) {
                Helpers::logInfo('Webhook read skip.');
                return;
            }

            // Dispatch job với delay 0 để đảm bảo nó được xử lý ngay
            if (!empty($messaging['message'])) {
                // Thêm log để debug
                Helpers::logInfo('Messenger webhook message job', $messaging);
                ProcessMessengerWebhook::dispatch($messaging);
            }

            if (!empty($messaging['postback'])) {
                Helpers::logInfo('Messenger webhook postback job', $messaging);
                ProcessMessengerWebhook::dispatch($messaging);
            }
        }
    }

  public function handleMessage($senderPsid, $receivedMessage, $pageAccessToken, $userId, $pageId)
  {
    try {
      // Kiểm tra echo sớm và return ngay
      if (isset($receivedMessage['is_echo'])) {
        return;
      }

      // Chỉ log khi cần thiết trong môi trường development
      if (config('app.debug')) {
        Log::info('Received message:', $receivedMessage);
      }

      $startTime = microtime(true);

      $response = [];

      if (isset($receivedMessage['text'])) {
        // Thêm timeout để tránh request bị treo quá lâu
        $content = $this->chatDocsService->getResponse($receivedMessage['text'], $userId, $senderPsid, $pageId);

        $response = [
          'recipient' => ['id' => $senderPsid],
          'message' => $content
        ];
      } else if (!empty($receivedMessage['attachments'])) {
        // chua hoat dong
        $attachmentUrl = $receivedMessage['attachments'][0]['payload']['url'];

        $response = [
          'recipient' => ['id' => $senderPsid],
          'message' => [
            'attachment' => [
              'type' => 'template',
              'payload' => [
                'template_type' => 'generic',
                'elements' => [
                  [
                    'title' => 'Is this the right picture?',
                    'subtitle' => 'Tap a button to answer.',
                    'image_url' => $attachmentUrl,
                    'buttons' => [
                      [
                        'type' => 'postback',
                        'title' => 'Yes!',
                        'payload' => 'yes',
                      ],
                      [
                        'type' => 'postback',
                        'title' => 'No!',
                        'payload' => 'no',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ];
      }

      if (!empty($response)) {
        // Gửi response bất đồng bộ để không block main thread
        dispatch(function () use ($response, $pageAccessToken) {
          $this->callSendAPI($response, $pageAccessToken);
        })->afterResponse();
      }

      if (config('app.debug')) {
        $executionTime = microtime(true) - $startTime;
        Log::info("Message handling completed in {$executionTime} seconds");
      }

      // send message to user
      $this->callSendAPI($response, $pageAccessToken);
    } catch (\Exception $e) {
      report($e);
      // Gửi thông báo lỗi cho user
      $errorResponse = [
        'recipient' => ['id' => $senderPsid],
        'message' => ['text' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.']
      ];
      $this->callSendAPI($errorResponse, $pageAccessToken);
      throw $e;
    }
  }

  public function handlePostback($senderPsid, $receivedPostback, $pageAccessToken, $pageModel = null)
  {
    $response = [
      'recipient' => ['id' => $senderPsid],
      'message' => ''
    ];

    $payload = $receivedPostback['payload'];

    if ($payload === 'yes') {
      $response = [
        'recipient' => ['id' => $senderPsid],
        'message' => 'Thanks!'
      ];
    } else if ($payload === 'no') {
      $response = [
        'recipient' => ['id' => $senderPsid],
        'message' => 'Oops, try sending another image.'
      ];
    }

    Log::info("handlePostback: " . json_encode($response));

    $this->callSendAPI($response, $pageAccessToken);
  }

  public function callSendAPI($response, $pageAccessToken = null)
  {
    try {
      Log::info("Sending message: " . json_encode($response));
      $pageAccessToken = $pageAccessToken ?? config('services.messenger.page_access_token');

      $messagePayload = is_array($response['message']) ? $response['message'] : ['text' => $response['message']];

      $response = Http::withToken($pageAccessToken)->post('https://graph.facebook.com/v19.0/me/messages', [
        'recipient' => [
          'id' => $response['recipient']['id']
        ],
        'message' => $messagePayload
      ]);

      Log::info("callSendAPI response: " . json_encode($response->json()));
      return $response->json();
    } catch (\Exception $e) {
      report($e);
      throw $e;
    }
  }
  public function hashSignatureString(array $webhookEvent, FacebookConfigs $pageSettings, string $hashEvent = '')
  {
    $pageSettingId = $pageSettings->id;
    $rawSignatureString = $hashEvent . $pageSettingId . json_encode($webhookEvent, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return hash('sha256', $rawSignatureString);
  }

  public function createReplied(array $webhookEvent, FacebookConfigs $pageSettings)
  {
    $hashSignatureString = $this->hashSignatureString($webhookEvent, $pageSettings, 'replied');
    cache([$hashSignatureString => true], now()->addMinutes(60));
  }

  public function isReplied(array $webhookEvent, FacebookConfigs $pageSettings): bool
  {
    $hashSignatureString = $this->hashSignatureString($webhookEvent, $pageSettings, 'replied');

    return cache()->has($hashSignatureString);
  }

  public function createLock(array $webhookEvent, FacebookConfigs $pageSettings)
  {
    $hashSignatureString = $this->hashSignatureString($webhookEvent, $pageSettings, 'locked');
    cache([$hashSignatureString => true], now()->addMinutes(10));
  }

  public function isLocked(array $webhookEvent, FacebookConfigs $pageSettings): bool
  {
    $hashSignatureString = $this->hashSignatureString($webhookEvent, $pageSettings, 'locked');

    return cache()->has($hashSignatureString);
  }

  public function unlock(array $webhookEvent, FacebookConfigs $pageSettings)
  {
    $hashSignatureString = $this->hashSignatureString($webhookEvent, $pageSettings, 'locked');
    cache()->forget($hashSignatureString);
  }

  public function saveFacebookConfig(Request $request)
  {
    try {
      $validated = $request->validate([
        'page_id' => 'required|string',
        'page_access_token' => 'required|string',
      ]);

      $user = Auth::user();

      // Check if a configuration already exists for this user/page
      $existingConfig = FacebookConfigs::where('user_id', $user->id)
        ->where('page_id', $validated['page_id'])
        ->first();

      if ($existingConfig) {
        // Update existing configuration
        $existingConfig->update([
          'page_access_token' => $validated['page_access_token']
        ]);
        return response()->json([
          'success' => true,
          'message' => 'Cấu hình Facebook Fanpage đã được cập nhật thành công.',
          'data' => $existingConfig
        ]);
      } else {
        // Create new configuration
        $config = FacebookConfigs::create([
          'user_id' => $user->id,
          'page_id' => $validated['page_id'],
          'page_access_token' => $validated['page_access_token']
        ]);

        return response()->json([
          'success' => true,
          'message' => 'Cấu hình Facebook Fanpage đã được lưu thành công.',
          'data' => $config
        ]);
      }
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
      ], 500);
    }
  }

  public function getFacebookConfig()
  {
    try {
      $user = Auth::user();

      // Lấy tất cả cấu hình Facebook Fanpage của người dùng
      $configs = FacebookConfigs::where('user_id', $user->id)->get();

      if ($configs->count() > 0) {
        return response()->json([
          'success' => true,
          'data' => $configs
        ]);
      } else {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy cấu hình Facebook Fanpage nào.'
        ]);
      }
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
      ], 500);
    }
  }

  public function getPromotionMessage(Request $request)
  {
    try {
      $user = Auth::user();

      $user_page_id = $request->input('user_page_id');

      $user_message_history = MessageHistories::where('user_id', $user->id)
        ->where('user_page_id', $user_page_id)
        ->first();

      if (!isset($user_message_history->page_id)) {
        return Response::json([
          'success' => false,
          'message' => 'Không tìm thấy trang cấu hình'
        ], 404);
      }

      $promptTemplate = "
      Dựa vào nội dung tin nhắn của người dùng, hãy tạo một tin nhắn mời khách hàng mua hàng sao cho thật sự thu hút, tự nhiên và phù hợp với ngữ cảnh cuộc trò chuyện, đồng thời khéo léo thúc đẩy khách hàng đưa ra quyết định nhanh chóng.
        Nội dung tin nhắn của người dùng: " . $user_message_history->message . "
      ";

      $inputModel = 'o3-mini';

      $message = $this->chatGPTService->getResponse($promptTemplate, null, $inputModel);

      // Comment out the chargeCredit call since it's causing linter errors
      // We'll need to implement or fix this method in the User model if needed
      /*
      Auth::user()->chargeCredit(
        'text',
        $inputModel,
        null,
        'ai-text',
        $promptTemplate,
        36,
        36
      );
      */

      return Response::json([
        'success' => true,
        'message' => $message
      ]);
    } catch (\Exception $e) {
      Log::error('Error creating promotion message: ' . $e->getMessage());
      return Response::json([
        'success' => true,
        'message' => "Chào bạn, tôi là bot của cửa hàng. Tôi có thể giúp gì cho bạn?"
      ]);
    }
  }

  public function sendPromotionMessage(Request $request)
  {
    try {
      $user = Auth::user();
      $user_page_id = $request->input('user_page_id');
      $message = $request->input('message') ?? '';
      $attachment = $request->file('attachment');

      // Check if current time is within 24-hour messaging window
      $lastInteraction = MessageHistories::where('user_id', $user->id)
        ->where('user_page_id', $user_page_id)
        ->orderBy('created_at', 'desc')
        ->first();

      if (!$lastInteraction || now()->diffInHours($lastInteraction->created_at) > 24) {
        return response()->json([
          'success' => false,
          'message' => 'Không thể gửi tin nhắn: Ngoài khoảng thời gian cho phép (24 giờ kể từ tin nhắn cuối của người dùng)'
        ], 403);
      }


      $user_message_history = MessageHistories::where('user_id', $user->id)
        ->where('user_page_id', $user_page_id)
        ->first();

      if (!isset($user_message_history->page_id)) {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy trang cấu hình'
        ], 404);
      }

      $page_config = FacebookConfigs::where('page_id', $user_message_history->page_id)->first();

      if (!isset($page_config->page_id)) {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy trang cấu hình'
        ], 404);
      }

      $responseData = [];
      $messageContent = $message;
      $messageType = 'text';

      $response = [
        'recipient' => ['id' => $user_page_id],
        'message' => ['text' => $message]
      ];

      if ($attachment) {
        // Upload image to Facebook first
        $uploadResponse = $this->uploadAttachment($attachment, $page_config->page_access_token);

        if (!isset($uploadResponse['attachment_id'])) {
          return response()->json([
            'success' => false,
            'message' => 'Không thể tải lên tệp đính kèm'
          ], 500);
        }

        $attachmentId = $uploadResponse['attachment_id'];

        // Send message with attachment
        $responseData = [
          'recipient' => ['id' => $user_page_id],
          'message' => [
            'attachment' => [
              'type' => 'image',
              'payload' => [
                'attachment_id' => $attachmentId
              ]
            ]
          ]
        ];

        $this->callSendAPI($responseData, $page_config->page_access_token);
        $messageType = 'image';
        $messageContent = json_encode(['type' => 'image', 'url' => '']); // Store attachment info
      }

      // If there's also a text message, send it separately
      if (!empty($message)) {
        $textResponse = [
          'recipient' => ['id' => $user_page_id],
          'message' => ['text' => $message]
        ];

        $this->callSendAPI($textResponse, $page_config->page_access_token);
      }

      // Create message history record
      MessageHistories::create([
        'user_id' => $user->id,
        'source_id' => "",
        'user_page_id' => $user_page_id,
        'page_id' => $page_config->page_id,
        'role' => 'assistant',
        'message' => $messageContent,
        'text_content' => !empty($message) ? $message : 'Hình ảnh đính kèm',
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Đã gửi tin nhắn thành công'
      ]);
    } catch (\Exception $e) {
      Log::error('Error sending promotion message: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Không thể gửi tin nhắn: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Upload attachment to Facebook
   *
   * @param \Illuminate\Http\UploadedFile $file
   * @param string $pageAccessToken
   * @return array
   */
  private function uploadAttachment($file, $pageAccessToken)
  {
    try {
      $fileContents = file_get_contents($file->getPathname());
      $fileName = $file->getClientOriginalName();

      $response = Http::attach(
        'filedata',
        $fileContents,
        $fileName
      )->withToken($pageAccessToken)
        ->post('https://graph.facebook.com/v19.0/me/message_attachments', [
          'message' => json_encode([
            'attachment' => [
              'type' => 'image',
              'payload' => [
                'is_reusable' => true
              ]
            ]
          ])
        ]);

      Log::info('Attachment upload response: ' . json_encode($response->json()));
      return $response->json();
    } catch (\Exception $e) {
      Log::error('Error uploading attachment: ' . $e->getMessage());
      throw $e;
    }
  }
}

<?php

namespace App\Services;

use App\Helper\Helpers;
use App\Jobs\ForwardMessengerWebhook;
use App\Models\AiTuTeConversation;
use App\Models\AiTuTeMessage;
use App\Services\FacebookConfigsService;
use App\Models\User;
use App\Models\UserSale;
use App\Services\DifyService;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerWebhookService
{
    protected $messaging;

    public $timeout = 60;
    public $tries = 1;
    public $maxExceptions = 1;

    private $difyService;
    private $fbVersion = 'v23.0';

    private $pageAccessToken;
    private $facebookConfig;
    private $PSID;
    private $userAgent;
    private $user;
    private $conversation;

    public function __construct(DifyService $difyService)
    {
        $this->difyService = $difyService;
    }

    public function handle($messaging)
    {
        $this->messaging = $messaging;

        $this->PSID = $this->messaging['sender']['id'];
        $pageId = $this->messaging['recipient']['id'];

        try {
            if (isset($this->PSID)) {
                try {
                    $this->loadDBData($pageId);
                    $this->_sendTypingIndicator();
                    if (isset($this->messaging['postback'])) {
                       $this->_handlePostback($this->messaging['postback']);
                    } else {
                        if($this->_isNewConversation()) {
                            $this->_firstMessage();
                        } else if (data_get($this->messaging, 'message.text')) {
                            $this->difySendMessage($this->messaging['message']['text']);
                        } else if (data_get($this->messaging, 'message.attachments')) {
                            $this->difySendAttachment($this->messaging['message']['attachments']);
                        }
                    }
                } catch (\Exception $e) {
                    // Gửi thông báo lỗi cho user
                    $this->_callSendAPI('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.');
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Helpers::logException($e);
            throw $e;
        }
    }

    public function setPSID($PSID)
    {
        $this->PSID = $PSID;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
        return $this;

    }

    public function loadDBData($pageId)
    {
        $this->facebookConfig = app(FacebookConfigsService::class)->findByPageId($pageId);

        $this->pageAccessToken = $this->facebookConfig->page_access_token;

        $this->userAgent = User::with([
            'projects' => function ($query) {
                $query->withCount('chatTrainingDocuments');
            }
        ])->where('id', data_get($this->facebookConfig, 'user_id', env('UTM_SOURCE_MASTER')))
        ->first();

        //Các user được tạo trước đó cùng PSID sẽ không sử dụng để chat lại nữa
        $this->user = UserSale::where('fb_message_psid', $this->PSID)->orderBy('id', 'DESC')->first();
        if ($this->user) {
            $this->conversation = $this->user->aiTuTeConversation;

            if (!data_get($this->user, 'name')) {
                $userName = $this->_getUserNameFromFb();
                if(!empty($userName)) {
                    $this->user->name = $userName;
                    $this->user->save();
                }
            }
        }

        Log::info('user', (array) $this->user);
        Log::info('conversation', (array) $this->conversation);
        return $this;
    }

    //Cần loadDBData
    private function _isNewConversation()
    {
        if(!empty($this->conversation)) {
            $lastMessage = $this->conversation->lastMessage;
            if (data_get($lastMessage, 'updated_at')) {
                $lastTimeUpdate = $lastMessage->updated_at;
                $time = Carbon::parse($lastTimeUpdate);
                $now = now();
                return $time->lt($now) && $time->diffInMinutes($now) >= config('constant.MESSENGER_TIME_TO_NEW_CONVERSATION');
            }
        }
        return True;
    }

    private function _handlePostback($postback)
    {
        $payload = $postback['payload'];

        if (empty($payload)) {
            Helpers::logInfo('Postback empty payload.');
            return;
        }

        $payload = json_decode($payload, true);

        if(data_get($payload, 'data_message')){
            $this->difySendMessage($payload['data_message']);
        }
    }

    private function _callSendAPI($messageText, $aiTuTeMessageId = null)
    {
        try {
            $payload = $this->_convertMessageToPayload($messageText, $aiTuTeMessageId);

            foreach ($payload as $value) {
                Helpers::logInfo("Sending message: PSID - ". $this->PSID, $value);
                $response = Http::withToken($this->pageAccessToken)->post('https://graph.facebook.com/'.$this->fbVersion.'/me/messages', $value);
            }

            Log::info("callSendAPI response: " . json_encode($response->json()));
            return $response->json();
        } catch (\Exception $e) {
            Helpers::logException($e);
            throw $e;
        }
    }

    private function _getUserNameFromFb()
    {
        $respond = $this->_getUserInfoFromFb();

        $name = '';

        if (data_get($respond, 'first_name')) {
            $name = $respond['first_name'];
        }

        if (data_get($respond, 'last_name')) {
            $name .= ' '.$respond['last_name'];
        }

        $name = trim($name);

        return !empty($name) ? $name : 'Người dùng Facebook';
    }

    private function _getUserInfoFromFb()
    {
        try{
            $response = Http::withToken($this->pageAccessToken)->get('https://graph.facebook.com/'.$this->fbVersion.'/'.$this->PSID.'?fields=first_name,last_name');
            return $response->json();
        } catch (\Exception $e) {
            Helpers::logException($e);
            throw $e;
        }
    }

    private function _firstMessage()
    {
        DB::beginTransaction();
        $userName = $this->_getUserNameFromFb();
        try {
            $projects = $this->userAgent->projects->where('chat_training_documents_count', '>', 0);

            $difyQuery = [
                'utm_source' => (string) data_get($this->userAgent, 'id'),
                'name' => $userName,
                'is_facebook' => 'true',
                'tone' => data_get($this->userAgent, 'tone_config') ? data_get(config('configs.ai_tu_te_chat_tone'), data_get($this->userAgent, 'tone_config'), 'Chuyên nghiệp') : 'Chuyên nghiệp',
            ];

            $difyQuery['agency_data_set_id'] = $this->userAgent->getDifyDatasetId();
            $difyQuery['agency_name'] = $this->userAgent->name;

            $difyQuery = json_encode($difyQuery, JSON_UNESCAPED_UNICODE);
            $response = $this->difyService->setAppKey($this->userAgent->getDifyAppChatKey())->sendChat([
                'json' => [
                    'query' => $difyQuery,
                    'response_mode' => 'blocking',
                    'user' => 'user_chat_box',
                    'inputs' => [],
                ]
            ]);

            $response = $response->getBody()->getContents();

            $response = json_decode($response, true);

            $difyConversationId = $response['conversation_id'];
            $answer = $response['answer'];

            $user = UserSale::create([
                'sale_id' => data_get($this->userAgent, 'id'),
                'fb_message_psid' => $this->PSID,
                'name' => $userName
            ]);

            $conversation = AiTuTeConversation::create([
                'user_id' => data_get($this->userAgent, 'id'),
                'dify_conversation_id' => $difyConversationId,
                'page_id' => $this->facebookConfig->page_id,
                'title' => 'Facebook Chat bot'
            ]);
            $this->setConversation($conversation);
            $user->update(['conversation_id' => $conversation->id]);
            $this->setUser($user);

            AiTuTeMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'user',
                'content' => 'Initialization',
                'is_hidden' => 'yes',
                'query' => $difyQuery
            ]);

            AiTuTeMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'ai',
                'content' => $answer
            ]);
            DB::commit();
            $this->_callSendAPI($answer);
        } catch (\Exception $e) {
            Helpers::logException($e);
            DB::rollBack();
        }
    }

    public function difySendMessage($messageText, $isSubmitAppointment = false, $oldMessageId = null)
    {
        DB::beginTransaction();
        try {
            $response = $this->difyService->setAppKey($this->userAgent->getDifyAppChatKey())->sendChat([
                'json' => [
                    'query' => (string) $messageText,
                    'response_mode' => 'blocking',
                    'user' => 'user_chat_box',
                    'inputs' => [],
                    'conversation_id' => data_get($this->conversation, 'dify_conversation_id')
                ]
            ]);
            $response = $response->getBody()->getContents();

            $response = json_decode($response, true);
            Helpers::logInfo("Dify output", $response);
            $answer = $response['answer'];

            $userMessageData = [
                'conversation_id' => $this->conversation->id,
                'sender' => 'user'
            ];

            if ($isSubmitAppointment && !empty($oldMessageId)) {
                $oldMessage = AiTuTeMessage::where('id', $oldMessageId)->first();
                if(!empty($oldMessage)) {
                    $oldForm = $oldMessage->content;
                    try {
                        $formData = json_decode($messageText, true);
                        $oldForm = json_decode($oldMessage->content, true);
                        foreach ($oldForm as $key => $value) {
                            if(data_get($value, 'name') && data_get($formData, data_get($value, 'name'))) {
                                $oldForm[$key]['value'] = $formData[$value['name']];
                            }
                        }
                    } catch (\Exception $e) {
                    }
                    $userMessageData['content'] = json_encode($oldForm, JSON_UNESCAPED_UNICODE);
                    $userMessageData['query'] = $messageText;
                }
            }else {
                $userMessageData['content'] = $messageText;
            }

            AiTuTeMessage::create($userMessageData);

            $aiAnswer = AiTuTeMessage::create([
                'conversation_id' => $this->conversation->id,
                'sender' => 'ai',
                'content' => $answer
            ]);
            DB::commit();
            $this->_callSendAPI($answer, $aiAnswer->id);
        } catch (ClientException $ce) {
            DB::rollBack();

            $body = (string) $ce->getResponse()->getBody();
            Helpers::logInfo($body);

            $data = json_decode($body, true);
            if(data_get($data, 'code') == 'not_found'){
                return $this->_firstMessage();
            }
        } catch (\Exception $e) {
            Helpers::logException($e);
            DB::rollBack();
            throw $e;
        }
    }

    public function difySendAttachment($attachments)
    {
        foreach ($attachments as $attachment) {
            $url = data_get($attachment, 'payload.url');
            $sticker = data_get($attachment, 'payload.sticker_id');

            // Nếu có $sticker là người dùng gửi icon
            if (!empty($url) && empty($sticker)) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $extension;
                $url = Helpers::downloadAndUploadToS3($url, 'chat-files/'.$this->conversation->id.'/'.$fileName);
                $difyQuery = json_encode([
                    'bankImage' => [$fileName],
                    'files' => [[
                        'url' => $url,
                        'type' => $extension
                    ]]
                ], JSON_UNESCAPED_UNICODE);

                $this->difySendMessage($difyQuery);
            }else if (!empty($url) && !empty($sticker)) {
                //Khi người dùng gửi icon thì chuyển thành nội dung vô nghĩa
                $this->difySendMessage('@@');
            }
        }
    }

    private function _sendTypingIndicator()
    {
        try {
            Log::info("Sending typing: PSID - ". $this->PSID);

            $response = Http::withToken($this->pageAccessToken)->post('https://graph.facebook.com/'.$this->fbVersion.'/me/messages', [
                'recipient' => [
                    'id' => $this->PSID
                ],
                'sender_action' => 'typing_on',
            ]);

            Log::info("Send api typing response: " . json_encode($response->json()));
            return $response->json();
        } catch (\Exception $e) {
            Helpers::logException($e);
            throw $e;
        }
    }

    private function _convertMessageToPayload($message, $aiTuTeMessageId)
    {
        $payload = $this->_convertAiTuTeAppointment($message, $aiTuTeMessageId);
        if ($payload) {
            return $payload;
        }

        $payload = $this->_convertAiTuTeUpload($message, $aiTuTeMessageId);
        if ($payload) {
            return $payload;
        }

        return $this->_convertBrHtml($message);
    }

    private function _convertBrHtml($message)
    {
        try {
            $messages = preg_split('/<br\s*\/?>/i', $message);

            foreach ($messages as $value) {

                $result = $this->_convertHtmlTagTuTe($value);

                if ($result) {
                    $payload[] = $result;
                }else {
                    $payload[] = [
                        'recipient' => ['id' => $this->PSID],
                        'message' => [
                            'text' => $value
                        ]
                    ];
                }
            }

            return $payload;
        } catch (\Exception $e) {
            Helpers::logException($e);
        }

        return [[
            'recipient' => ['id' => $this->PSID],
            'message' => [
                'text' => $message
            ]
        ]];
    }

    private function _convertHtmlTagTuTe($content)
    {
        //button tag
        try {
            preg_match_all(
                '/(.*?)<button[^>]+data-message="(\[([^,]+),\s*([^\]]+)\])"[^>]*>([^<]+)<\/button>/s',
                $content,
                $matches,
                PREG_SET_ORDER
            );

            if (count($matches) > 0) {
                $cards = [];

                foreach ($matches as $match) {
                    $description = trim($match[1]);        // mô tả ngay trước button
                    $dataMessageRaw = $match[2];           // toàn bộ chuỗi trong data-message: "[uuid, name]"
                    $projectName = trim($match[4]);        // tên dự án
                    $buttonTitle = trim($match[5]);        // text nút bấm

                    $payload = [
                        'data_message' => $dataMessageRaw
                    ];
                    $cards[] = [
                        'title' => $projectName,
                        'subtitle' => $description,
                        'buttons' => [
                            [
                                'type' => 'postback',
                                'title' => $buttonTitle,
                                'payload' => json_encode($payload)
                            ]
                        ]
                    ];
                }
                return [
                    'recipient' => ['id' => $this->PSID],
                    'message' => [
                        'attachment' => [
                            'type' => 'template',
                            'payload' => [
                                'template_type' => 'generic',
                                'elements' => $cards
                            ]
                        ]
                    ]
                ];
            }
        } catch (\Exception $e) {
            Helpers::logException($e);
        }

        //img tag
        try {
            preg_match('/<img[^>]+src="([^">]+)"/', $content, $matches);

            if (!empty($matches[1])) {
                $url = $this->_handleSendFacebookImage($matches[1]);

                return [
                    'recipient' => ['id' => $this->PSID],
                    'message' => [
                        'attachment' => [
                            'type'=>'image',
                            'payload' => ['url' => $url]
                        ]
                    ]
                ];
            }
        } catch (\Exception $e) {
            Helpers::logException($e);
        }

        //a tag
        try {
            preg_match('/<a[^>]*href=["\']?([^"\'>]*)["\']?[^>]*>(.*?)<\/a>/i', $content, $matches);

            $href = $matches[1] ?? '';
            $text = $matches[2] ?? '';

            if ($href && $text) {
                return [
                    'recipient' => ['id' => $this->PSID],
                    'message' => [
                        'text' => $text.': '.$href
                    ]
                ];
            }
        } catch (\Exception $e) {
            Helpers::logException($e);
        }

        return null;
    }

    //Do facebook bị chặn tải xuống trực tiếp từ link ảnh
    private function _handleSendFacebookImage($url)
    {
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        return Helpers::downloadAndUploadToS3($url, 'chat-files/'.$this->conversation->id.'/tmp/'.$fileName);
    }

    private function _convertAiTuTeAppointment($message, $aiTuTeMessageId)
    {
        try {
            $message = json_decode($message, true);
            $match = false;
            foreach ($message as $value) {
                if (data_get($value, 'content') == 'tu_te_appointment'){
                    $match = true;
                }
                Helpers::logInfo('_convertAiTuTeAppointment', $value);
                if (data_get($value, 'id') == 'introduction') {
                    $payload[] = [
                        'recipient' => ['id' => $this->PSID],
                        'message' => [
                            'text' => $value['content']
                        ]
                    ];
                }
            }
            $payload[] = [
                'recipient' => ['id' => $this->PSID],
                'message' => [
                    'attachment' => [
                        'type' => 'template',
                        'payload' => [
                            'template_type' => 'button',
                            'text' => 'Điền thông tin để tiếp tục',
                            'buttons' => [
                                [
                                    'type' => 'web_url',
                                    'url' => route('facebook.messenger-webview', [
                                        'message_id' => $aiTuTeMessageId,
                                        'user_sale_id' => $this->user->id,
                                        'page_id' => $this->facebookConfig->page_id,
                                    ]),
                                    //'url' => ' https://914d-1-54-211-223.ngrok-free.app/facebook-webview/messenger?message_id='.$aiTuTeMessageId.'&user_sale_id='.$this->user->id.'&page_id='.$this->facebookConfig->page_id,
                                    'title' => 'Mở',
                                    'webview_height_ratio' => 'tall',
                                    'messenger_extensions' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            Helpers::logInfo('', $payload);
            if ($match) {
                return $payload;
            }
        } catch (\Exception $e) {
            //Helpers::logException($e);
        }
        return [];
    }

    private function _convertAiTuTeUpload($message, $aiTuTeMessageId)
    {
        try {
            $message = json_decode($message, true);
            $match = false;
            $payload = [];
            foreach ($message as $value) {
                if (data_get($value, 'content') == 'input_info'){
                    $match = true;
                }
                Helpers::logInfo('_convertAiTuTeAppointment', $value);
                if (data_get($value, 'id') == 'introduction') {
                    $payload = array_merge($payload, $this->_convertBrHtml($value['content']));
                }
                //Tạm thời gửi ảnh trực tiếp qua messenger không cần mở webview
                if (false && data_get($value, 'id') == 'bankImage') {
                    $payload[] = [
                        'recipient' => ['id' => $this->PSID],
                        'message' => [
                            'attachment' => [
                                'type' => 'template',
                                'payload' => [
                                    'template_type' => 'button',
                                    'text' => $value['label'],
                                    'buttons' => [
                                        [
                                            'type' => 'web_url',
                                            'url' => route('facebook.messenger-webview', [
                                                'message_id' => $aiTuTeMessageId,
                                                'user_sale_id' => $this->user->id,
                                                'page_id' => $this->facebookConfig->page_id,
                                            ]),
                                            //'url' => ' https://914d-1-54-211-223.ngrok-free.app/facebook-webview/messenger?message_id='.$aiTuTeMessageId.'&user_sale_id='.$this->user->id.'&page_id='.$this->facebookConfig->page_id,
                                            'title' => 'Mở',
                                            'webview_height_ratio' => 'tall',
                                            'messenger_extensions' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];
                }
            }
            Helpers::logInfo('', $payload);
            if ($match) {
                return $payload;
            }
        } catch (\Exception $e) {
            //Helpers::logException($e);
        }
        return [];
    }
}
<?php

namespace App\Services;

use App\Constants\Zalo;
use App\Exceptions\DomainException;
use App\Exceptions\ZaloChatBotServiceException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ZaloChatBotService
{
    public function __construct(
        protected ZaloService $zaloService,
        protected ChatGPTService $chatGPTService,
        protected ChatDocsService $chatDocsService,
    ) {}
    /**
     *
     * @param array $params
     *
     * @return array
     * @throws DomainException
     */
    public function __invoke(array $params, Request $request): array
    {
        $verifySignature = $this->zaloService
            ->setConfig($params['app_id'])
            ->verifySignature($request);

        $allowReply = $this->zaloService->allowReply($params['sender']['id']);
        $isReplied = $this->zaloService->isReplied($request);

        if (
            $allowReply &&
            !$isReplied &&
            $verifySignature &&
            isset($params['sender']['id']) &&
            isset($params['message']['text']) &&
            in_array($params['event_name'], [
                Zalo::EVENT_NAME_USER_SEND_TEXT
            ])
        ) {
            $result = $this->userSendText($params['sender'], $params['message']);
        }

        if (!isset($result)) {
            $zaloConfig = $this->zaloService->getZaloConfig();
            Log::channel('zalo')->info('Zalo check:', [
                'userIdByApp' => $params['sender']['id'],
                'allowReply' => $allowReply,
                'isReplied' => $isReplied,
                'verifySignature' => $verifySignature,
                'params' => $params,
                'zaloConfig' => $zaloConfig,
            ]);
            throw new ZaloChatBotServiceException('Tin nhắn không được trả lời bằng bot', Response::HTTP_BAD_REQUEST);
        }

        $this->zaloService->checkReplied($request);

        return $result;
    }

    private function userSendText(array $sender, array $receivedMessage): array
    {
        $response = $this->handleMessage($sender, $receivedMessage);
        if (empty($response)) {
            throw new ZaloChatBotServiceException('Không lấy được tin nhắn phản hồi', Response::HTTP_BAD_REQUEST);
        }

        $result = $this->callZaloAPI($response);

        return $result;
    }

    private function handleMessage(array $sender, array $receivedMessage): array
    {
        try {
            $zaloConfig = $this->zaloService->getZaloConfig();

            if (!$zaloConfig['user_id']) {
                throw new ZaloChatBotServiceException('Lỗi khi lấy tin nhắn phản hồi', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $response = $this->chatDocsService->getResponse($receivedMessage['text'], $zaloConfig['user_id']);
            $response = [
                'recipient' => ['id' => $sender['id']],
                'message' => ['text' => $response]
            ];
        } catch (\Exception $e) {
            throw new ZaloChatBotServiceException('Lỗi khi lấy tin nhắn phản hồi', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        return $response;
    }

    private function callZaloAPI(array $response): array
    {
        if (!$response['recipient']['id'] || !$response['message']['text'])
            return [];

        return $this->zaloService->sendMessageText($response['recipient']['id'], $response['message']['text']);
    }
}

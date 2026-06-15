<?php

namespace App\Services;

use App\Exceptions\DomainException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
class ChatDocsService
{
    // const API_CHATDOCS = "http://10.10.20.24:5000/chat?message=:message&user_id=:userId&user_page_id=:userPageId";
    const API_CHATDOCS = ":base_urlchat?message=:message&user_id=:userId&user_page_id=:userPageId&page_id=:pageId";
    protected $base_url;
    public function __construct() {
        $this->base_url = config('services.chatdocs.endpoint');
    }

    public function getResponse(string $message, int $userId, string $userPageId = null, string $pageId = null): string
    {
        set_time_limit(240);
        $message = urlencode($message);

        $url = str_replace(
            [':base_url', ':message', ':userId', ':userPageId', ':pageId'],
            [$this->base_url, $message, $userId, $userPageId, $pageId],
            self::API_CHATDOCS
        );

        Log::info('ChatDocs URL:', ['url' => $url]);

        // Add SSL stream context
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $context = stream_context_create($opts);
        
        $responseContent = '';
        $stream = fopen($url, 'r', false, $context);

        if (!is_resource($stream)) {
            throw new DomainException('Unable to open stream', Response::HTTP_BAD_REQUEST);
        }

        try {
            while (!feof($stream)) {
                $data = fread($stream,  16);
                $responseContent .= $data;
                $position = strpos($responseContent, 'data: END');
                if ($position !== false) {
                    break;
                }
            }

            $decodedData = preg_replace('/\s*data:\s*/', '', $responseContent);
            $decodedData = trim(preg_replace('/\s+/', ' ', $decodedData));
            $decodedData = urldecode($decodedData);
            $decodedData = preg_replace('/END.*/', '', $decodedData);
            fclose($stream);

            return $decodedData;
        } catch (\Exception $th) {
            throw new DomainException('Đã xảy ra lỗi khi xử lý yêu cầu.', Response::HTTP_BAD_REQUEST, $th);
        }
    }
}
<?php

namespace App\Services;

use App\Constants\Facebook as ConstantsFacebook;
use App\Exceptions\FacebookApiServiceException;
use App\Models\Facebook as ModelsFacebook;
use App\Models\User;
use Carbon\Carbon;
use Facebook\Authentication\AccessTokenMetadata;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Facebook\Facebook;
use Facebook\Exception\ResponseException;
use Facebook\Exception\SDKException;

class FacebookApiService
{
    const FACEBOOK_GRAPH_URL = 'https://graph.facebook.com/';
    const FACEBOOK_GRAPH_VERSION = 'v21.0';
    const API_GET_ALL_PAGES = 'me/accounts?limit={limit}&after={cursors_after}';
    const API_GET_USER_INFO = '/me?fields=id,name,email,picture';
    const API_POST_TO_PAGE = '{page_id}/feed';
    const API_GET_PICTURE_PAGE = '{page_id}/picture?type=large';
    const API_POST_PHOTO_PAGE = '{page_id}/photos';
    const API_POST_VIDEO_PAGE = '{page_id}/videos';
    const API_POST_ID = '{post_id}';
    const API_GET_ATTACHMENTS_POST_ID = '{post_id}?fields=permalink_url';
    const ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED = 190;
    const LONG_LIVED_ACCESS_TOKEN_LILFTIME_YEARS = 3; //Max: nerver expired
    protected $facebook;
    public function __construct()
    {
        $this->setConfig();
    }

    public function setConfig(?User $user = null)
    {
        if ($this->facebook) {
            return $this;
        }

        if (is_null($user)) {
            $user = auth('web')->user();
        }

        if (!$user) {
            return $this;
        }

        $facebook = $user->facebooks->first();

        if (!$facebook || !$facebook->app_id || !$facebook->app_secret) {
            throw new FacebookApiServiceException('Không tìm thấy thông tin facebook', Response::HTTP_NOT_FOUND);
        }

        $this->facebook = new Facebook([
            'app_id' => $facebook->app_id,
            'app_secret' => $facebook->app_secret,
            'default_graph_version' => self::FACEBOOK_GRAPH_VERSION,
        ]);

        return $this;
    }

    private function getUrl(string $path, array $search = [], array $replace = []): string
    {
        $path = str_replace($search, $replace, $path);
        return  self::FACEBOOK_GRAPH_URL . self::FACEBOOK_GRAPH_VERSION . '/' . trim($path, '/');
    }

    private function getPath(string $path, array $search = [], array $replace = []): string
    {
        $path = str_replace($search, $replace, $path);
        return  '/' . trim($path, '/');
    }

    public function refreshAccessToken(ModelsFacebook $modelsFacebook): array
    {
        $isExpired = $this->isExpiredInNext30Days($modelsFacebook->user_access_token_expires_at);

        if ($isExpired) {
            try {
                $oAuth2Client = $this->facebook->getOAuth2Client();
                $code = $oAuth2Client->getCodeFromLongLivedAccessToken($modelsFacebook->user_access_token);
            } catch (SDKException $e) {
                throw new FacebookApiServiceException("Facebook SDK refreshAccessToken an error: {$e->getMessage()}", $e->getCode(), $e);
            }

            try {
                $accessToken = $oAuth2Client->getAccessTokenFromCode($code);
            } catch (SDKException $e) {
                echo 'Error getting a new long-lived access token: ' . $e->getMessage();
                exit;
            }

            $expiresAt = $accessToken->getExpiresAt() ?: Carbon::now()->addYears(self::LONG_LIVED_ACCESS_TOKEN_LILFTIME_YEARS);

            return [
                'user_access_token'  => $accessToken->getValue(),
                'user_access_token_expires_at'   =>  $expiresAt
            ];
        }

        return [
            'user_access_token'  => $modelsFacebook->user_access_token,
            'user_access_token_expires_at'   => new Carbon($modelsFacebook->user_access_token_expires_at)
        ];
    }

    private function isExpiredInNext30Days(string|null $expiresAt): bool
    {
        return !Carbon::now()
            ->addDays(ConstantsFacebook::DAYS_REMAINING_TO_EXPIRE)
            ->lessThan(new Carbon($expiresAt));
    }

    public function redirectTo()
    {
        $helper = $this->facebook->getRedirectLoginHelper();

        $permissions = [
            'pages_manage_posts',
            'pages_read_engagement',
            'pages_show_list',
            'publish_video'
        ];

        return $helper->getLoginUrl(route('social.facebook.callback-access-token'), $permissions);
    }

    public function handleCallback(): array
    {
        $helper = $this->facebook->getRedirectLoginHelper();

        if (request('state')) {
            $helper->getPersistentDataHandler()->set('state', request('state'));
        }

        try {
            $accessToken = $helper->getAccessToken();
        } catch (ResponseException $e) {
            throw new FacebookApiServiceException("Graph returned an error: {$e->getMessage()}", $e->getCode(), $e);
        } catch (SDKException $e) {
            throw new FacebookApiServiceException("Facebook SDK returned an error: {$e->getMessage()}", $e->getCode(), $e);
        }

        if (!isset($accessToken)) {
            throw new FacebookApiServiceException("Bạn phải được mời làm user test mới sử dụng được chức năng này", Response::HTTP_FORBIDDEN);
        }

        if (!$accessToken->isLongLived()) {
            try {
                $oAuth2Client = $this->facebook->getOAuth2Client();
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (SDKException $e) {
                throw new FacebookApiServiceException("Error getting a long-lived access token: {$e->getMessage()}", $e->getCode(), $e);
            }
        }

        $user = $this->getUserFacebookApi($accessToken->getValue());
        $expiresAt = $accessToken->getExpiresAt() ?: Carbon::now()->addYears(self::LONG_LIVED_ACCESS_TOKEN_LILFTIME_YEARS);

        return [
            'facebook_user_id' => $user['facebook_user_id'],
            'facebook_user_name' => $user['facebook_user_name'],
            'facebook_user_email' => $user['facebook_user_email'],
            'facebook_user_picture' => $user['facebook_user_picture'],
            'user_access_token' => $accessToken->getValue(),
            'user_access_token_expires_at' => $expiresAt,
        ];
    }
    public function getUserFacebookApi(string $userAccesstoken): array
    {
        try {
            $response = $this->facebook->get(self::API_GET_USER_INFO, $userAccesstoken);
            $user = $response->getGraphUser();

            return [
                'facebook_user_id' => $user->getId(),
                'facebook_user_name' => $user->getName(),
                'facebook_user_email' => $user->getEmail(),
                'facebook_user_picture' => $user->getPicture()?->getUrl(),
            ];
        } catch (SDKException $e) {
            throw new FacebookApiServiceException("Error getting user details: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function getFanpagesFacebookApi(string $userAccesstoken, string|null $cursorsAfter = '', int $limit = 100): array
    {
        try {
            $path = $this->getPath(self::API_GET_ALL_PAGES, ['{limit}', '{cursors_after}'], [$limit, $cursorsAfter]);

            $response = $this->facebook->get($path,  $userAccesstoken);
            $body = $response->getDecodedBody();

            $pages = array_map(function ($item) {
                return [
                    'access_token' => $item['access_token'],
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'image' => $this->getUrl(self::API_GET_PICTURE_PAGE, ['{page_id}'], [$item['id']])
                ];
            }, $body['data']);

            return [
                'data' => $pages,
                'paging_next' => isset($body['paging']['next']),
                'paging_cursors_after' => $body['paging']['cursors']['after']
            ];
        } catch (SDKException $e) {
            report($e);
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED, $e);
            }
            throw new FacebookApiServiceException("Lỗi lấy danh sách page: {$e->getMessage()}", Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function postToFanpage(array $params, string $pageId, string $pageAccessToken, array $files = []): array
    {
        if (
            !empty($files) &&
            (
                $files[0] instanceof UploadedFile && str_starts_with($files[0]->getMimeType(), 'video/') ||
                is_array($files[0]) && $files[0]['type'] == 'video'
            )
        ) {
            return  $this->postVideoToPage($params, $pageId, $pageAccessToken, $files[0]);
        }

        return  $this->postMessageToPage($params, $pageId, $pageAccessToken, $files);
    }

    private function postMessageToPage(array $params, string $pageId, string $pageAccessToken, array $files = []): array
    {
        $url = $this->getUrl(self::API_POST_TO_PAGE, ['{page_id}'], [$pageId]);
        $now = now();
        $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
        $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);

        $data = [
            'message' => $params['content'],
            'access_token' => $pageAccessToken,
        ];

        if (
            !$params['published'] &&
            !is_null($params['scheduled_publish_time']) &&
            $now->lessThan($scheduledPublishTime)
        ) {
            $data['published'] = false;
            $data['scheduled_publish_time'] = $scheduledPublishTime->timestamp;
        }

        $attachments = $this->uploadImages($pageId, $pageAccessToken, $files);

        foreach ($attachments as  $attachment) {
            $data["attached_media"][] = ['media_fbid' => $attachment];
        }

        $response = Http::post($url, $data);

        if ($response->successful()) {
            return $response->collect()->toArray();
        } else {
            $error = $response->collect()->get('error');
            Log::error($error['message'], $response->collect()->toArray());
            if ($error['code'] == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED);
            }
            throw new FacebookApiServiceException('Lỗi post lên Fanpage', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $pageId
     * @param string $accessToken
     * @param array<int, UploadedFile|array{type: 'video'|'image', url: string}> $images
     * @throws \App\Exceptions\FacebookApiServiceException
     * @return array
     */
    private function uploadImages(int $pageId, string $pageAccessToken, $images = []): array
    {
        $attachments = [];
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $imageId =  $this->uploadImagesBySource($image, $pageId, $pageAccessToken);
            } else {
                $imageId =  $this->uploadImagesByUrl($image, $pageId, $pageAccessToken);
            }

            $attachments[] = $imageId;
        }

        return $attachments;
    }

    private function uploadImagesBySource(UploadedFile $image, int $pageId, string $pageAccessToken): string
    {
        $url = $this->getUrl(self::API_POST_PHOTO_PAGE, ['{page_id}'], [$pageId]);

        if (!file_exists($image)) {
            throw new FacebookApiServiceException('Hình ảnh không tồn tại', Response::HTTP_NOT_FOUND);
        }

        $contents = file_get_contents($image->getPathname());
        $filename = $image->getClientOriginalName();

        $response = Http::attach(
            'source',
            $contents,
            $filename
        )->post($url, [
            'access_token' => $pageAccessToken,
            'published' => false,
        ]);
        if ($response->successful()) {
            return $response->collect()->get('id');
        } else {
            throw new FacebookApiServiceException('Lỗi upload ảnh', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array{type: 'image', url: string} $image
     * @param int $pageId
     * @param string $pageAccessToken
     * @throws \App\Exceptions\FacebookApiServiceException
     * @return string
     */
    private function uploadImagesByUrl(array $image, int $pageId, string $pageAccessToken): string
    {
        try {
            $path = $this->getPath(self::API_POST_PHOTO_PAGE, ['{page_id}'], [$pageId]);
            $data = [
                'url' => $image['url'],
                'published' => false,
            ];

            $response = $this->facebook->post($path, $data,  $pageAccessToken);
            $body = $response->getDecodedBody();

            return $body['id'];
        } catch (SDKException $e) {
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED);
            }
            throw new FacebookApiServiceException("Lỗi upload ảnh: {$e->getMessage()}", Response::HTTP_BAD_REQUEST);
        }
    }

    private function postVideoToPage(array $params, string $pageId, string $pageAccessToken, UploadedFile|array $video): array
    {
        if ($video instanceof UploadedFile) {
            $result =  $this->postVideoBySource($params, $video, $pageId, $pageAccessToken);
        } else {
            $result =  $this->postVideoByUrl($params, $video, $pageId, $pageAccessToken);
        }

        return $result;
    }
    private function postVideoBySource(array $params, UploadedFile $video, string $pageId, string $accessToken): array
    {
        $url = $this->getUrl(self::API_POST_VIDEO_PAGE, ['{page_id}'], [$pageId]);
        if (!file_exists($video)) {
            throw new FacebookApiServiceException('Video không tồn tại', Response::HTTP_NOT_FOUND);
        };
        $contents = file_get_contents($video->getPathname());
        $filename = $video->getClientOriginalName();
        $now = now();
        $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
        $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);

        $data = [
            'description' => $params['content'],
            'access_token' => $accessToken,
        ];

        if (
            !$params['published'] &&
            !is_null($params['scheduled_publish_time']) &&
            $now->lessThan($scheduledPublishTime)
        ) {
            $data['published'] = false;
            $data['scheduled_publish_time'] = $scheduledPublishTime->timestamp;
        }

        $response = Http::attach(
            'source',
            $contents,
            $filename
        )->post($url, $data);

        if ($response->successful()) {
            return $response->collect()->toArray();
        } else {
            $error = $response->collect()->get('error');
            Log::error($error['message'], context: $response->collect()->toArray());
            if ($error['code'] == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED);
            }
            throw new FacebookApiServiceException('Lỗi post lên Fanpage', Response::HTTP_BAD_REQUEST);
        }
    }

    private function postVideoByUrl(array $params, array $video, string $pageId, string $pageAccessToken): array
    {
        try {
            $path = $this->getPath(self::API_POST_VIDEO_PAGE, ['{page_id}'], [$pageId]);
            $now = now();
            $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
            $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);

            $data = [
                'description' => $params['content'],
                'file_url' => $video['url'],
            ];

            if (
                !$params['published'] &&
                !is_null($params['scheduled_publish_time']) &&
                $now->lessThan($scheduledPublishTime)
            ) {
                $data['published'] = false;
                $data['scheduled_publish_time'] = $scheduledPublishTime->timestamp;
            }

            $response = $this->facebook->post($path, $data,  $pageAccessToken);
            $body = $response->getDecodedBody();

            return $body;
        } catch (SDKException $e) {
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED);
            }
            throw new FacebookApiServiceException("Lỗi upload video: {$e->getMessage()}", Response::HTTP_BAD_REQUEST);
        }
    }

    public function debugToken(string $accessToken): AccessTokenMetadata
    {
        try {
            $oAuth2Client = $this->facebook->getOAuth2Client();
            $accessTokenMetadata = $oAuth2Client->debugToken($accessToken);
        } catch (SDKException $e) {
            throw new FacebookApiServiceException("Facebook SDK debugToken an error: {$e->getMessage()}", $e->getCode(), $e);
        }

        return $accessTokenMetadata;
    }

    public function updateToFanpage(array $params, string $postId, string $pageAccessToken, array $files = []): array
    {
        return  $this->updateMessageToPage($params, $postId, $pageAccessToken, $files);
    }

    private function updateMessageToPage(array $params, string $postId, string $pageAccessToken, array $files = []): array
    {
        try {
            $now = now();
            $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
            $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);

            $data = [
                'description' => $params['content'], //video
                'message' => $params['content'], //post
            ];

            $data['is_published'] = true;

            if (
                !$params['published'] &&
                !is_null($params['scheduled_publish_time']) &&
                $now->lessThan($scheduledPublishTime)
            ) {
                unset($data['is_published']);
                $data['scheduled_publish_time'] = $scheduledPublishTime->timestamp;
            }


            $path = $this->getPath(self::API_POST_ID, ['{post_id}'], [$postId]);
            $response = $this->facebook->post($path, $data,  $pageAccessToken);
            $body = $response->getDecodedBody();

            if (isset($body['success']) && $body['success']) {
                return $body;
            } else {
                throw new FacebookApiServiceException("Lỗi update bài viết lên page");
            }
        } catch (SDKException $e) {
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED, $e);
            }
            throw new FacebookApiServiceException("Lỗi update bài viết lên page: {$e->getMessage()}", Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function deletePost(string $postId, string $pageAccessToken): array
    {
        try {
            $path = $this->getPath(self::API_POST_ID, ['{post_id}'], [$postId]);
            $response = $this->facebook->delete($path, [],  $pageAccessToken);
            $body = $response->getDecodedBody();

            if (isset($body['success']) && $body['success']) {
                return $body;
            } else {
                throw new FacebookApiServiceException("Lỗi xóa bài viết lên page");
            }
        } catch (SDKException $e) {
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED, $e);
            }
            throw new FacebookApiServiceException("Lỗi xóa bài viết lên page: {$e->getMessage()}", Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function getImageFacebookPost(string $postId, string $pageAccessToken): array
    {
        try {
            $path = $this->getPath(self::API_GET_ATTACHMENTS_POST_ID, ['{post_id}'], [$postId]);
            $response = $this->facebook->get($path,  $pageAccessToken);
            $body = $response->getDecodedBody();

            return $body;
        } catch (SDKException $e) {
            if ($e->getCode() == self::ERROR_CODE_ACCESS_TOKEN_HAS_EXPIRED) {
                throw new FacebookApiServiceException('Token đã hết hạn vui lòng thêm lại tài khoản facebook để tiếp tục', Response::HTTP_UNAUTHORIZED, $e);
            }
            throw new FacebookApiServiceException("Lỗi lấy bài viết page: {$e->getMessage()}", Response::HTTP_BAD_REQUEST, $e);
        }
    }
}

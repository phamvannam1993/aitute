<?php

namespace App\Services;

use App\Exceptions\ZaloServiceException;
use Illuminate\Support\Facades\Http;
use Zalo\Zalo;
use Zalo\Builder\MessageBuilder;
use Zalo\ZaloEndPoint;
use App\Repositories\ZaloConfigRepository;
use Carbon\Carbon;
use Zalo\Exceptions\ZaloResponseException;
use App\Helper\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ZaloService
{
    private $zaloConfigId;
    private $appId;
    private $appSecret;
    private $oaSecretKeyWebhook;
    private $adminGroupId;
    private $accessToken;
    private $refreshToken;
    private $accessTokenExpiresAt;
    private $isPublic;
    private $userIdByAppForTest;
    private $zalo;
    private $userID;

    const API_OA_SEND_MESSAGE_TO_GROUP =  'https://openapi.zalo.me/v3.0/oa/group/message';
    const API_OA_GET_GROUPS =  'https://openapi.zalo.me/v3.0/oa/group/getgroupsofoa';
    const API_ZNS_MESSAGE =  'https://business.openapi.zalo.me/message/template';
    const API_OA_GET_USER_DETAIL =  'https://openapi.zalo.me/v3.0/oa/user/detail';


    public function __construct(
        private ZaloConfigRepository $zaloConfigRepository
    ) {}

    public function setConfig(string $appId)
    {
        $zaloConfig = $this->zaloConfigRepository->first(['app_id' => $appId]);

        if (!$zaloConfig) {
            throw new ZaloServiceException('Không tìm thấy thông tin zalo', Response::HTTP_NOT_FOUND);
        }
        $this->zaloConfigId = $zaloConfig->id;
        $this->appId = $zaloConfig->app_id;
        $this->adminGroupId = $zaloConfig->admin_group_id;
        $this->appSecret = $zaloConfig->app_secret;
        $this->oaSecretKeyWebhook = $zaloConfig->oa_secret_key_webhook;
        $this->refreshToken = $zaloConfig->refresh_token;
        $this->accessToken = $zaloConfig->access_token;
        $this->accessTokenExpiresAt = $zaloConfig->access_token_expires_at;
        $this->isPublic = $zaloConfig->is_public;
        $this->userIdByAppForTest = $zaloConfig->user_id_by_app_for_test;
        $this->userID = $zaloConfig->user_id;

        $this->zalo = new Zalo(config: [
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret
        ]);

        $this->checkAccessTokenExpiresAt();

        return $this;
    }

    public function getZaloConfig(): array
    {
        return [
            'zalo_config_id' => $this->zaloConfigId,
            'app_id' => $this->appId,
            'user_id' => $this->userID,
            'is_public' => $this->isPublic,
            'user_id_by_app_for_test' => $this->userIdByAppForTest,
        ];
    }

    private function checkAccessTokenExpiresAt()
    {
        if (Carbon::now()->lessThan(new Carbon($this->accessTokenExpiresAt))) {
            return true;
        }

        $token = $this->getZaloTokenFromRefreshTokenByOA();

        if (!$token['accessToken']) {
            throw new ZaloServiceException('Không lấy được zalo access token', Response::HTTP_BAD_REQUEST);
        }

        $this->zaloConfigRepository->updateConfig($this->zaloConfigId, $token);
        $this->setConfig($this->appId);
    }

    private function getZaloTokenFromRefreshTokenByOA()
    {
        if (!$this->refreshToken) {
            throw new ZaloServiceException('Không có zalo refresh token', Response::HTTP_BAD_REQUEST);
        }

        try {
            $zaloToken =  $this->zalo->getOAuth2Client()->getZaloTokenFromRefreshTokenByOA($this->refreshToken);
            $accessToken = $zaloToken->getAccessToken();
            $refreshToken = $zaloToken->getRefreshToken();
            return [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ];
        } catch (ZaloResponseException $e) {
            throw new ZaloServiceException('Không lấy được zalo access token: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function getGroupsOfOa()
    {
        $response = $this->zalo->get(self::API_OA_GET_GROUPS, $this->accessToken, []);
        return $response->getDecodedBody();
    }

    public function sendOTPByZalo($data, $user)
    {
        try {
            $data = [
                'phone' => preg_replace('/^0/', '84', $data['phone_number']),
                'template_id' => config('services.zalo.otp_template_id'),
                'template_data' => [
                    'otp' => $user->verification_code,
                ],
            ];
            $response = $this->zalo->post(self::API_ZNS_MESSAGE, $this->accessToken, $data);
            $result = $response->getDecodedBody();
            $result['status'] = true;
            $result['statusCode'] = JsonResponse::HTTP_OK;
            return $result;
        } catch (ZaloResponseException $e) {
            throw new ZaloServiceException('Lỗi khi gửi OTP đi: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
    public function getUserInforByAcesstoken($accessToken)
    {
        $response = Http::withHeaders([
            'access_token' => $accessToken,
            'appsecret_proof' => Helpers::calculateHMacSHA256($accessToken, config('services.zalo.app_secret_v2'))
        ])->get('https://graph.zalo.me/v2.0/me?fields=id,name,birthday,picture');
        return (object)$response->json();
    }
    public function getUserPhoneNumber($accessToken, $phoneToken)
    {
        $response = Http::withHeaders([
            'access_token' => $accessToken,
            'secret_key' => config('services.zalo.app_secret_v2'),
            'code' => $phoneToken
        ])->get('https://graph.zalo.me/v2.0/me/info');
        $result = $response->json();
        $phoneNumber = $result['data']['number'];
        return $phoneNumber;
    }

    public function sendMessageText(string $user_id, string $message): array
    {
        try {
            $msgBuilder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
            $msgBuilder->withUserId($user_id);
            $msgBuilder->withText($message);
            $msgText = $msgBuilder->build();
            $response = $this->zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3, $this->accessToken, $msgText);
            return $response->getDecodedBody();
        } catch (ZaloResponseException $e) {
            throw new ZaloServiceException('Lỗi khi gửi tin nhắn đi: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function verifySignature(Request $request): bool
    {
        if (!$request->hasHeader('X-ZEvent-Signature')) {
            return false;
        }

        $signatureHeader = $request->header('X-ZEvent-Signature');
        $hashSignatureString = $this->hashSignatureString($request);
        $calculatedSignature = 'mac=' . $hashSignatureString;

        return $calculatedSignature === $signatureHeader;
    }

    public function getUserDetailByUserId(string $userId): array
    {
        $params = [
            'data' =>   json_encode(['user_id' => $userId])
        ];

        $response = $this->zalo->get(self::API_OA_GET_USER_DETAIL, $this->accessToken, $params);

        return $response->getDecodedBody();
    }

    public function sendRequestUserInfo(string $userId): array
    {
        $msgBuilder = new MessageBuilder(MessageBuilder::MSG_TYPE_REQUEST_USER_INFO);
        $msgBuilder->withUserId($userId);

        $element = array(
            "title" => "Hãy để lại số điện thoại, chúng tôi sẽ liên hệ lại cho bạn ngay",
            "subtitle" => "<Nhấn vào đây để gửi số điện thoại cho chúng tôi>",
            "image_url" => "https://stc-oa-chat-adm.zdn.vn/images/request-info-banner.png"
        );
        $msgBuilder->addElement($element);

        $msgText = $msgBuilder->build();

        $response = $this->zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3, $this->accessToken, $msgText);
        return $response->getDecodedBody();
    }

    public function sendMessageTextToGroup(string $text = '')
    {
        try {
            $msgBuilder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
            $msgBuilder->withGroupId($this->adminGroupId);
            $msgBuilder->withText($text);
            $msgImage = $msgBuilder->build();
            $response = $this->zalo->post(self::API_OA_SEND_MESSAGE_TO_GROUP, $this->accessToken, $msgImage);
            return $response->getDecodedBody();
        } catch (ZaloResponseException $e) {
            throw new ZaloServiceException('Lỗi khi gửi tin nhắn đi: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function allowReply(string $userIdByApp): bool
    {
       

        if ($this->isPublic) {
            return true;
        }

        if (!is_array($this->userIdByAppForTest)) {
            return false;
        }

        return in_array($userIdByApp, $this->userIdByAppForTest);
    }

    public function hashSignatureString(Request $request)
    {
        $appId = $this->appId;
        $data = $request->all();
        $timeStamp  = $request->get('timestamp');
        $OAsecretKey = $this->oaSecretKeyWebhook;

        $rawSignatureString = $appId . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . $timeStamp . $OAsecretKey;
        return hash('sha256', $rawSignatureString);
    }

    public function checkReplied(Request $request)
    {
        $hashSignatureString = $this->hashSignatureString($request);
        cache([$hashSignatureString => true], now()->addMinutes(10));
    }

    public function isReplied(Request $request): bool
    {
        $hashSignatureString = $this->hashSignatureString($request);

        return cache()->has($hashSignatureString);
    }
}

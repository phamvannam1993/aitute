<?php

namespace App\Services;

use App\Helper\Helpers;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use App\Models\FacebookConfigs;
use Facebook\Facebook;
use Illuminate\Support\Facades\Auth;

class FacebookConfigsService
{
    private $fb;
    private $callbackUrl;

    public function __construct()
    {
        $this->fb = new Facebook([
            'app_id' => config('services.messenger.app_id'),
            'app_secret' => config('services.messenger.app_secret'),
            'default_graph_version' => 'v23.0',
        ]);

        $this->callbackUrl = route('facebook.callback');
    }

    public function findByPageId(string $pageId)
    {
        return FacebookConfigs::where('page_id', $pageId)->first();
    }

    public function findByUserId(string $userId)
    {
        return FacebookConfigs::where('user_id', $userId)->first();
    }

    public function getListPaginate($params = [])
    {
        $defaultPerPage = 10;
        $query = FacebookConfigs::where('user_id', Auth::user()->id);
        if (isset($params['page'])) {
            $perPage = data_get($params, 'per_page', $defaultPerPage);
            $query->offset($params['page'] * $perPage);
            $query->limit($perPage);
        }
        return $query->orderBy('created_at', 'desc')->paginate(data_get($params, 'per_page', $defaultPerPage));
    }

    public function saveFanPages($facebookConfigs)
    {
        foreach ($facebookConfigs as $facebookConfig) {
            if (!empty($facebookConfig['page_id']) && !empty($facebookConfig['page_access_token'])) {
                $user = Auth::user();
                if (!$this->__subscribeWebhook($facebookConfig['page_id'], $facebookConfig['page_access_token'])) {
                    continue;
                }
                $fbConfig = FacebookConfigs::where('page_id', $facebookConfig['page_id'])->first();
                $response = $this->fb->get('/'.$facebookConfig['page_id'].'/picture?redirect=false&height=200&type=large', $facebookConfig['page_access_token']);
                $picture = $response->getDecodedBody();
                $pagePicture = data_get($picture, 'data.url');
                if (!$fbConfig) {
                    FacebookConfigs::create([
                        'page_id' => $facebookConfig['page_id'],
                        'page_name' => $facebookConfig['page_name'],
                        'page_picture' => $pagePicture,
                        'page_access_token' => $facebookConfig['page_access_token'],
                        'user_id' => $user->id
                    ]);
                }else {
                    $fbConfig->page_name = $facebookConfig['page_name'];
                    $fbConfig->page_picture = $pagePicture;
                    $fbConfig->page_access_token = $facebookConfig['page_access_token'];
                    $fbConfig->user_id = $user->id;
                    $fbConfig->save();
                }
            }
        }
    }

    private function __subscribeWebhook($pageId, $pageAccessToken)
    {
        try {
            // Đăng ký các fields cần theo dõi
            $subscribeFields = [
                'messages',              // Nhận thông báo khi có tin nhắn mới
                'messaging_postbacks',   // Nhận postback từ buttons, quick replies
                'message_deliveries',    // Theo dõi trạng thái gửi tin nhắn
                //'message_reads'          // Theo dõi trạng thái đọc tin nhắn
                'messaging_referrals',
                'messaging_optins'
            ];

            // Gọi API để đăng ký webhook cho page
            $response = $this->fb->post('/' . $pageId . '/subscribed_apps', [
                'subscribed_fields' => implode(',', $subscribeFields),
                'access_token' => $pageAccessToken
            ]);

            $result = $response->getGraphNode();

            \Log::info('Kết quả đăng ký webhook', [
                'success' => $result->getField('success'),
                'result' => $result->asArray()
            ]);

            if ($result->getField('success')) {
                return true;
            }else {
                return false;
            }


        } catch (FacebookResponseException $e) {
            Helpers::logInfo($e->getMessage());
            return false;
        } catch (FacebookSDKException $e) {
            Helpers::logInfo($e->getMessage());
            return false;
        } catch (\Exception $e) {
            Helpers::logException($e);
            return false;
        }
    }
}

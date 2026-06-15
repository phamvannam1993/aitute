<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\FacebookConfigs;
use App\Services\FacebookConfigsService;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class FacebookConfigController extends Controller
{
    private $fb;
    private $callbackUrl;

    public function __construct(private FacebookConfigsService $facebookConfigsService)
    {
        $this->fb = new Facebook([
            'app_id' => config('services.messenger.app_id'),
            'app_secret' => config('services.messenger.app_secret'),
            'default_graph_version' => 'v23.0',
        ]);

        $this->callbackUrl = route('facebook.callback');
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $facebookConfigs = $this->facebookConfigsService->getListPaginate();

        $user = Auth::user();

        return Inertia::render('Client/EmbedConfig/Facebook/Index', [
            'facebookConfigs' => $facebookConfigs,
            'fanpage_dify_apps' => config('configs.fanpage_dify_apps'),
            'fanpagesProp' => Session::get('facebook_pages', []),
            'toneConfigs' => config('configs.ai_tu_te_chat_tone'),
            'fanpage_dify_apps' => config('configs.fanpage_dify_apps'),
            'user' => $user,
            'documents' => [],
            'totalPage' => 0,
            'currentPage' => 1
        ]);
    }

    public function fetchData(Request $request)
    {
        $facebookConfigs = $this->facebookConfigsService->getListPaginate($request->all());
        return response()->json([
            'facebookConfigs' => $facebookConfigs,
            'success' => true,
        ]);
    }

    public function update($id, Request $request)
    {
        try {
            $data = [
                'fanpage_dify_app' => $request->get('fanpage_dify_app')
            ];
            FacebookConfigs::where('id', $id)->where('user_id', Auth::user()->id)->update($data);
            return response()->json([
                'message' => 'Cập nhật thành công!',
            ]);
        } catch (\Exception $e) {
            Helpers::logException($e);
            return response()->json([
                'message' => 'Cập nhật thất bại!',
            ]);
        }
    }

    public function redirectToFacebook()
    {
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = ['pages_show_list', 'pages_messaging', 'pages_read_engagement'];

        $loginUrl = $helper->getLoginUrl($this->callbackUrl, $permissions);

        return redirect($loginUrl);
    }

    public function handleFacebookCallback(Request $request)
    {
        $helper = $this->fb->getRedirectLoginHelper();

        // Fix for "Cross-site request forgery validation failed"
        if ($request->has('state')) {
            $helper->getPersistentDataHandler()->set('state', $request->get('state'));
        }

        try {

            $accessToken = $helper->getAccessToken($this->callbackUrl);

            if (!$accessToken) {
                return redirect()->route('embed-config.facebook.index')
                    ->with('error', 'Đăng nhập Facebook thất bại');
            }

            // Get long-lived access token
            $oAuth2Client = $this->fb->getOAuth2Client();
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);


            // Get user's pages
            $response = $this->fb->get('/me/accounts', $longLivedAccessToken);
            $pages = $response->getGraphEdge()->asArray();


            $formattedPages = array_map(function($page) {
                return [
                    'page_id' => $page['id'],
                    'page_name' => $page['name'],
                    'page_access_token' => $page['access_token']
                ];
            }, $pages);

            $this->facebookConfigsService->saveFanPages($formattedPages);

            return redirect()->route('embed-config.facebook.index')
                ->with('success', 'Liên kết thành công');

        } catch (FacebookResponseException $e) {
            return redirect()->route('embed-config.facebook.index')
                ->with('error', 'Lỗi Facebook API: ' . $e->getMessage());
        } catch (FacebookSDKException $e) {
            return redirect()->route('embed-config.facebook.index')
                ->with('error', 'Lỗi Facebook SDK: ' . $e->getMessage());
        }
    }

    public function subscribeWebhook($pageId, $pageAccessToken)
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
                return [
                    'success' => true,
                    'message' => 'Đăng ký webhook thành công'
                ];
            }

            return [
                'success' => false,
                'message' => 'Đăng ký webhook thất bại'
            ];

        } catch (FacebookResponseException $e) {

            return [
                'success' => false,
                'message' => 'Lỗi Facebook API: ' . $e->getMessage()
            ];
        } catch (FacebookSDKException $e) {
            return [
                'success' => false,
                'message' => 'Lỗi Facebook SDK: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Helpers::logException($e);
            return [
                'success' => false,
                'message' => 'Lỗi không xác định: ' . $e->getMessage()
            ];
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
            'page_name' => 'required|string',
            'page_access_token' => 'required|string',
        ]);

        $user = Auth::user();


        try {
            $subscribeResult = $this->subscribeWebhook($request->page_id, $request->page_access_token);

            if (!$subscribeResult['success']) {
                return redirect()->back()->with('error', $subscribeResult['message']);
            }

            $config = FacebookConfigs::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'page_id' => $request->page_id,
                    'page_name' => $request->page_name,
                    'page_access_token' => $request->page_access_token,
                ]
            );

            return redirect()->back()->with('success', 'Cấu hình Facebook và đăng ký webhook đã được cập nhật thành công');

        } catch (FacebookResponseException $e) {
            return redirect()->back()->with('error', 'Lỗi xác thực Facebook: ' . $e->getMessage());
        } catch (FacebookSDKException $e) {
        } catch (\Exception $e) {
            \Log::error('Lỗi không xác định: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;

            FacebookConfigs::where("id", $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xóa có lỗi.'
            ]);
        }
    }
}
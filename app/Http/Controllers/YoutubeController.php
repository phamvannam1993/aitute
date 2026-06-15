<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\YoutubeAccessToken;
use App\Models\AIGeneratedMedia;
use App\Helper\Helpers;
use Auth;
class YoutubeController extends Controller
{
    protected $client, $OAUTH2_CLIENT_ID, $OAUTH2_CLIENT_SECRET;
    protected $scopes;
    public function __construct(\Google_Client $client)
    {
        $this->OAUTH2_CLIENT_ID = env("GOOGLE_CLIENT_ID");
        $this->OAUTH2_CLIENT_SECRET = env("GOOGLE_CLIENT_SECRET");
        $this->client = $this->setup($client);
        $this->scopes = [
            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.readonly'
        ];
    }

    public function store(Request $request)
    {
        $id = $request["id"];
        $userId =  Auth::user()->id;
        $aiHistory = AIGeneratedMedia::where("id", $id)->first();
        if($aiHistory['s3_url'] == "") {
            return response()->json(["success" => false, "code" => 404, "message" => "Không tồn tại video"]);
        }
        $path = Helpers::preSignedS3Url($aiHistory['s3_url']);
        try {
            $videoPath = storage_path('app/public/' . uniqid('video_') . '.mp4');
            $access_token = $this->getAccessToken($userId);
            if(empty($access_token)) {
                return response()->json(["success" => false, "code" => 400, "message" => "Không tồn tại token"]);
            }
            file_put_contents($videoPath, file_get_contents($path));
            $response = uploadYoutube($videoPath, [
                'title'       => $request["title"],
                'description' => $request["description"],
            ], $access_token);
            if($response["success"] == false) {
                if($response["code"] == 401) {
                    return response()->json(["success" => false, "code" => 401, "message" => "Bạn cần được admin cấp quyền mới dùng được tính năng này"]);
                } else if($response["code"] == 403) {
                    return response()->json(["success" => false, "code" => 301, "message" => "Tài khoản của bạn đã post 3 video trong ngày. Xin vui lòng post video mới vào ngày hôm sau"]);
                } else {
                    return response()->json(["success" =>false, "code" => 500, "message" => "Có lỗi xảy ra trong quá trình thực hiện"]);
                }
            }
            return response()->json($response);
        } catch(\Exception $ex) {
            return response()->json(["success" =>false, "code" => 500, "message" => "Có lỗi xảy ra trong quá trình thực hiện"]);
        }
    }

    public function getListAccessToken() {
        $youtubeToken = YoutubeAccessToken::get();
        return response()->json(["data" =>  $youtubeToken]);
    }

    public function getAccessToken($user_id) {
        $youtubeToken = YoutubeAccessToken::where("user_id", $user_id)->first();
        if($youtubeToken) {
            $accessToken = json_decode($youtubeToken->access_token, TRUE);
            if(empty($accessToken)) {
                return "";
            }
            $created = $accessToken["created"];
            $expiresIn = $accessToken["expires_in"];
            if ((time() - $created) > $expiresIn) {
                if(!isset($accessToken["refresh_token"])) {
                    return "";
                }
                $this->client->refreshToken($accessToken["refresh_token"]);
                $newToken = $this->client->getAccessToken();
                YoutubeAccessToken::where("id", $youtubeToken->id)->update(['access_token' => json_encode($newToken),  "updated_at" => date('Y-m-d H:i:s')]);
                return json_encode($newToken);
            } else {
                return $youtubeToken->access_token;
            }
        } else {
            return "";
        }
    }

    private function setup(\Google_Client $client)
    {
        $client->setClientId($this->OAUTH2_CLIENT_ID);
        $client->setClientSecret($this->OAUTH2_CLIENT_SECRET);
        $client->setScopes($this->scopes);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        return $this->client = $client;
    }

    public function login()
    {
        try {
            session()->put('login_google_type', "yotube");
            $parameters = ['access_type' => 'offline', 'approval_prompt' => "force"];
            return Socialite::driver('google')->scopes($this->scopes)->with($parameters)->redirect();
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function loginCallback(Request $request)
    {
        $session =  session("login_google_type");
        if($session == "google") {
            $data = $request->all();
            $param = http_build_query($data);
            return redirect("/authorized/google/callback?".$param);
        }
        try {
            $user_id =  Auth::user()->id;
            $access_token = Socialite::driver("google")->getAccessTokenResponse($request->code);
            $access_token["created"] = time();
            $access_token = json_encode($access_token);
            $youtubeToken = YoutubeAccessToken::where("user_id", $user_id)->first();
            if($youtubeToken) {
                YoutubeAccessToken::where("id", $youtubeToken->id)->update(['access_token' => $access_token,  "updated_at" => date('Y-m-d H:i:s')]);
            } else {
                YoutubeAccessToken::insert([
                    "user_id" => $user_id,
                    'access_token' => $access_token,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ]);
            }
            return redirect("/ai-video/history");
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}

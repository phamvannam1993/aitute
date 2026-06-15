<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\FacebookContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacebookContentController extends Controller
{
    public function __construct(
        private FacebookContentService $facebookConentService

    ) {}

    public function apiSaveContent(Request $request) {
        return $this->facebookConentService->apiSaveContent($request);
    }

    public function deleteFacebookContent(Request $request) {
        return $this->facebookConentService->deleteFacebookContent($request);
    }

    public function getList(Request $request) {
        $userId = Auth::user()->id;
        $results = $this->facebookConentService->getList($userId, $request->project_id, $request->limit);
        return response()->json(["data" => $results]);
    }

    public function getTotal(Request $request) {
        $userId = Auth::user()->id;
        $total = $this->facebookConentService->getTotal($userId, $request->project_id);
        return response()->json(["user_id" => $userId, "total" => $total]);
    }

    public function getDetail(Request $request) {
        $item = $this->facebookConentService->getOne($request->id, $request->is_next);
        $imagesDefault =  [
            [
                "is_post" => false,
                "imageRef" =>  ""
            ],
            [
                "is_post" => false,
                "imageRef" =>  ""
            ],
            [
                "is_post" => false,
                "imageRef" =>  ""
            ],
            [
                "is_post" => false,
                "imageRef" =>  ""
            ],
        ];
        $item->images = $item->images ? json_decode($item->images) : $imagesDefault;
        $item->videos = $item->videos ? json_decode($item->videos) : "";
        return response()->json(["data" => $item]);
    }

    public function update(Request $request) {
        return $this->facebookConentService->update($request);
    }

    public function postFacebookCron(Request $request) {
        return $this->facebookConentService->postFacebookCron();
    }

    public function confirmPostFacebook(Request $request) {
        $userId = Auth::user()->id;
        return $this->facebookConentService->confirmPostFacebook($userId, $request);
    }

    public function delete($id) {
        try {
            $userId = Auth::user()->id;
            $this->facebookConentService->delete($id, $userId);
            return response()->json(["success" => true]);
        } catch(\Exception $ex) {
            return response()->json(["success" => false]);
        }
    }

    public function postFacebook(Request $request) {
        $postId = $request->id ? $request->id : 3;
        try {
            $res = $this->facebookConentService->postFacebookByID($postId);
            if(!$res) {
                return response()->json(["success" => false, "message" => "Đăng bài thất bại", "res" => $res]);
            }
            return response()->json(["success" => true, "message" => "Đăng bài thành công", "res" => $res]);
        } catch (\Exception $ex) {
            return response()->json(["success" => false, "message" => $ex->getMessage()]);
        }
    }
}

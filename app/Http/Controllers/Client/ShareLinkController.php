<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ShareLink\StoreShareLinkRequest;
use App\Models\AIAssistantHistories;
use App\Models\AiGeneratedBannerPoster;
use App\Models\AIGeneratedMedia;
use App\Models\AIVideo;
use App\Models\Video;
use App\Models\ProductImage;
use App\Models\CreatomateVideo;
use App\Models\McVirtual;
use App\Models\PictoryVideo;
use App\Models\SwapFace;
use App\Services\ShareLinkService;
use App\Models\Lipsync;
use App\Models\Music;
use App\Models\PebblelyVideo;
use App\Models\SocialPost;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class ShareLinkController extends Controller
{
    use ApiResponses;
    public function __construct(
        private ShareLinkService $shareLinkService,

    ) {}


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreShareLinkRequest $request): JsonResponse
    {
        try {
            $result = $this->shareLinkService->store($request->all());
            return $this->okResponse($result);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): InertiaResponse|RedirectResponse
    {
        try {
            $shareLinkable = $this->shareLinkService->show($request->route('token'));
            switch (true) {
                case $shareLinkable instanceof AIAssistantHistories:
                    $params = $shareLinkable->only(['prompt', 'response']);
                    return Inertia::render('Client/AiAssistantHistory/View', $params);
                case $shareLinkable instanceof AIGeneratedMedia:
                    $params = $shareLinkable->only(['s3_url', 'thumbnail', 's3_url_video_result', 's3_url_video_image', 's3_url_video_audio']);
                    if ($params["s3_url_video_audio"] != "") {
                        $params['s3_url'] = $params["s3_url_video_audio"];
                    }
                    if ($params["s3_url_video_result"] != "") {
                        $params['s3_url'] = $params["s3_url_video_result"];
                    }
                    $url = Helpers::preSignedS3Url($params['s3_url']);
                    $thumbnail = Helpers::preSignedS3Url($params['thumbnail']);
                    if (str_contains($url, '.png')) {
                        $thumbnail  = $url;
                        session()->put('thumbnail', $url);
                        return Inertia::render('Client/AIVideo/ShareView', [
                            "thumbnail" => $thumbnail,
                        ]);
                    }
                    session()->put('thumbnail', $thumbnail);
                    session()->put('video_url', $url);
                    if (str_contains($url, 'images')) {
                        session()->put('thumbnail', $url);
                    }
                    return Inertia::render('Client/AIGeneratedMedia/View', [
                        'url' => $url,
                        "thumbnail" => $thumbnail,
                    ]);
                case $shareLinkable instanceof Video:
                    $params = $shareLinkable->only(['s3_url', 'thumbnail', 's3_url_video_result', 's3_url_video_image', 's3_url_video_audio']);
                    if ($params["s3_url_video_audio"] != "") {
                        $params['s3_url'] = $params["s3_url_video_audio"];
                    }
                    if ($params["s3_url_video_result"] != "") {
                        $params['s3_url'] = $params["s3_url_video_result"];
                    }
                    $url = Helpers::preSignedS3Url($params['s3_url']);
                    $thumbnail = Helpers::preSignedS3Url($params['thumbnail']);
                    if (str_contains($url, '.png')) {
                        $thumbnail  = $url;
                        session()->put('thumbnail', $url);
                        return Inertia::render('Client/AIVideo/ShareView', [
                            "thumbnail" => $thumbnail,
                        ]);
                    }
                    session()->put('thumbnail', $thumbnail);
                    session()->put('video_url', $url);
                    if (str_contains($url, 'images')) {
                        session()->put('thumbnail', $url);
                    }
                    return Inertia::render('Client/AIGeneratedMedia/View', [
                        'url' => $url,
                        "thumbnail" => $thumbnail,
                    ]);
                case $shareLinkable instanceof Lipsync:
                    $params = $shareLinkable->only(['result']);
                    $url = Helpers::preSignedS3Url($params['result']);
                    session()->put('thumbnail', $url);
                    session()->put('video_url', $url);
                    return Inertia::render('Client/AIGeneratedMedia/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof AIVideo:
                    $params = $shareLinkable->only(['thumbnail']);
                    $thumbnail = Helpers::preSignedS3Url($params['thumbnail']);
                    session()->put('thumbnail', $thumbnail);
                    session()->put('url', $thumbnail);
                    return Inertia::render('Client/AIVideo/ShareView', [
                        "thumbnail" => $thumbnail,
                    ]);
                case $shareLinkable instanceof ProductImage:
                    $params = $shareLinkable->only(['s3_url']);
                    $url = Helpers::preSignedS3Url($params['s3_url']);
                    session()->put('thumbnail', $url);
                    session()->put('video_url', $url);
                    return Inertia::render('Client/AIGeneratedMedia/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof SwapFace:
                    $params = $shareLinkable->only(['s3_url']);
                    $thumbnail = Helpers::preSignedS3Url($params['s3_url']);
                    session()->put('thumbnail', $thumbnail);
                    session()->put('video_url', $thumbnail);
                    return Inertia::render('Client/AIVideo/ShareView', [
                        "thumbnail" => $thumbnail,
                    ]);
                case $shareLinkable instanceof McVirtual:
                    $params = $shareLinkable->only(['avatar_url', 'result_url']);
                    $thumbnail = Helpers::preSignedS3Url($params['avatar_url']);
                    $url = Helpers::preSignedS3Url($params['result_url']);
                    session()->put('thumbnail', $thumbnail);
                    session()->put('url', $url);
                    return Inertia::render('Client/AIVirtual/View', [
                        "thumbnail" => $thumbnail,
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof PebblelyVideo:
                    $params = $shareLinkable->only(['s3_url']);
                    $url = Helpers::preSignedS3Url($params['s3_url']);
                    session()->put('thumbnail', $url);
                    return Inertia::render('Client/AIBackground/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof PictoryVideo:
                    $params = $shareLinkable->only(['s3_url']);
                    $url = json_decode($params['s3_url'])->videoURL;
                    // $url = Helpers::preSignedS3Url($params['preview_url']);
                    session()->put('video_url', $url);
                    return Inertia::render('Client/AIVideo/UrlToVideo/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof AiGeneratedBannerPoster:
                    $params = $shareLinkable->only(['s3_url']);
                    $url = Helpers::preSignedS3Url($params['s3_url']);
                    session()->put('url', $url);
                    return Inertia::render('Client/AiImageAdvertisement/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof CreatomateVideo:
                    $params = $shareLinkable->only(['result']);
                    $url = $params['result'];
                    session()->put('video_url', $url);
                    return Inertia::render('Client/Creatomate/View', [
                        'url' => $url,
                    ]);
                case $shareLinkable instanceof Music:
                    $params = $shareLinkable->only(['prompt', 'lyrics', 'result_audio']);
                    $url = Helpers::preSignedS3Url($params['result_audio']);
                    if (isset($shareLinkable['image_url'])) {
                        $params['image_url'] = Helpers::preSignedS3Url($shareLinkable['image_url']);
                    }
                    $params['url'] = $url;
                    session()->put('url', $url);
                    return Inertia::render('Client/TextToMusic/View', [
                        'params' => $params,
                    ]);
                case $shareLinkable instanceof SocialPost:
                    $params = $shareLinkable->only(['content', 'medias', 'video']);
                    return Inertia::render('Client/ShareLink/SocialPosts/View', $params);
                default:
                    $params =  $shareLinkable->only(['prompt', 'response']);
                    return Inertia::render('Client/AiAssistantHistory/View', $params);
            }
        } catch (\Throwable $e) {
            report($e);
            return to_route('home.index');
        }
    }

    public function generateChatBoxIframeUrl()
    {
        try {
            $token = Crypt::encryptString(json_encode([
                'utm_source' => auth()->user()->id,
                'dify_app_chat_ai' => auth()->user()->dify_app_chat_ai
            ]));
            $url = route('chat-box.iframe', ['token' => $token]);
            return response()->json([
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Helpers::logException($e);
            return response()->json([
                'message' => 'Tạo link chat box thất bại!',
            ], 400);
        }
    }
}

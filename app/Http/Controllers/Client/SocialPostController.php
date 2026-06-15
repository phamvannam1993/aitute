<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialPost\MultiPostToFbRequest;
use App\Http\Requests\SocialPost\PostToFbRequest;
use App\Services\SocialPostService;
use App\Services\StorageService;
use App\Http\Requests\SocialPost\JobCreateContentAIRequest;
use App\Http\Requests\SocialPost\StoreAjaxSocialPostRequest;
use App\Http\Requests\SocialPost\StoreSocialPostRequest;
use App\Http\Requests\SocialPost\UpdateAjaxSocialPostRequest;
use App\Http\Requests\SocialPost\UpdateToFbRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialPostController extends Controller
{
    use ApiResponses;
    public function __construct(
        protected SocialPostService $socialPostService,
        protected StorageService $storageService,
    ) {}


    /**
     * Handle the incoming request.
     *
     * @param MultiPostToFbRequest $request
     * @return JsonResponse
     */
    public function store(StoreSocialPostRequest $request): JsonResponse
    {
        try {
            $result = $this->socialPostService->store($request->validated());
            return $this->okResponse($result);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    private function uploadMediaToAyrshare($filePaths = [], $fileNames = 'filename', $formData, $video)
    {
        $mediaUrls = [];

        if (!empty($filePaths)) {
            foreach ($filePaths as $index => $filePath) {
                $fileName = $fileNames . '-' . $index;
                $file = fopen($filePath, 'r');

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('ayrshare.api_key'),
                ])->attach('file', $file, $fileName)->post('https://app.ayrshare.com/api/upload');

                fclose($file);

                if ($response->successful()) {
                    $mediaUrl = $response->json()['url'];
                    $mediaUrls[] = $mediaUrl;
                } else {
                    throw new \Exception('Failed to upload media to Ayrshare for file: ' . $fileName);
                }
            }
        }

        if (!empty($video)) {
            $file = fopen($video, 'r');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ayrshare.api_key'),
            ])->attach('file', $file, 'video')->post('https://app.ayrshare.com/api/upload');

            fclose($file);

            if ($response->successful()) {
                $mediaUrl = $response->json()['url'];
                $mediaUrls[] = $mediaUrl;
                $formData['isVideo'] = true;
            } else {
                throw new \Exception('Failed to upload video to Ayrshare');
            }
        }

        if (!empty($mediaUrls)) {
            $formData['mediaUrls'] = $mediaUrls;
        }

        return $this->postContentToFacebook($formData);
    }

    public function postContentToFacebook($formData)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ayrshare.api_key'),
        ])->post('https://app.ayrshare.com/api/post', $formData);
    }

    public function postToTikTok(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'mediaUrl' => 'required|url',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ayrshare.api_key'),
                'Content-Type' => 'application/json'
            ])->post('https://app.ayrshare.com/api/post', [
                'post' => $request->text,
                'platforms' => ['tiktok'],
                'mediaUrls' => [$request->mediaUrl]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đăng bài lên TikTok thành công',
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đăng bài lên TikTok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function connectSocial()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('AYRSHARE_API_KEY'),
        ])->get('https://app.ayrshare.com/api/profiles');

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error retrieving linked accounts',
            'data' => $response->json(),
        ], $response->status());
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('file');
        if ($file) {
            $result = $this->storageService->putToS3($file, folderName: 'social-image');
            return response()->json(['url' => $result]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Handle the incoming request.
     *
     * @param PostToFbRequest $request
     * @return RedirectResponse
     */
    public function postToFanpage(PostToFbRequest $request): RedirectResponse
    {
        try {
            $this->socialPostService->postToFanpage($request->validated());
            return to_route('calendar');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi khi đăng bài lên Fanpage: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param UpdateToFbRequest $request
     * @return RedirectResponse
     */
    public function updateToFanpage(UpdateToFbRequest $request): RedirectResponse
    {
        try {
            $this->socialPostService->updateToFanpage($request->validated());
            return to_route('calendar');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi cập nhật bài viết Fanpage: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(int $id, Request $request): RedirectResponse
    {
        try {
            $this->socialPostService->destroy($id);
            return to_route('calendar');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi xóa bài: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function jobCreateContentAI(JobCreateContentAIRequest $request): RedirectResponse
    {
        try {
            $this->socialPostService->jobCreateContentAI($request->validated());
            return to_route('calendar');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi khi đăng bài hàng loạt Fanpage: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param MultiPostToFbRequest $request
     * @return JsonResponse
     */
    public function multiPostToFanpage(MultiPostToFbRequest $request): JsonResponse
    {
        try {

            $this->socialPostService->multiPostToFanpage($request->validated());
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param PostToFbRequest $request
     * @return JsonResponse
     */
    public function ajaxPostToFanpage(PostToFbRequest $request): JsonResponse
    {
        try {
            $this->socialPostService->postToFanpage($request->validated());
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param PostToFbRequest $request
     * @return JsonResponse
     */
    public function ajaxStoreSocialPost(StoreAjaxSocialPostRequest $request): JsonResponse
    {
        try {
            $this->socialPostService->storeSocial($request->validated());
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param PostToFbRequest $request
     * @return JsonResponse
     */
    public function ajaxUpdateSocialPost(UpdateAjaxSocialPostRequest $request): JsonResponse
    {
        try {
            $this->socialPostService->updateSocial($request->validated());
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }


    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxJobCreateContentAI(JobCreateContentAIRequest $request): JsonResponse
    {
        try {
            $this->socialPostService->jobCreateContentAI($request->validated());
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    public function ajaxDestroy(int $id): JsonResponse
    {
        try {
            $this->socialPostService->destroy($id);
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}

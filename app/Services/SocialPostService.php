<?php

namespace App\Services;

use App\Constants\AIModel;
use App\Constants\Facebook;
use App\Exceptions\FacebookApiServiceException;
use App\Exceptions\SocialPostServiceException;
use App\Jobs\SocialPostAIFacebook;
use App\Repositories\FacebookFanpageRepository;
use App\Services\FacebookApiService;
use Illuminate\Http\Response;
use App\Helper\Helpers;
use App\Models\FacebookFanpage;
use App\Models\SocialPost;
use App\Models\User;
use App\Repositories\SocialPostRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SocialPostService
{
    private FacebookApiService $facebookApiService;
    public function __construct(
        private ChatGPTService $chatGPTService,
        private FacebookFanpageRepository $facebookFanpageRepository,
        private FacebookFanpageService $facebookFanpageService,
        private SocialPostRepository $socialPostRepository,
        private UserRepository $userRepository,
        private AIImageService $aIImageService,
        private CreditService $creditService,
        private UserService $userService,
        private StorageService $storageService
    ) {}

    public function getSocialPosts(): Collection
    {
        /** @var User $user */
        $user = auth('web')->user();

        return $user?->socialPosts()
            ->get();
    }


    public function store(array $params): SocialPost
    {
        $userId = auth('web')->id();

        if (isset($params['files'])) {
            $files = $this->updateUrlFiles($params['files']);

            foreach ($files as $file) {
                $path = Helpers::extractRelativePathFromSignedS3Url($file['url']);
                if ($file['type'] == 'video') {
                    $video =  $path;
                } else {
                    $medias[] = $path;
                }
            }
        }


        return $this->socialPostRepository->create([
            'user_id' => $userId,
            'content' => $params['content'],
            'medias' => $medias ?? null,
            'video' => $video ?? null,
            'title' => $params['title'] ?? null,
        ]);
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

    /**
     * @throws Exception
     */
    public function publish(SocialPost $socialPost): void
    {
        $socialPost->increment('attempts');
        $formData = [
            'post' => $socialPost->content,
            'platforms' => $socialPost->platforms,
        ];
        if (!empty($socialPost->medias) || !empty($socialPost->video)) {
            $this->uploadMediaToAyrshare($socialPost->medias, null, $formData, $socialPost->video);
        } else {
            $this->postContentToFacebook($formData);
        }

        $socialPost->update([
            'published_at' => now(),
        ]);
    }


    /**
     * @param array{content: string,user_id?: int, facebook_fanpage_id: integer, published: boolean, files: array<int, UploadedFile|array{type: 'video'|'image', s3_url?: string, url?: string}>, scheduled_publish_time: string|null} $params
     * @throws \App\Exceptions\SocialPostServiceException
     * @return array
     */
    public function postToFanpage(array $params): array
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $userId = Helpers::runInCronJobs() ? $params['user_id'] : auth('web')->id();

        $facebookFanpage = $this->facebookFanpageRepository->getFacebookFanpageOfUser($params['facebook_fanpage_id'], $userId);
        if (!$facebookFanpage) {
            throw new SocialPostServiceException('Không tìm thấy facebook Fanpage', Response::HTTP_NOT_FOUND);
        }

        try {
            if (isset($params['files'])) {
                $files = $this->updateUrlFiles($params['files']);
            }

            if (auth('admin')->check()) {
                $user = auth('web')->user();
            } else {
                $user =  $this->userRepository->find($userId);
            }

            $result = $this->facebookApiService->setConfig($user)
                ->postToFanpage(
                    $params,
                    $facebookFanpage->page_id,
                    $facebookFanpage->page_access_token,
                    $files ?? []
                );
        } catch (FacebookApiServiceException $e) {
            report($e);
            $page = $this->debugToken($e, $facebookFanpage);
            $result = $this->facebookApiService->setConfig($user)
                ->postToFanpage(
                    $params,
                    $page['page_id'],
                    $page['page_access_token'],
                    $files
                );
        }

        $this->storeSocialPost($userId, $params, $result['id'], $facebookFanpage, $files ?? []);

        return $result;
    }

    private function debugToken(FacebookApiServiceException $e, FacebookFanpage $facebookFanpage)
    {
        if ($e->getCode() !== Response::HTTP_UNAUTHORIZED) {
            throw $e;
        }

        $facebook = $facebookFanpage->facebook;

        if (!$facebook) {
            throw $e;
        }

        $debugToken = $this->facebookApiService->debugToken($facebook->user_access_token);

        if (!$debugToken->getIsValid()) {
            throw $e;
        }

        $newFanpagesToken = $this->facebookFanpageService->refreshAccessToken($facebook);

        if (!isset($newFanpagesToken[$facebookFanpage->id])) {
            throw $e;
        }

        $page = $newFanpagesToken[$facebookFanpage->id];
        return  $page;
    }

    /**
     * @param array<int, UploadedFile|array{type: 'video'|'image', s3_url?: string, url?: string}> $files
     * @return UploadedFile[]
     */
    private function updateUrlFiles(array $files): array
    {
        $uploads = [];
        foreach ($files as  $file) {
            if ($file instanceof UploadedFile) {
                $uploads[] = $this->uploadedFileToS3($file);
                continue;
            }

            if (isset($file['s3_url'])) {
                $uploads[] = [
                    'url' => $file['s3_url'],
                    'type' => $file['type']
                ];
                continue;
            }

            $uploads[] = $this->uploadedUrlToS3($file);
        }

        return $uploads;
    }


    private function uploadedFileToS3(UploadedFile $file): array
    {
        if (str_starts_with($file->getMimeType(), 'video/')) {
            $type = 'video';
        } else {
            $type = 'image';
        }

        $filePath = $file->store($type . 's', 's3');
        $s3Url = Helpers::preSignedS3Url($filePath);

        return [
            'url' => $s3Url,
            'type' => $type
        ];
    }

    private function uploadedUrlToS3(array $file): array
    {
        $client = new Client();

        $response = $client->get($file['url']);

        if ($response->getStatusCode() !== 200) {
            throw new SocialPostServiceException('Không thể tải file từ URL', Response::HTTP_NOT_FOUND);
        }

        $fileContents = $response->getBody()->getContents();
        $fileName = basename($file['url']);
        $parsedUrl = parse_url($fileName);
        $fileName = $parsedUrl['path'] ?? $fileName;

        $filePath = $file['type'] . 's/' . time() . '_' . $fileName;
        Storage::disk('s3')->put($filePath, $fileContents);
        $s3Url = Helpers::preSignedS3Url($filePath);

        return [
            'url' => $s3Url,
            'type' => $file['type']
        ];
    }

    public function getSocialPostFromTo(array $params): Collection
    {
        $params['user_id'] = auth('web')->id();

        $socialPosts = $this->socialPostRepository->getSocialPostFromTo($params);

        return $socialPosts;
    }

    public function storeSocialPost(int $userId, array $params, string $platformPostId, FacebookFanpage $facebookFanpage, array $files = [])
    {
        $now = now();
        $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
        $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);

        $data = [
            'platform_post_id' => $platformPostId,
            'published_at' => now(),
            'created_at' => now(),
        ];

        if (!is_null($params['scheduled_publish_time']) && $now->lessThan($scheduledPublishTime)) {
            $data['scheduled_at'] = new Carbon($params['scheduled_publish_time']);
            $data['published_at'] = new Carbon($params['scheduled_publish_time']);;
        }

        foreach ($files as $file) {
            $path = Helpers::extractRelativePathFromSignedS3Url($file['url']);
            if ($file['type'] == 'video') {
                $video =  $path;
            } else {
                $medias[] = $path;
            }
        }

        $socialPost = $this->socialPostRepository->create([
            'user_id' => $userId,
            'content' => $params['content'],
            'medias' => $medias ?? null,
            'video' => $video ?? null,
            'title' => $params['title'] ?? null,
        ]);

        $facebookFanpage->socialPosts()
            ->attach(
                $socialPost->id,
                $data
            );
    }

    public function updateToFanpage(array $params)
    {
        try {
            $socialPost = $this->socialPostRepository->findByFilter([
                'id' => $params['id'],
                'user_id' => auth('web')->id()
            ]);

            if (!$socialPost) {
                throw new SocialPostServiceException('Không tìm thấy bài viết', Response::HTTP_NOT_FOUND);
            }

            $facebookFanpage = $socialPost->facebookFanpages->first();

            if (!$facebookFanpage) {
                throw new SocialPostServiceException('Không tìm thấy facebook Fanpage', Response::HTTP_NOT_FOUND);
            }

            $hasUpdatedFile = $this->checkUploadFiles($params, $socialPost);
            $result = $this->updatePost($hasUpdatedFile, $params, $socialPost, $facebookFanpage->page_access_token);
        } catch (FacebookApiServiceException $e) {
            $page = $this->debugToken($e, $facebookFanpage);
            $result = $this->updatePost($hasUpdatedFile, $params, $socialPost, $page['page_access_token']);
        }

        if (!$hasUpdatedFile) {
            $this->updateSocialPost($params, $socialPost);
        }

        return $result;
    }

    private function updatePost(bool $hasUpdatedFile, array $params, SocialPost $socialPost, string $pageAccessToken): array
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $userId = Helpers::runInCronJobs() ? $params['user_id'] : auth('web')->id();

        if ($hasUpdatedFile) {
            $result = $this->postToFanpage($params);
            $result = $this->destroy($params['id']);
        } else {
            if (auth('admin')->check()) {
                $user = auth('web')->user();
            } else {
                $user =  $this->userRepository->find($userId);
            }

            $result = $this->facebookApiService->setConfig($user)->updateToFanpage(
                $params,
                $socialPost->socialPostFacebook->platform_post_id,
                $pageAccessToken,
                []
            );
        }

        return $result;
    }

    public function updateSocialPost(array $params, SocialPost $socialPost)
    {
        $socialPost->content = $params['content'];
        $socialPost->save();

        $now = now();
        $params['scheduled_publish_time'] = $params['scheduled_publish_time'] ?? null;
        $scheduledPublishTime = new Carbon($params['scheduled_publish_time']);
        $published = (int) $params['published'];

        if (
            !$published &&
            !is_null($params['scheduled_publish_time']) &&
            $now->lessThan($scheduledPublishTime)
        ) {
            $socialPost->socialPostFacebook->scheduled_at = new Carbon($params['scheduled_publish_time']);
            $socialPost->socialPostFacebook->published_at = null;
        }

        $socialPost->socialPostFacebook->save();
    }

    public function destroy(int $id)
    {
        try {
            $this->facebookApiService = app(FacebookApiService::class);
            $socialPost = $this->socialPostRepository->findByFilter([
                'id' => $id,
                'user_id' => auth('web')->id()
            ]);

            $user = auth('web')->user();

            if (!$socialPost) {
                throw new SocialPostServiceException('Không tìm thấy bài viết', Response::HTTP_NOT_FOUND);
            }

            $facebookFanpage = $socialPost->facebookFanpages->first();
            if (!$facebookFanpage) {
                throw new SocialPostServiceException('Không tìm thấy facebook Fanpage', Response::HTTP_NOT_FOUND);
            }

            $result = $this->facebookApiService->setConfig($user)
                ->deletePost(
                    $socialPost->socialPostFacebook->platform_post_id,
                    $facebookFanpage->page_access_token,
                );
        } catch (FacebookApiServiceException $e) {
            $page = $this->debugToken($e, $facebookFanpage);
            $result = $this->facebookApiService->deletePost(
                $socialPost->socialPostFacebook->platform_post_id,
                $page['page_access_token'],
            );
        }

        $socialPost->delete();

        return $result;
    }

    public function jobCreateContentAI(array $params)
    {
        $params['facebook_fanpage_id'] = $params['social_postable_id'];
        $params['user_id'] = auth('web')->id();
        $params['action'] = 'ai-text';
        $params['type'] = 'text';
        $params['modelKey'] = 'GPT-4o';
        list($startDate, $endDate) = $params['date_range'];
        $startDate = new Carbon($startDate);
        $endDate = new Carbon($endDate);
        $limitTopic = $startDate->diffInDays($endDate);
        ++$limitTopic;

        $startDate->addHours($params['time']['hours']);
        $startDate->addMinutes($params['time']['minutes']);

        if (isset($params['category']) && $params['category']) {
            $this->createPostByCategory($params, $limitTopic, $startDate);
        } else {
            $this->createPostByTopic($params, $limitTopic, $startDate);
        }
    }

    private function createPostByTopic(array $params, int $limitTopic, Carbon $startDate)
    {

        $model = AIModel::getModel($params['modelKey']);
        $promptTemplate = config('chatgpt.ai-create-topic');
        $prompt = str_replace(
            [':lang', ':limit', ':description'],
            [$params['lang'], $limitTopic, $params['description']],
            $promptTemplate
        );

        $checkCredit = $this->creditService->checkCredit([
            'model' => $params['modelKey'],
            'action' => $params['action'],
            'type' =>  $params['type'],
            'text' => $prompt
        ]);

        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->chatGPTService->getResponse($prompt, null, $model);
            if (!$response) {
                throw new SocialPostServiceException('Lỗi khi tạo chủ đề cho yêu cầu', Response::HTTP_BAD_REQUEST);
            }

            Auth::user()->chargeCredit($params['type'], $params['modelKey'], null, $params['action'], $prompt, 31, 31);

            $response = preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
            $topics = json_decode($response, true);


            foreach ($topics as $topic) {
                SocialPostAIFacebook::dispatch($startDate, $topic, $params)->onQueue("social-post-ai-facebook");
                $startDate->addDays(1);
            }
        } catch (\Throwable $e) {
            throw new SocialPostServiceException('Tạo bài viết bằng ChatGPT không thành công', Response::HTTP_BAD_REQUEST, $e);
        }
    }

    private function createPostByCategory(array $params, int $limitTopic, Carbon $startDate)
    {
        for ($i = 0; $i < $limitTopic; $i++) {
            SocialPostAIFacebook::dispatch($startDate, '', $params)->onQueue("social-post-ai-facebook");
            $startDate->addDays(1);
        }
    }

    public function createContentAIPostToFacebook(Carbon $scheduledPublishTime, string $topic, array $params): array
    {
        $data = $this->getDataAiPostToFacebook($scheduledPublishTime, $topic, $params);
        if (empty($data)) {
            return [];
        }

        $result = $this->postToFanpage($data);
        return $result;
    }

    private function getDataAiPostToFacebook(Carbon $scheduledPublishTime, string $topic,  array $params): array
    {
        $userId = $params['user_id'];
        $facebookFanpageId = $params['facebook_fanpage_id'];
        $lang = $params['lang'];
        $limit = $params['limit'];

        $minSchedule = now()->addMinutes(Facebook::MINUTES_MIN_SCHEDULE_POST);
        $maxSchedule = now()->addDays(Facebook::DAYS_MAX_SCHEDULE_POST);

        if ($maxSchedule->lessThan($scheduledPublishTime)) {
            return [];
        }

        if ($minSchedule->greaterThan($scheduledPublishTime)) {
            $data = [
                'published' => true,
                'scheduled_publish_time' => null,
                'facebook_fanpage_id' => $facebookFanpageId
            ];
        } else {
            $data = [
                'published' => false,
                'scheduled_publish_time' => $scheduledPublishTime,
                'facebook_fanpage_id' => $facebookFanpageId
            ];
        }

        $user =  $this->userRepository->find($userId);

        if (isset($params['category']) && $params['category']) {
            $data['content'] = $this->createContentAICategory($lang, $limit, $user, $params);
        } else {
            $data['content'] = $this->createContentAI($topic, $lang, $limit, $user, $params);
        }

        $data['user_id'] = $userId;
        $data['title'] = $topic;

        try {
            $prompt = $this->createPromptImage($params, $data['content'], $user);
            $imagesUrl = $this->creatImage($prompt, $user);

            $data['files'] = [
                [
                    'type' => 'image',
                    'url' => $imagesUrl
                ]
            ];
        } catch (\Throwable $th) {
            report($th);
            $data['files'] = [];
        }


        return $data;
    }

    private function createPromptImage(array $params, string $content, User $user): string
    {
        $promptCreateImage = 'Bạn là trợ lý ảo AI. Hãy tạo ra 1 câu prompt để cho AI tạo ra ảnh đại diện cho bài viết. Nội dung là :"' . $content . '". Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Sử dụng tiếng Anh. Độ dài không quá 1000 ký tự.';
        $checkCredit = $this->creditService->checkCredit([
            'model' => $params['modelKey'],
            'action' => $params['action'],
            'type' =>  $params['type'],
            'text' => $promptCreateImage,
            'user' => $user

        ]);
        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }
        $model = AIModel::getModel($params['modelKey']);
        $prompt = $this->chatGPTService->getResponse($promptCreateImage, null, $model, $user);
        $user->chargeCredit($params['type'], $params['modelKey'], null, $params['action'], $prompt, 31, 33);
        return $prompt;
    }

    private function creatImage(string $prompt, User $user): string
    {
        $checkCredit = $this->creditService->checkCredit([
            'model' => null,
            'action' => null,
            'type' =>  'flux-schnell',
            'text' => null,
            'user' => $user

        ]);
        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }
        $imagesUrl = $this->aIImageService->generateImageReplicate($prompt);
        $user->chargeCredit('flux-schnell', null, null, null, null, 31, 34);
        return $imagesUrl;
    }

    private function createContentAI(string $topic, string $lang, int $limit, User|null $user = null, array $params = []): string
    {
        $model = AIModel::getModel('GPT-4o');
        $promptTemplate = config('chatgpt.ai-create-content');
        $prompt = str_replace(
            [':lang', ':limit', ':platform', ':description'],
            [$lang, $limit, 'facebook', $topic],
            $promptTemplate
        );

        $checkCredit = $this->creditService->checkCredit([
            'model' => $params['modelKey'],
            'action' => $params['action'],
            'type' =>  $params['type'],
            'text' => $prompt,
            'user' => $user
        ]);

        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->chatGPTService->getResponse($prompt, null, $model, $user);
            if ($response) {
                $user->chargeCredit($params['type'], $params['modelKey'], null, $params['action'], $prompt, 31, 32);
                return preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
            }

            throw new SocialPostServiceException('ChatGPT API error', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            report($e);
            throw new SocialPostServiceException('Lỗi khi tạo nội dung AI', Response::HTTP_BAD_REQUEST, $e);
        }
    }
    private function createContentAICategory(string $lang, int $limit, User|null $user = null, array $params = []): string
    {
        $params['min'] = $params['limit'];
        $params['max'] =  (int)$params['limit'] + 400;
        $prompt =  $this->getPromptByCategory($params, $user);

        $checkCredit = $this->creditService->checkCredit([
            'model' => $params['modelKey'],
            'action' => $params['action'],
            'type' =>  $params['type'],
            'text' => $prompt,
            'user' => $user
        ]);

        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->getContent($prompt, $params, $lang);

            if ($response) {
                $user->chargeCredit($params['type'], $params['modelKey'], null, $params['action'], $prompt, 31, 32);
                return preg_replace('/(\*\*|###)(.*?)\1/', '', $response);
            }

            throw new SocialPostServiceException('ChatGPT API error', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            report($e);
            throw new SocialPostServiceException('Lỗi khi tạo nội dung AI', Response::HTTP_BAD_REQUEST, $e);
        }
    }
    private function getContent(string $content, array $params, $lang = 'Tiếng Việt')
    {
        $userPrompt = $content . "Chú ý:\n
         - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.\n
         - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.\n
         - Trả lời bằng ' . $lang.\n
         - Độ dài status: {min} - {max} từ\n
         ";

        $userPrompt = str_replace([
            '{min}',
            '{max}',
        ], [
            $params['min'],
            $params['max'],
        ], $userPrompt);

        return $this->chatGPTService->callGpt('', $userPrompt);
    }
    private function getPromptByCategory(array $params,  User|null $user = null)
    {
        $promptByCategories = config('chatgpt.postFb.category');
        $target = $this->getTarget($params);
        $feeling = $this->getFeeling($params);
        $style = $this->getStyle($params);
        $postFormat = $this->getPostFormat($params);
        Log::info('getPromptByCategory: ', [
            'TargetPromptByCategory' => $target,
            'FeelingPromptByCategory' => $feeling,
            'StylePromptByCategory' => $style,
            'PostFormatPromptByCategory' => $postFormat
        ]);
        $systemPrompt = $promptByCategories[$params['category']]['system_prompt'];
        $userPrompt = $promptByCategories[$params['category']]['user_prompt'];
        $code5 = $promptByCategories[$params['category']]['code_5'] ?? '';
        $userPrompt = str_replace([
            '{code_5_result}',
            '{bai_viet_qc_result}',
            '{thong_tin_dau_vao}',
            '{ten_du_an}',
            '{muc_tieu}',
            '{min}',
            '{max}',
            '{cam_xuc}',
            '{phong_cach}',
            '{dinh_dang_bai_viet}',
        ], [
            $code5,
            '',
            $params['description'],
            $params['project_name'],
            $target,
            $params['min'],
            $params['max'],
            $feeling,
            $style,
            $postFormat,
        ], $userPrompt);

        Log::info('userPrompt: ' . $userPrompt);

        $checkCredit = $this->creditService->checkCredit([
            'model' => $params['modelKey'],
            'action' => $params['action'],
            'type' =>  $params['type'],
            'text' => $userPrompt,
            'user' => $user
        ]);

        if (!$checkCredit['success']) {
            throw new SocialPostServiceException($checkCredit['message'], Response::HTTP_BAD_REQUEST);
        }

        $prompt = $this->chatGPTService->callGpt($systemPrompt, $userPrompt);

        if (!$prompt) {
            throw new SocialPostServiceException('Lỗi khi tạo chủ đề cho yêu cầu', Response::HTTP_BAD_REQUEST);
        }

        $user->chargeCredit($params['type'], $params['modelKey'], null, $params['action'], $prompt, 31, 31);

        return $prompt;
    }

    private function getOptionsPost(array $params, $type = 'goal'): array
    {
        if (!isset($params['options_rewrite']))
            return [];

        $options = collect($params['options_rewrite'])->first(function ($value) use ($type) {
            return $value['name'] === $type;
        });
        return collect($options['options'])->pluck('value')->toArray();
    }

    private function getTarget(array $params): string|null
    {
        $options = $this->getOptionsPost($params, 'goal');
        if (!empty($params['target']))
            $options[] = $params['target'];

        return $options[array_rand($options)];
    }

    private function getFeeling(array $params): string|null
    {
        $options = $this->getOptionsPost($params, 'desired_emotion');
        if (empty($options))
            return null;

        return $options[array_rand($options)];
    }

    private function getStyle(array $params): string|null
    {
        $options = $this->getOptionsPost($params, 'writing_style');
        if (empty($options))
            return null;

        return $options[array_rand($options)];
    }

    private function getPostFormat(array $params): string|null
    {
        $options = $this->getOptionsPost($params, 'post_format');
        if (empty($options))
            return null;

        $filtered = collect($options)->reject(function (string $value) {
            return  in_array($value, ['Kịch bản Video quảng cáo']);
        });

        $options =  array_values($filtered->all());

        return $options[array_rand($options)];
    }


    public function checkUploadFiles(array $params, SocialPost $socialPost): bool
    {
        $file_paths = [];
        if (isset($params['files'])) {
            foreach ($params['files'] as  $file) {
                $file_paths[] = Helpers::extractRelativePathFromSignedS3Url($file['s3_url']);
            }
        }

        $media_paths = [];
        foreach ($socialPost->medias as $media) {
            $media_paths[] = Helpers::extractRelativePathFromSignedS3Url($media);
        }

        if ($socialPost->video) {
            $media_paths[] = Helpers::extractRelativePathFromSignedS3Url($socialPost->video);
        }

        $difference = array_diff($file_paths, $media_paths);

        return count($difference);
    }

    /**
     * @param array{content: array<int, array{conversations: string}>, user_id?: int, facebook_fanpage_id: integer, published: boolean, files: array<int, UploadedFile|array{type: 'video'|'image', s3_url?: string, url?: string}>, scheduled_publish_time: string|null} $params
     * @throws \App\Exceptions\SocialPostServiceException
     * @return array
     */
    public function multiPostToFanpage(array $params): array
    {
        foreach ($params['post_datas'] as $key => $post_data) {
            $data =  $this->getDataPostToFacebook($params['facebook_fanpage_id'], $post_data);
            $result = $this->postToFanpage($data);
        }
        return $result;
    }

    private function getDataPostToFacebook(int $facebookFanpageId, array $post_data): array
    {
        $post_data['content'] = Helpers::convertHtmlToText($post_data['content']);
        $post_data['facebook_fanpage_id'] = $facebookFanpageId;
        return $post_data;
    }

    public function storeSocial(array $params): array
    {

        switch ($params['social_postable_type']) {
            case 'FacebookFanpage':
                $params['facebook_fanpage_id'] = $params['social_postable_id'];
                $result = $this->postToFanpage($params);
                break;
            default:

                break;
        }

        return $result;
    }

    public function updateSocial(array $params): array
    {

        switch ($params['social_postable_type']) {
            case 'FacebookFanpage':
                $params['facebook_fanpage_id'] = $params['social_postable_id'];
                $result = $this->updateToFanpage($params);
                break;
            default:

                break;
        }

        return $result;
    }
}
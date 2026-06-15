<?php

namespace App\Services;

use App\Exceptions\FacebookFanpageServiceException;
use App\Models\Facebook;
use App\Models\FacebookFanpage;
use App\Repositories\FacebookFanpageRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;

class FacebookFanpageService
{
    private FacebookApiService $facebookApiService;
    public function __construct(
        private FacebookFanpageRepository $facebookFanpageRepository,
        private FacebookService $facebookService,
    ) {}

    public function store(array $params)
    {
        $facebooks = $this->facebookService->getUserFacebooks();
        $facebook = $facebooks->where('id', $params['facebook_id'])->first();
        if (!$facebook) {
            throw new FacebookFanpageServiceException('Không tìm thấy facebook', Response::HTTP_NOT_FOUND);
        }

        $existingPageIds = $facebook->facebookFanpages->pluck('page_id');
        $uniqueFanpages = collect($params['fanpages'])->unique('id');

        $nonExistingPageIds  = $uniqueFanpages->filter(
            function (array $value,  int $key) use ($existingPageIds) {
                return !$existingPageIds->contains($value['id']);;
            }
        );

        $inserts = [];
        foreach ($nonExistingPageIds as $fanpage) {
            $inserts[] = [
                'facebook_id' => $facebook->id,
                'page_id' => $fanpage['id'],
                'page_name' => $fanpage['name'],
                'page_access_token' => $fanpage['access_token'],
                'page_image' => $fanpage['image'],
                'page_access_token_expires_at' => $facebook->user_access_token_expires_at,
                'created_at' => Carbon::now()
            ];
        }

        return $this->facebookFanpageRepository->model->insert($inserts);
    }

    public function getFanpages(Facebook|null $facebook): array
    {
        if (!$facebook) {
            return [];
        }

        $facebookFanpages = $facebook->facebookFanpages->map(function ($item) {
            return collect($item)->only(['id', 'facebook_id', 'page_id', 'page_name', 'page_image']);
        });

        return $facebookFanpages->toArray();
    }

    public function getFanpagesFacebookApi(array $params): array
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $facebooks = $this->facebookService->getUserFacebooks();
        $facebook = $facebooks->where('id', $params['facebook_id'])->first();
        if (!$facebook) {
            throw new FacebookFanpageServiceException('Không tìm thấy facebook', Response::HTTP_NOT_FOUND);
        }
        $accessToken = $this->facebookService->refreshAccessToken($facebook);

        $result = $this->facebookApiService->getFanpagesFacebookApi($accessToken['user_access_token'], $params['paging_cursors_after']);
        return $result;
    }

    public function refreshAccessToken(Facebook $facebook): array
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $fanpagesApi = $this->facebookApiService->getFanpagesFacebookApi($facebook->user_access_token);
        $fanpagesApi = collect($fanpagesApi['data']);
        $fanpagesDb = $this->getFanpages($facebook);

        $newFanpagesToken = [];

        foreach ($fanpagesDb as $fanpageDb) {
            $pageApi = $fanpagesApi->firstWhere('id', $fanpageDb['page_id']);

            if (!$pageApi) {
                $this->facebookFanpageRepository->deleteByFilter(['id' => $fanpageDb['id']]);
            } else {
                $this->facebookFanpageRepository->updateOrCreate(
                    ['id' => $fanpageDb['id']],
                    [
                        'page_name' => $pageApi['name'],
                        'page_access_token' => $pageApi['access_token'],
                        'page_access_token_expires_at' => $facebook->user_access_token_expires_at,
                    ]
                );
            }

            $newFanpagesToken[$fanpageDb['id']] = [
                'page_id' => $pageApi['id'],
                'page_name' => $pageApi['name'],
                'page_access_token' => $pageApi['access_token'],
                'page_access_token_expires_at' => $facebook->user_access_token_expires_at,
            ];
        }

        return $newFanpagesToken;
    }

    public function deletePage($pageId) {
        try {
            $fanpage = $this->facebookFanpageRepository->first(["id" => $pageId]);
            if(!$fanpage) {
                return response()->json(["success" => false, "message" => "Không tồn tại fanpage"]);
            }
            $res = $this->facebookFanpageRepository->deleteByFilter(["id" => $pageId]);
            if($res) {
                return response()->json(["success" => true, "message" => "Xóa page ".$fanpage->page_name." thành công"]);
            } else {
                return response()->json(["success" => false, "message" => "Có lỗi xày ra trong quá trình thực hiện"]);
            }
        } catch(\Exception $ex) {
            return response()->json(["success" => false, "message" => "Có lỗi xày ra trong quá trình thực hiện"]);
        }
    }
}

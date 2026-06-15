<?php

namespace App\Services;

use App\Models\Facebook;
use App\Repositories\FacebookRepository;


class FacebookService
{
    private FacebookApiService $facebookApiService;

    public function __construct(
        private FacebookRepository $facebookRepository,
    ) {}

    public function getUserFacebooks()
    {
        $user = auth('web')->user();
        return $user->facebooks;
    }

    public function update(array $params): Facebook
    {
        $user = auth('web')->user();
        $facebook = $user->facebooks->first();

        $facebook->facebook_user_id = $params['facebook_user_id'];
        $facebook->facebook_user_name = $params['facebook_user_name'];
        $facebook->user_access_token = $params['user_access_token'];
        $facebook->user_access_token_expires_at = $params['user_access_token_expires_at'];

        $facebook->save();

        return  $facebook;
    }

    public function refreshAccessToken(Facebook $facebook): array
    {
        $this->facebookApiService = app(FacebookApiService::class);

        $accessToken = $this->facebookApiService->refreshAccessToken($facebook);
        if ($facebook->user_access_token !== $accessToken['user_access_token']) {
            $user = $this->facebookApiService->getUserFacebookApi($accessToken['user_access_token']);
            $facebook->facebook_user_name = $user['facebook_user_name'];
            $facebook->user_access_token = $accessToken['user_access_token'];
            $facebook->user_access_token_expires_at = $accessToken['user_access_token_expires_at'];
            $facebook->save();
        }

        return  $accessToken;
    }

    public function store(array $params): Facebook
    {

        $user = auth('web')->user();
        $facebook = $user->facebooks->first();
        $facebook = $user->facebooks()->updateOrCreate(
            ['facebooks.id' => $facebook?->id],
            [
                'app_id' => $params['app_id'],
                'app_secret' => $params['app_secret'],
            ]
        );
 
        return  $facebook;
    }
}

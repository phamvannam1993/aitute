<?php

namespace App\Services;

use App\Services\FacebookApiService;

class SocialService
{
    private FacebookApiService $facebookApiService;
    public function __construct(
        private FacebookService $facebookService,
        private AuthService $authService,
    ) {}


    public function handleCallback()
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $fbUser = $this->facebookApiService->handleCallback();
        if (!auth('web')->check()) {
            $this->authService->loginFacebook($fbUser);
        }
       
        $this->facebookService->update($fbUser);

        return $fbUser;
    }
}

<?php

namespace App\Services;

use App\Http\Resources\SocialPost\ListSocialPostResource;
use App\Enums\SocialPostTypeEnum;

class CalendarService
{
    public function __construct(
        private FacebookFanpageService $facebookFanpageService,
        private FacebookService $facebookService,
        private SocialPostService $socialPostService,

    ) {}

    public function getFacebooksFanpagesUser(): array
    {
        $facebooks = $this->facebookService->getUserFacebooks();
        $fanpages = $this->facebookFanpageService->getFanpages($facebooks->first());

        $facebooks = $facebooks->map(function ($item) {
            return collect($item)->only(['id', 'user_access_token']);
        })->toArray();

        return [
            'facebooks' => $facebooks ?: [],
            'fanpages' => $fanpages ?: [],
        ];
    }

    public function getCalendar(array $params)
    {
        $socialPosts = $this->socialPostService->getSocialPostFromTo($params);
        return ListSocialPostResource::collection($socialPosts);
    }

    public function getSocialPostableType(array $params): array
    {
        $socials =  match ($params['social_postable_type']) {
            SocialPostTypeEnum::FacebookFanpage->name     => ($this->getFacebooksFanpagesUser())['fanpages'],
        };

        return $socials;
    }    
}

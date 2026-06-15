<?php

namespace App\Helper;

use App\Enums\SocialPostEnum;
use App\Models\SocialPost;
use Carbon\Carbon;

class SocialPostHelper
{
    public static function getStatus(SocialPost $socialPost): SocialPostEnum
    {
        $publishedAt = new Carbon($socialPost->published_at);
        $scheduledAt = new Carbon($socialPost->scheduled_at);

        if ($socialPost->published_at && now()->greaterThan($publishedAt)) {
            return SocialPostEnum::PUBLISHED;
        }

        if (
            $socialPost->platform_post_id &&
            $socialPost->scheduled_at &&
            now()->greaterThan($scheduledAt)
        ) {
            return SocialPostEnum::PUBLISHED;
        }

        if ($socialPost->scheduled_at && now()->lessThan($scheduledAt)) {
            return SocialPostEnum::SCHEDULED;
        }

        if ($socialPost->attempts >= SocialPost::MAX_ATTEMPTS) {
            return SocialPostEnum::FAILED;
        }

        return SocialPostEnum::RETRYING;
    }
}

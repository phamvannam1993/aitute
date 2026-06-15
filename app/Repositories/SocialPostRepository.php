<?php

namespace App\Repositories;

use App\Enums\SocialPostTypeEnum;
use App\Models\SocialPost;
use Carbon\Carbon;

class SocialPostRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return SocialPost::class;
    }

    public function getSocialPostFromTo(array $params)
    {
        $from = Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay();
        $to = Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay();

        $socialPostableType =  SocialPostTypeEnum::array()[$params['social_postable_type']];

        $list =  $this->getModel()->select(
            'social_posts.id',
            'social_posts.user_id',
            'social_posts.content',
            'social_posts.medias',
            'social_posts.video',
            'social_postables.attempts',
            'social_postables.scheduled_at',
            'social_postables.published_at',
            'social_postables.platform_post_id'
        )
            ->join('social_postables', 'social_postables.social_post_id', '=', 'social_posts.id')
            ->where('social_posts.user_id', $params['user_id']);

        if (isset($params['social_postable_id'])) {
            $list =  $list->where([
                ['social_postable_id', $params['social_postable_id']],
                ['social_postable_type', $socialPostableType],
            ]);
        }

        if (isset($params['from'])) {
            $list =  $list->where(function ($query) use ($from, $to) {
                $query =  $query->whereBetween('scheduled_at', [$from, $to])
                    ->orWhereBetween('published_at', [$from, $to]);
            });
        }

        return  $list
            ->orderBy('social_postables.published_at')
            ->orderBy('social_postables.scheduled_at')
            ->get();
    }

    public function getSocialPostByPlatformPostId(array $params)
    {

        $list =  $this->getModel()->select(
            'social_posts.id',
            'social_posts.user_id',
            'social_postables.platform_post_id'
        )
            ->join('social_postables', 'social_postables.social_post_id', '=', 'social_posts.id')
            ->where('social_posts.user_id', $params['user_id']);

        if (isset($params['social_post_id'])) {
            $list =  $list->where([
                ['social_postables.social_post_id', $params['social_post_id']],
            ]);
        }

        return  $list->first();
    }
}

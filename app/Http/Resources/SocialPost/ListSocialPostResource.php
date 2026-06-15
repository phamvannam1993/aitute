<?php

namespace App\Http\Resources\SocialPost;

use App\Helper\SocialPostHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListSocialPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'scheduled_at' => $this->scheduled_at,
            'published_at' => $this->published_at,
            'attempts' => $this->attempts,
            'user_id' => $this->user_id,
            'platform_post_id' => $this->platform_post_id,
            'medias' => $this->medias,
            'video' => $this->video,
            'status' => SocialPostHelper::getStatus($this->resource)->value,
        ];
    }
}

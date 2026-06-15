<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'medias',
        'video',
        'user_id',
    ];

    const MAX_ATTEMPTS = 3;

    public function socialPostFacebook(): HasOne
    {
        return $this->hasOne(SocialPostable::class)
            ->where('social_postable_type', FacebookFanpage::class);
    }

    public function facebookFanpages(): MorphToMany
    {
        return $this->morphedByMany(FacebookFanpage::class, 'social_postable')
            ->wherePivot('social_postable_type', FacebookFanpage::class);
    }
    public function medias(): Attribute
    {
        return Attribute::make(
            get: static function (?string $value) {
                if (!$value) {
                    return [];
                }
                $images = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

                return array_map(fn($image) => Helpers::preSignedS3Url($image), $images);
            },
            set: static fn(string|array|null $value) => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value,
        );
    }

    public function video(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value) => $value ? Helpers::preSignedS3Url($value) : $value,
        );
    }
    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
}

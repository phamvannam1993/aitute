<?php

namespace App\Models;

use App\Helper\Helpers;
use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PictoryVideo extends Model
{
    use HasFactory;

    protected $table = 'pictory_videos';

    protected $fillable = [
        'user_id',
        'job_id',
        'article_url',
        'video_description',
        'title',
        'renderParams',
        'preview_url',
        's3_url',
        'final_url',
        'is_upload_audio',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
    protected function finalUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
}

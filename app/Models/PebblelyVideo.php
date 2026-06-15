<?php

namespace App\Models;

use App\Helper\Helpers;
use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PebblelyVideo extends Model
{
    use HasFactory;

    protected $table = 'pebblely_videos';

    protected $fillable = [
        'user_id',
        'theme',
        'input_url',
        's3_url'
    ];

    protected function inputUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
}

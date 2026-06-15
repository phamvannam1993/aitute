<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class McVirtual extends Model
{
    use HasFactory;
    protected $table = 'mc_virtuals';
    protected $fillable = [
        'user_id',
        'avatar_url',
        'voice_id',
        'mc_virtual_id',
        'result_url',
        'content',
        'status',
        'ai_generated_media_id',
        'is_upload_audio',
        's3_url_video_audio',
        'type',
        'created_by',
        'background_url',
        'duration',
        'info_json',
        'model',
        'transcription_url',
    ];
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function resultUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function backgroundUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
    protected function transcriptionUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}

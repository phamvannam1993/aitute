<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AIGeneratedMedia extends Model
{
    use HasFactory;
    protected $table = 'ai_generated_medias';

    protected $fillable = [
        'user_id',
        'ai_assistant_id',
        'description',
        'style',
        'artist',
        'width',
        'height',
        's3_url',
        's3_url_mobile',
        'type',
        'voice_type',
        'task_id',
        'duration',
        'model',
        'thumbnail',
        'is_upload_audio',
        'is_upload_image',
        'is_convert_ratio',
        's3_url_video_image',
        's3_url_video_ratio',
        's3_url_video_result',
        'ratio',
        's3_url_video_audio',
        'transcription_url',
        'error_msg',
        'ssml_text',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
    protected function transcriptionUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function ssmlText(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
    protected function s3UrlMobile(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
}

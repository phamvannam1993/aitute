<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lipsync extends Model
{
    protected $table = 'lipsync';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'audio',
        'video',
        'lip_sync_id',
        'is_upload_audio',
        's3_url_video_audio',
        'transcription_url',
        'thumbnail',
        'result',
        'duration',
        'status'
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
    protected function result(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
    protected function video(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
}

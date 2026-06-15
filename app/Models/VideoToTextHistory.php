<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VideoToTextHistory extends Model
{
    protected $table = 'video_to_text_histories';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'youtube_url',
        'content',
        'video_title',
        'audio_url'
    ];

    protected $appends = [
        'content_text'
    ];

    public function getContentTextAttribute() {
        $textContent = strip_tags($this->attributes['content']);
        if (strlen($textContent) > 140) {
            $textContent = substr($textContent, 0, 140) . '...';
        }

        return $textContent;
    }
}

<?php

namespace App\Models;

use App\Helper\Helpers;
use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiTuTeMessage extends Model
{
    use HasFactory, SoftDeletes;
    use S3ServiceTraits;

    protected $table = 'ai_tu_te_messages';

    protected $fillable = [
        'conversation_id',
        'sender',
        'content',
        'metadata',
        'is_processed',
        'is_hidden',
        'query',
        'path_image',
        'updated_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_processed' => 'boolean'
    ];

    protected $appends = ['path_image_url', 'beauty_content'];

    public function getPathImageUrlAttribute()
    {
        return !empty($this->path_image) ? $this->getUrl($this->path_image) : null;
    }

    public function conversation()
    {
        return $this->belongsTo(AiTuTeConversation::class, 'conversation_id');
    }

    public function files()
    {
        return $this->hasMany(AITuTeFile::class, 'message_id');
    }

    public function getBeautyContentAttribute()
    {
        if ($this->content) {
            $content = $this->content;

            if (Helpers::isJson($this->content)) {
                return $content;
            }
            //Convert string dạng [f66c7606-ae27-40ff-8d2c-32c5d0bc3ce5, Khu đô thị Nam Thăng Long - Ciputra Hà Nội] -> Khu đô thị Nam Thăng Long - Ciputra Hà Nội
            preg_match('/,\s*(.*?)\]$/', $content, $matches);

            $content = $matches[1] ?? $content;

            return $content;
        }else {
            return 'N/A';
        }
    }
}

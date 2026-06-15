<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use App\Helpers;

class AIChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [ 'ai_chat_session_id', 'sender', 'user_id', 'content_type', 'file_url', 'content', 'content_uploads', 'model', 'language', 'file_type'];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
}

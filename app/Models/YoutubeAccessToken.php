<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'user_id', 'access_token', 'created_at', 'updated_at'
    ];

    protected $table = "youtube_access_tokens";
}

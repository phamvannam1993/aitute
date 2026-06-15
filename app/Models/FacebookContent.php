<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'project_id',
        'goal',
        'facebook_fanpage_id',
        'content',
        'videos',
        'images',
        'status',
        'post_date',
        'is_post',
        'is_video'
    ];
}

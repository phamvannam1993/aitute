<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class FacebookFanpage extends Model
{
    use HasFactory;
    protected $fillable = [
        'facebook_id',
        'page_id',
        'page_name',
        'page_access_token',
        'page_access_token_expires_at',
    ];

    public function facebook()
    {
        return $this->belongsTo(Facebook::class);
    }

    public function socialPosts(): MorphToMany
    {
        return $this->morphToMany(SocialPost::class, 'social_postable');
    }
}

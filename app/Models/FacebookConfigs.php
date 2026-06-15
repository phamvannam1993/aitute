<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookConfigs extends Model
{
    use HasFactory;

    protected $table = 'facebook_configs';

    protected $fillable = [
        'page_id',
        'page_name',
        'page_picture',
        'page_access_token',
        'page_url',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
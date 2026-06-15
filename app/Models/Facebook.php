<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facebook extends Model
{
    use HasFactory;
    protected $fillable = [
        'facebook_user_id',
        'facebook_user_name',
        'user_access_token',
        'user_access_token_expires_at',
        'app_id',
        'app_secret',
    ];

    public function facebookFanpages(): HasMany
    {
        return $this->hasMany(FacebookFanpage::class);
    }
}

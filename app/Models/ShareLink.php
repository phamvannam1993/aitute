<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShareLink extends Model
{
    protected $fillable = [
        'user_id',
        'link_token',
        'share_linkable_id',
        'share_linkable_type'
    ];

    public function shareLinkable(): MorphTo
    {
        return $this->morphTo();
    }
}

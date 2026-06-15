<?php

namespace App\Models;

use App\Helper\Helpers;
use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CreatomateVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'creatomate_template_id',
        'title',
        'snapshot_url',
        'content',
        'result'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected function result(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
}

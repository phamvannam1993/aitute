<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LikeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyAIImageCollection extends Model
{
    use HasFactory, LikeScope;
    protected $table = 'my_ai_image_collections';

    protected $fillable = [
        'title',
        's3_url'
    ];

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    public $timestamps = true;

    public function categories()
    {
        return $this->hasMany(MyAIImageTemplateCategory::class, 'my_ai_image_collection_id');
    }
}

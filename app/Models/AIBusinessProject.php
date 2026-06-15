<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

class AIBusinessProject extends Model
{
    protected $table = 'ai_business_projects';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'image_product',
        'files',
        'data_json',
        'count',
        'meta_data',
        'is_use_backup_flow',
        'content',
        'image_urls',
        'video_url',
        'image_model',
        'model_product',
        'project_type',
        'total_campaign_days'
    ];
    // Thêm cast để Laravel tự động chuyển đổi JSON
    protected $casts = [
        'files' => 'array',
        'data_json' => 'array'
    ];
    protected function imageProduct(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes) {
                return Helpers::preSignedS3Url($value);
            }
        );
    }
    protected function imageModel(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes) {
                return Helpers::preSignedS3Url($value);
            }
        );
    }

    protected function modelProduct(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes) {
                return Helpers::preSignedS3Url($value);
            }
        );
    }

    protected function videoUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes) {
                return Helpers::preSignedS3Url($value);
            }
        );
    }

    protected function files(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes) {
                $files = json_decode($value) ?? [];
                $result = [];
                foreach ($files as $file) {
                    if (isset($file->path)) {
                        $file->url = Helpers::preSignedS3Url($file->path);
                    }
                    $result[] = $file;
                }
                return $result;
            }
        );
    }
}

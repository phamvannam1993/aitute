<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

class AIProjectExpert extends Model
{
    protected $table = 'ai_project_experts';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'image_product',
        'model_product',
        'files',
        'answers',
        'expert_info',
        'buss_info',
        'analysis_sections',
        'communication_strategy',
        'results'
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
    protected function modelProduct(): Attribute
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

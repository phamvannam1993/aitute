<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AIPresenter extends Model
{
    protected $table = 'ai_presenters';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        's3_url',
    ];

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }
}

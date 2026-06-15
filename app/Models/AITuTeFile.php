<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;


class AITuTeFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_tu_te_files';

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'is_processed',
        'is_before_care',
        'metadata'
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'metadata' => 'array',
        'is_processed' => 'boolean'
    ];

    public function message()
    {
        return $this->belongsTo(AiTuTeMessage::class, 'message_id');
    }

    // Helper method để lấy full URL
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($this->file_path),
        );
    }
    public function getFullUrlAttribute()
    {
        return Storage::disk('s3')->url($this->file_path);
    }
}

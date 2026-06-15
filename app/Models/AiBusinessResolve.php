<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AiBusinessResolve extends Model
{
    protected $table = 'ai_business_resolves';

    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPES = [
        self::TYPE_IMAGE => 'Hình ảnh',
        self::TYPE_VIDEO => 'Video',
    ];

    use HasFactory;
    protected $fillable = [
        'user_id',
        'project_id',
        'type',
        's3_url',
        'path',
        'prompt',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'width' => 'integer',
        'height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship với user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship với project
    public function project(): BelongsTo
    {
        return $this->belongsTo(AiBusinessProject::class, 'project_id');
    }
}

<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiAssistant extends Model
{
    use S3ServiceTraits;
    use HasFactory, SoftDeletes;

    protected $table = 'ai_assistants';

    protected $fillable = [
        'admin_id',
        'occupation_id',
        'operation_id',
        'type',
        'name',
        'description',
        'thumbnail',
        'step_ais',
        'pdf_content',
        'is_public'
    ];

    protected $dates = ['deleted_at'];
    protected $appends = ['thumbnail_url'];

    protected $casts = [
        'is_public' => 'boolean',
    ];
    // Accessor cho image_url
    public function getThumbnailUrlAttribute()
    {
        return !empty($this->thumbnail) ? $this->getUrl($this->thumbnail) : null;
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(AssistantFavorites::class, 'ai_assistant_id', 'id');
    }

    // Quan hệ với steps
    public function step_ais()
    {
        return $this->hasMany(StepAi::class);
    }

    // Set and get steps (JSON column)
    public function setStepAisAttribute($value)
    {
        $this->attributes['step_ais'] = json_encode($value);
    }

    public function getStepAisAttribute($value)
    {
        return json_decode($value, true);
    }

    public function criteria()
    {
        return $this->hasMany(AiAssistantCriteria::class);
    }
}

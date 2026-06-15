<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AIAssistantHistories extends Model
{
    use HasFactory;

    protected $table = 'ai_assistant_histories';

    protected $fillable = [
        'user_id',
        'ai_assistant_id',
        'type',
        'prompt',
        'media_link',
        'response',
        'css_style',
        'ai_text_conversation_id',
        'step_ais'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }

    public function conversation()
    {
        return $this->belongsTo(AiTextConversation::class, 'ai_text_conversation_id');
    }
}

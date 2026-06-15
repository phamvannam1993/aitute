<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiTuTeConversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_tu_te_conversations';

    protected $fillable = [
        'user_id',
        'dify_conversation_id',
        'page_id',
        'title',
        'status',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AiTuTeMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(AiTuTeMessage::class, 'conversation_id')
            ->latest();
    }

    public function facebookConfig()
    {
        return $this->belongsTo(FacebookConfigs::class, 'page_id', 'page_id');
    }
}

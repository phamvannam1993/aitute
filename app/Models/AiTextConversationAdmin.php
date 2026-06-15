<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTextConversationAdmin extends Model
{
    use HasFactory;

    protected $table = 'ai_text_conversations_admin';

    protected $fillable = [
        'messages',
    ];

    protected $casts = [
        'messages' => 'array',
    ];
}

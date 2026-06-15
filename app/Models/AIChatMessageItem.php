<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatMessageItem extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_message_items';

    protected $fillable = [ 'chat_message_id', 'user_id', 'sender', 'content'];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
}

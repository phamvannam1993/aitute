<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendChatHistories extends Model
{
    protected $table = 'send_chat_histories';
    use HasFactory;

    protected $fillable = [
        'dify_conversation_id',
        'is_dify_backup',
        'project_id'
    ];
}

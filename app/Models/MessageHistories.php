<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageHistories extends Model
{
    use HasFactory;

    protected $table = 'message_histories';

    protected $fillable = [
        'id',
        'source_id',
        'role',
        'message',
        'reply_id',
        'user_id',
        'user_page_id',
        'status',
        'text_content',
        'page_id',
        'price',
        'comment',
        'created_at',
        'updated_at'
    ];
}
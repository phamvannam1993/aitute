<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIChatSession extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_sessions';

    protected $fillable = [ 'user_id', 'ai_assistant_id'];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    public function messages(): HasMany
    {
        return $this->hasMany(AIChatMessage::class, 'ai_chat_session_id');
    }
    public function oldestMessage()
    {
        return $this->hasOne(AIChatMessage::class, 'ai_chat_session_id')
                    ->orderBy('created_at', 'asc');
    }
}

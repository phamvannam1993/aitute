<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantFavorites extends Model
{
    use HasFactory;

    protected $table = 'assistant_favorites';

    protected $fillable = [
        'user_id',
        'ai_assistant_id',
    ];

    public function aiAssistant()
    {
        return $this->belongsTo(AiAssistant::class, 'ai_assistant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

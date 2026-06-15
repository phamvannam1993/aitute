<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyAIImage extends Model
{
    protected $table = 'my_ai_images';

    protected $fillable = [
        'user_id', 'model_name', 'trigger_word', 'model_enpoint', 'status', 'start_time', 'end_time', 'alias_name', 'replicate_id'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getProcessingTimeInSecondsAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return strtotime($this->end_time) - strtotime($this->start_time);
        }

        return null;
    }
}

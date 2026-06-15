<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepAi extends Model
{

    protected $table = 'step_ais';
    use HasFactory;

    protected $fillable = ['ai_assistant_id', 'step_name', 'step_description', 'position'];

    public function aiAssistant()
    {
        return $this->belongsTo(AiAssistant::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SlideAiTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name'
    ];
    
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }
}

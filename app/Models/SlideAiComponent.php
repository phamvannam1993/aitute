<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SlideAiComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_name',
        'template_id',
        'type'
    ];
}

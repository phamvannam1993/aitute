<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIPricing extends Model
{
    protected $table = 'ai_pricings';
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'model_code',
        'cost_normal',
        'cost_input',
        'cost_output',
        'note'
    ];
}

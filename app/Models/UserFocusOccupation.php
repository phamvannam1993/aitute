<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFocusOccupation extends Model
{
    use HasFactory;

    protected $table = 'user_focus_occupations';

    protected $fillable = [
        'user_id',
        'occupation_id',
    ];
}

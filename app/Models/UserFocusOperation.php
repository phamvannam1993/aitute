<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFocusOperation extends Model
{
    use HasFactory;

    protected $table = 'user_focus_operations';

    protected $fillable = [
        'user_id',
        'operation_id',
    ];
}

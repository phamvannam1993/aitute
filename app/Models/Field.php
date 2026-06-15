<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'field_user', 'field_id', 'user_id');
    }
}

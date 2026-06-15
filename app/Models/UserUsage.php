<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUsage extends Model
{
    protected $table = 'user_usage';

    protected $fillable = ['user_id', 'assistant_type', 'usage_count'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageLimit extends Model
{
    protected $table = 'usage_limits';
    protected $fillable = ['assistant_type', 'default_limit'];
    public $timestamps = true;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'interests';
    protected $fillable = ['user_id', 'operation_id', 'occupation_id', 'is_interested'];
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }
}

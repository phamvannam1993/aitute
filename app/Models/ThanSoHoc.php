<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanSoHoc extends Model
{
    use HasFactory;

    protected $table = 'than_so_hoc';

    protected $fillable = [
        'user_id',
        'fullname',
        'dob',
        'numerology_data'
    ];

    protected $casts = [
        'numerology_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
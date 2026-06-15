<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatomateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'title',
        'template_thumbnail',
        'structure',
        'resolution',
        'demo_url'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
}

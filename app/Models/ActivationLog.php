<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivationLog extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'key', 'device_info', 'activated_at', 'device_summary', 'ip'];
}

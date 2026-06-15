<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FailedAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'attempts', 'locked_until'];
}

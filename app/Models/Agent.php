<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = ['coupon_parent', 'code', 'name', 'email', 'phone', 'address'];

    public function affKeys()
    {
        return $this->hasMany(AffKey::class, 'agent_id');
    }
}

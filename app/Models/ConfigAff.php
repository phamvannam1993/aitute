<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigAff extends Model
{
    use HasFactory;

    protected $table = 'config_aff';

    protected $fillable = [
        'name',
        'code',
        'credit',
        'month',
        'price',
        'day'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function affKeys()
    {
        return $this->hasMany(AffKey::class, 'config_aff_id');
    }
}

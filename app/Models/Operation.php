<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operation extends Model
{
    use S3ServiceTraits;
    use HasFactory, SoftDeletes;

    protected $table = 'operations';

    protected $fillable = [
        'user_id',
        'occupation_id',
        'name',
        'description',
        'image',
    ];

    /**
     * Các thuộc tính mà SoftDeletes sẽ tự động quản lý.
     */
    protected $dates = ['deleted_at'];
    protected $appends = ['image_url'];

    // Accessor cho image_url
    public function getImageUrlAttribute()
    {
        return !empty($this->image) ? $this->getUrl($this->image) : null;
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    public function ais()
    {
        return $this->HasMany(AiAssistant::class, 'operation_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

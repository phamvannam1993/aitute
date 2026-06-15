<?php

namespace App\Models;

use App\Traits\S3ServiceTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occupation extends Model
{
    use S3ServiceTraits;
    use HasFactory, SoftDeletes;

    protected $table = 'occupations';

    protected $fillable = [
        'user_id',
        'category_id',
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

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function ais()
    {
        return $this->hasMany(AiAssistant::class);
    }
}

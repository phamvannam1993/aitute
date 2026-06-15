<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LikeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyAIImageTemplateCategory extends Model
{
    use HasFactory, LikeScope;
    protected $table = 'my_ai_image_template_categories';

    protected $fillable = [
        'my_ai_image_collection_id',
        'title',
        'slug',
        'month',
        'order',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set the order to the next highest value
            $model->order = $model->order ?? (self::max('order') + 1);
        });
    }

    public function templates()
    {
        return $this->hasMany(MyAIImageTemplate::class, 'my_ai_image_template_category_id');
    }
}

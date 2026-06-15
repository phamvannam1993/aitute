<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'dify_dataset_id',
        'user_id'
    ];

    protected $table = 'projects';

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDifyDatasetName()
    {
        return config('constant.APP_ENV') . '_' . config('app.name') .'_'. $this->user_id . '_' . $this->id;
    }

    public function chatTrainingDocuments()
    {
        return $this->hasMany(ChatTrainingDocument::class);
    }
}

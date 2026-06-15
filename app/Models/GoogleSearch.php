<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSearch extends Model
{

    protected $table = 'google_search';
    use HasFactory;

    protected $fillable = ['position', 'title', 'address', 'state', 'city', 'district', 'latitude', 'longitude', 'rating', 'ratingCount', 'type', 'types', 'phoneNumber', 'openingHours', 'thumbnailUrl'];
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $appends = ['note'];

    public function getNoteAttribute() {
        return $this->userNote ? $this->userNote->note : '';
    }

    public function userNote()
    {
        return $this->hasOne(UserNoteGoogleSearch::class, 'google_search_id')->where('user_id', auth('web')->id());
    }
}

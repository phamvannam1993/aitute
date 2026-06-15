<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGoogleSearch extends Model
{
    protected $table = 'user_google_search';

    protected $fillable = [
        'key', 'city_id'
    ];
}

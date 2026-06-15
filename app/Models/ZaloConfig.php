<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZaloConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'app_id',
        'app_secret',
        'refresh_token',
        'access_token',
        'oa_secret_key_webhook',
        'admin_group_id',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'is_public',
        'user_id_by_app_for_test'
    ];

    /**
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id_by_app_for_test' => 'array',
        ];
    }
}

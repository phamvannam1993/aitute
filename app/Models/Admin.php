<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
    ];
    protected $appends = ['role_names','permission_names'];

    protected function permissionNames(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getAllPermissions()->pluck('name'),
        );
    }

    protected function roleNames(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roles->pluck('name'),
        );
    }

}

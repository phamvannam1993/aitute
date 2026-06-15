<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSaleContactStatus extends Model
{
    use HasFactory;
    protected $table = 'user_sale_contact_statuses';

    protected $fillable = [
        'name'
    ];

    public function user()
    {
        return $this->hasMany(UserSale::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'month',
        'credit',
        'price',
        'price_actual',
        'currency',
        'discount',
        'description',
        'recommend',
        'is_delete',
    ];
}

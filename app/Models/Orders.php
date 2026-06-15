<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;
    use HasFactory;
    const STATUS_PAID = 'PAID';
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'coupon_id',
        'coupon_discount_vnd',
        'total_price',
        'total_point',
        'request_id',
        'status',
        'order_at',
        'cancel_at',
        'finish_at'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHistory extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'order_histories';
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'request_id',
        'amount',
        'payment_method',
        'status',
        'order_at',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

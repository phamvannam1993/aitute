<?php

namespace App\Repositories;

use App\Constants\OrderStatus;
use App\Models\OrderHistory;
use App\Models\Orders;

class OrderHistoryRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return OrderHistory::class;
    }

    public function updateStatusPayment(Orders $order) {
        OrderHistory::where('order_id' , $order->id)->update([
            'status' => OrderStatus::PAID
        ]);
    }
}

<?php

namespace App\Repositories;

use App\Models\Orders;

class OrderRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Orders::class;
    }

    public function checkOrder($productIds, $adminId, $lastAccess)
    {
        $record = $this->model->where('admin_id', $adminId)
            ->join('order_items', function ($q) use ($productIds) {
                $q->on('order_items.order_id', '=', 'orders.id')
                    ->whereIn('order_items.product_id', $productIds)
                    ->whereNull('order_items.deleted_at');
            })
            ->selectRaw("
                orders.id
            ")
            ->where('orders.updated_at', '>=', $lastAccess)
            ->whereNull('orders.cancel_at')
            ->first();

        return !empty($record->id);
    }
}

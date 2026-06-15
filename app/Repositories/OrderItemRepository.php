<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return OrderItem::class;
    }
}

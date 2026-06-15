<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\VoiceType;

class ProductRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Product::class;
    }

    public function getTotalPrice($ids, $coupon, $quantity = 1)
    {
        $sumPrice = Product::where('id', $ids)->sum('price_actual') * $quantity;

        if ($coupon && isset($coupon->discount)) {
            $discountPrice = $sumPrice * ($coupon->discount / 100);

            if ($coupon->limit && $discountPrice >= $coupon->limit) {
                $discountPrice = $coupon->limit;
            }

            return $sumPrice - $discountPrice;
        }

        return $sumPrice;
    }

    public function getAll()
    {
        return Product::all()->toArray();
    }
}

<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ProductImage::class;
    }
}

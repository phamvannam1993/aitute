<?php

namespace App\Services;

use Exception;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;

class ProductImageService
{
    public function getOneBy($params = [])
    {
        return  ProductImage::where($params)->first();
    }

    public function save($data = []) {
        return  ProductImage::create($data);
    }

    public function delete($id) {
        return  ProductImage::where("id", $id)->delete();
    }

    public function getHistories()
    {
        try {
            $userId = auth('web')->id();
            $per_page = 20;
            $result = ProductImage::where('user_id', '=', $userId)
                ->select('id', 's3_url')
                ->orderBy('created_at', 'DESC')
                ->paginate($per_page);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }
}

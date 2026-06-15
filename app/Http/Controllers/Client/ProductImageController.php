<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductImageService;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helpers;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductImageController extends Controller
{
    private $productImageService;

    public function __construct(
        ProductImageService $productImageService,
    ) {
        $this->productImageService = $productImageService;
    }

    public function apiHistory() {
        try {
            $list = $this->productImageService->getHistories();
            return response()->json(['data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $request) {
        $user_id = Auth::user()->id;
        $image_file = $request->file('image_file');
        try {
            if ($image_file) {
                if (!file_exists(storage_path('app/public/images'))) {
                    mkdir(storage_path('app/public/images'), 0777, true);
                }
                $image_file = $image_file->store('images', 'public');
                Storage::disk('s3')->put($image_file, Storage::disk('public')->get($image_file));
                $productImage = Helpers::preSignedS3Url($image_file);
                $imagePath = storage_path('app/public/'.$image_file);
                $dataSave = [
                    'user_id' => $user_id,
                    's3_url' => $image_file
                ];
                $data = $this->productImageService->save($dataSave);
                unlink($imagePath);
                return response()->json(["s3_url" => $productImage, "success" => true, 'data' => $data]);
            }
            return response()->json(["s3_url" => "", "success" => false]);
        } catch(\Exception $ex) {
            return response()->json(["s3_url" => "", "success" => false]);
        }
    }

    public function history()
    {
        try {
            $list = $this->productImageService->getHistories();
            return Inertia::render('Client/ProductImage/History', ['list' => $list]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteImage($id)
    {
        try {
            $this->productImageService->delete($id);
            return response()->json(["message" => "Xóa thành công", "success" => true]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Xóa thất bại", "success" => false]);
        }
    }
}

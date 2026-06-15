<?php

namespace App\Http\Controllers\Client;

use App\Constant\SlideType;
use App\Http\Controllers\Controller;
use App\Services\LessionService;
use App\Services\PhotoService;
use App\Services\ProductService;
use App\Services\SemesterMaterialService;
use App\Services\StorageService;
use App\Services\SubjectService;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImageController extends Controller
{
    public function __construct(
        protected StorageService $storageService
    ) {
    }


    public function uploadImage(Request $request)
    {
        $file = $request->file('upload');
        if ($file) {
            $result = $this->storageService->putToS3($file, folderName: 'social-image');
            return response()->json($result);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

}

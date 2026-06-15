<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackgroundImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\PebblelyVideoService;
use App\Helper\Helpers;

class BackgroundImageController extends Controller
{
    private $pebblelyService;

    public function __construct( PebblelyVideoService $pebblelyService)
    {
        $this->pebblelyService = $pebblelyService;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sample_file' => 'required|file|image|max:5120',
            'category' => 'required|string',
        ]);

        $sampleFile = $request->file('sample_file');

        $sampleFilePath = 'background-images/' . uniqid() . '.' . $sampleFile->getClientOriginalExtension();
        Storage::disk('s3')->put($sampleFilePath, file_get_contents($sampleFile));

        $validated['sample_url'] = $sampleFilePath;

        $backgroundImage = BackgroundImage::create($validated);

        return response()->json($backgroundImage, 201);
    }

    public function index()
    {
        $backgroundImages = BackgroundImage::all();
        return response()->json($backgroundImages);
    }

    public function component()
    {
        $backgroundImages = BackgroundImage::all();
        $history = $this->pebblelyService->getHistories();
        return response()->json(['backgroundImages' => $backgroundImages, 'history' => $history]);
    }
}

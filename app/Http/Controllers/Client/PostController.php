<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PostController extends Controller
{
    public function show(Request $request)
    {
        $url = $request->query('s3_url');
    
        if (!$url) {
            return response()->json(['error' => 'URL is required'], 400);
        }
    
        try {
            $response = Http::get($url);
    
            if (!$response->ok()) {
                return response()->json(['error' => 'Failed to fetch the file'], $response->status());
            }
    
            $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
            return response($response->body(), 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}

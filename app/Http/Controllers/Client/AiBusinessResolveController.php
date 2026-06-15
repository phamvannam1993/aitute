<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AiBusinessResolve;
use Illuminate\Http\Request;

class AiBusinessResolveController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Parse metadata từ request nếu là string
            $metadata = is_string($request->metadata) ? json_decode($request->metadata, true) : $request->metadata;
            $data = [
                'user_id' => auth()->id(),
                'project_id' => $request->project_id,
                'type' => $request->type ?? 'image',
                'path' =>  $request->path ?? '',
                's3_url' =>  $request->s3_url ?? '',
                'prompt' => $request->prompt,
                'metadata' => [
                    'width' => $metadata['width'] ?? $request->width ?? null,
                    'height' => $metadata['height'] ?? $request->height ?? null,
                    'created_at' => $metadata['created_at'] ?? now()->timestamp,
                ]
            ];

            $resolve = AiBusinessResolve::create($data);

            return response()->json([
                'success' => true,
                'data' => $resolve
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lưu kết quả: ' . $e->getMessage()
            ], 500);
        }
    }
}

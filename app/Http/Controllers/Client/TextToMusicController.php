<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AIMusicService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class TextToMusicController extends Controller
{
    public function __construct(
        protected AIMusicService $aiMusicService,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Client/TextToMusic/Index');
    }
    public function history(Request $request): Response
    {
        return Inertia::render('Client/TextToMusic/History');
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $response = $this->aiMusicService->deleteById($id);
        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Http\Controllers\Controller;
use App\Services\ChatGPTService;
use App\Services\CreatomateTemplateService;
use App\Services\CreatomateVideoService;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Creatomate\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class UrlToVideoController extends Controller
{
    public function __construct(
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Client/UrlToVideo/Index');
    }
}

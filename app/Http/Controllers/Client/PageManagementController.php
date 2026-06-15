<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialPost\ListSocialPostResource;
use App\Services\FacebookFanpageService;
use App\Services\FacebookService;
use App\Services\SocialPostService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageManagementController extends Controller
{
    public function __construct(
        private FacebookService $facebookService,
        private SocialPostService $socialPostService,
        private FacebookFanpageService $facebookFanpageService
    ) {}

    public function index(Request $request)
    {
        $facebooks = $this->facebookService->getUserFacebooks();
        $fanpages = $this->facebookFanpageService->getFanpages($facebooks->first());
        $socialPosts = $this->socialPostService->getSocialPosts();

        return Inertia::render('Client/PageManagement/Index', [
            'socialPosts' => ListSocialPostResource::collection($socialPosts),
            'fanpages' => $fanpages,
        ]);
    }
}

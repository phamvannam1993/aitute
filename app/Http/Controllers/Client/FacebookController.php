<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Facebook\StoreFacebookRequest;
use App\Models\Facebook;
use App\Services\FacebookService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class FacebookController extends Controller
{
    use ApiResponses;

    public function __construct(
        private FacebookService $facebookService

    ) {}

    public function create(): Response
    {
        $user = auth('web')->user();
        $facebook = $user->facebooks->first();
        $facebook = $facebook ?: new Facebook;
        return Inertia::render('Client/Facebook/Create', [
            'facebook' => $facebook
        ]);
    }

    public function document(): Response
    {
        return Inertia::render('Client/Facebook/Document');
    }
    

    /**
     * Handle the incoming request.
     *
     * @param StoreFacebookRequest $request
     * @return JsonResponse
     */
    public function store(StoreFacebookRequest $request): JsonResponse
    {
        try {
            $facebook = $this->facebookService->store($request->validated());
            return $this->okResponse(['facebook' => $facebook]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', HttpResponse::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}

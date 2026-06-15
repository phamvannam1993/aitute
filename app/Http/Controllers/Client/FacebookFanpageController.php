<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FacebookFanpage\StoreFacebookFanpageRequest;
use App\Services\FacebookFanpageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FacebookFanpageController extends Controller
{
    public function __construct(
        private FacebookFanpageService $facebookFanpageService
    ) {}

    /**
     * Handle the incoming request.
     *
     * @param StoreFacebookFanpageRequest $request
     * @return RedirectResponse
     */
    public function store(StoreFacebookFanpageRequest $request): RedirectResponse
    {
        try {
            $result = $this->facebookFanpageService->store($request->validated());
            return to_route('calendar');
        } catch (\Throwable $e) {
            report($e);

            if ($e instanceof DomainException) {
                return back()->withErrors(['message' => $e->getMessage()]);
            }

            return back()->withErrors(['message' => 'Error posting to Facebook']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFanpagesFacebookApi(Request $request): JsonResponse
    {
        try {
            $result = $this->facebookFanpageService->getFanpagesFacebookApi($request->all());
            return response()->json($result);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    public function deletePage(Request $request) { 
        if(!$request->pageId) {
            return response()->json(["success" => false, "message" => "page id không được để trống"]);
        }
        return $this->facebookFanpageService->deletePage($request->pageId);
    }
}

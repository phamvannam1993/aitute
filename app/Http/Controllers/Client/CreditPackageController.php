<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Services\CreditPackageService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CreditPackageController extends Controller
{
    use ApiResponses;
    public function __construct(
        protected CreditPackageService $creditPackageService
    ) {}


    /**
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $creditPackages = $this->creditPackageService->index();
            return $this->okResponse($creditPackages);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}

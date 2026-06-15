<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\DomainException;
use App\Exceptions\ZaloException;
use App\Http\Controllers\Controller;
use App\Services\ZaloChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZaloChatBotController extends Controller
{
    public function __construct(
        protected ZaloChatBotService $zaloChatBotService,
    ) {}

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws ZaloException
     */
    public function zaloChatBot(Request $request): JsonResponse
    {
        try {
            $result = $this->zaloChatBotService->__invoke($request->all(), $request);
            return response()->json(['result' => $result]);
        } catch (DomainException $e) {
            throw new ZaloException($e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new ZaloException($e->getCode(), $e);
        }
    }
}

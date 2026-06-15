<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AlepayWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Alepay webhook', $request->all());
        if ($this->verifyChecksumCode($request)) {
            return $next($request);
        }

        Log::error('Invalid checksum code');

        return response()->json([
            'message' => 'Invalid checksum code',
        ], 400);
    }

    private function verifyChecksumCode(Request $request): bool
    {
        $requestChecksum = $request->get('checksum');
        $checksumKey = config('common.payment.alepay.checksum');
        $transactionInfo = $request->collect('transactionInfo');
        $orderCode = $transactionInfo->get('orderCode');
        $amount = $transactionInfo->get('amount');
        $transactionCode = $transactionInfo->get('transactionCode');

        $checksum = md5($orderCode.$amount.$transactionCode.$checksumKey);

        return $checksum === $requestChecksum;
    }
}

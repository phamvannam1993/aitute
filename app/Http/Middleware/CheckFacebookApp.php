<?php

namespace App\Http\Middleware;

use App\Exceptions\FacebookApiServiceException;
use App\Helper\ApiErrorsMessageBag;
use App\Services\FacebookApiService;
use App\Traits\ApiResponses;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFacebookApp
{
    use  ApiResponses;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $facebookApiService =  app(FacebookApiService::class);
        } catch (FacebookApiServiceException $e) {
            if ($request->expectsJson()) {
                $apiErrors = new ApiErrorsMessageBag([
                    'message' => 'Bạn cần cập nhật thông tin Facebook App trước khi sử dụng chức năng này',
                ]);
                throw new HttpResponseException($this->notFoundResponse($apiErrors));
            }

            if ($e->getMessage() == 'Không tìm thấy thông tin facebook') {
                return redirect()->route('social.facebook.create')->withErrors(['message' => 'Bạn cần cập nhật thông tin Facebook App trước khi sử dụng chức năng Quản Lý Social']);
            }
        }

        return $next($request);
    }
}

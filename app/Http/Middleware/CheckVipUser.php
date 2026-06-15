<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVipUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user_experied_at = $user->vip_expired_at;
            if ($user->is_vip || !empty($user_experied_at)) {
                return $next($request);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Forbidden.',
                ], \Illuminate\Http\Response::HTTP_PRECONDITION_FAILED);
            }

            $request->session()->put('messageError', 'Tài khoản của bạn chưa được nâng cấp để sử dụng chức năng này. Để được sử dụng mọi tính năng, vui lòng nâng cấp gói tháng.');
            return redirect()->route('home.index');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return redirect()->route('client.auth.login-form');
    }
}

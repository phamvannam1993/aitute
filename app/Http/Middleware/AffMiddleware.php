<?php

namespace App\Http\Middleware;

use App\Models\AffKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AffMiddleware
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

            $aff = AffKey::join('config_aff', 'config_aff.id', '=', 'aff_keys.config_aff_id')
                ->select('code')
                ->where('user_id', $user->id)
                ->orderBy('aff_keys.created_at', 'DESC')
                ->first();

            if (empty($aff->code) || $aff->code == 'aff_trial') {
                return redirect()->route('home.index');
            } else {
                return $next($request);
            }
        } else {
            $routeName = Route::currentRouteName();

            $list = ['pricing.index'];
            if (in_array($routeName, $list)) {
                return redirect()->route('login');
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return redirect()->route('home.index');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if (auth('web')->user()) {
            $userData = auth('web')->user()->only(['id', 'name', 'email', 'credit', 'clone_voice', 'vip_expired_at']);
            $package = auth('web')->user()->package;
        }
        return [
            ...parent::share($request),
            'auth' => [
                'user' => auth('web')->user()
                    ? $userData + [
                        'package_name' => empty($package->package_name) ? '' : $package->package_name,
                        'package_code' => empty($package->package_code) ? '' : $package->package_code,
                        'is_vip_expired' => strtotime($userData['vip_expired_at']) > time() ? 0 : 1,
                    ] : null,
                'admin' => auth('admin')->user()?->only(['id', 'email', 'permission_names', 'role_names']),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'data' => $request->session()->get('data'),
            ],
        ];
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AffKey;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    
    public function __construct(
        protected AuthService $authService,
    ) {
       
    }

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Kiểm tra guard admin
        if (Auth::guard('admin')->check()) {
            return response()->json([
                'status' => 'admin',
                'redirect' => route('admin.index', absolute: false),
            ]);
        }
       
        // Kiểm tra user (guard mặc định 'web')
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
        
            $existsInAffKeys = AffKey::where('user_id', $userId)->exists();
            
            if (!$existsInAffKeys) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();
                
                return response()->json([
                    'status' => 'guest',
                    'redirect' => route('home.index', absolute: false),
                ]);
            }

            $this->authenticated();
            return response()->json([
                'status' => 'user',
                'redirect' => route('home.index', absolute: false),
            ]);
        }
      
        return response()->json([
            'status' => 'guest',
            'redirect' => route('home.index', absolute: false),
        ]);
    }

    protected function authenticated()
    {
        Auth::logoutOtherDevices(request('password'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }


    public function redirectToSocial(string $social_type)
    {
        if($social_type == "google") {
            session()->put('login_google_type', $social_type);
        }
        return Socialite::driver($social_type)->stateless()->redirect();
    }

    public function handleSocialCallback(string $social_type)
    {
        $user = $this->authService->getUserBySocial($social_type);
        if ($user) {
            if (auth()->guard('admin')->check()) {
                $url = session()->get('url.intended', redirect('/admin'));
            } elseif (auth()->guard('web')->check()) {
                $url = session()->get('url.intended', redirect('/'));
            } else {
                $url = route('login');
            }

            if (!Str::contains($url, 'reset-password')) {
                return inertia()->location($url);
            }

            return inertia()->location(route('admin.home'));
        }

        // Xử lý khi không đăng nhập được
        return back()->withErrors([
            'form' => 'The provided credentials do not match our records.',
        ]);
    }

    public function checkAuthStatus(Request $request): JsonResponse
    {
        if (Auth::guard('admin')->check()) {
            return response()->json(['authenticated' => true, 'role' => 'admin']);
        }

        if (Auth::guard('web')->check()) {
            return response()->json(['authenticated' => true, 'role' => 'user']);
        }

        return response()->json(['authenticated' => false]);
    }


}

<?php

namespace App\Http\Middleware;

use App\Models\AffKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    public function handle($request, Closure $next, ...$featureCodes)
    {
        $user = auth('web')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy gói có quyền cao nhất của user
        $highestConfigAffId = AffKey::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->value('config_aff_id');

        if (!$highestConfigAffId) {
            return redirect()->route('error.permission')->with('error', 'Bạn không có quyền truy cập chức năng này!');
        }

        // Kiểm tra quyền của gói cao nhất với featureCodes
        $hasAccess = AffKey::join('package_features', 'aff_keys.config_aff_id', '=', 'package_features.config_aff_id')
            ->where('aff_keys.user_id', $user->id)
            ->where('aff_keys.config_aff_id', $highestConfigAffId) // Chỉ kiểm tra quyền của gói cao nhất
            ->whereIn('package_features.feature_code', $featureCodes)
            ->exists();

        if (!$hasAccess) {
            // Kiểm tra nếu là request từ axios (XMLHttpRequest)
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Bạn không có quyền truy cập chức năng này!'], 405);
            }

            // Nếu là request thông thường, redirect về trang báo lỗi
            return redirect()->route('error.permission')->with('error', 'Bạn không có quyền truy cập chức năng này!');
        }

        return $next($request);
    }
}

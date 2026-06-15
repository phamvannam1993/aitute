<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Constants\Sender;
use App\Http\Controllers\Controller;
use App\Models\ActivationLog;
use App\Models\AffKey;
use App\Models\AiAssistant;
use App\Models\FailedAttempt;
use App\Models\User;
use App\Services\AgentService;
use App\Services\ChatClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\AIVideoService;
use App\Jobs\GenerateVideoAudio;
use App\Services\AIChatService;
use App\Services\AIVirtualService;
use App\Services\ChatGPTService;
use App\Services\DocumentReaderService;
use App\Services\StorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Jenssegers\Agent\Agent;

class UserSettingController extends Controller
{

    public function __construct()
    {

    }
    public function index(Request $request)
    {
        $user = Auth::user();
        return Inertia::render('Client/UserSetting/Index', [
            'firstLogin' => $user->first_login ?? 1,
        ]);
    }

    public function checkUserPackage(Request $request)
    {
        $user = Auth::user();
        $package = $user->package;
        $packageId = $request->query('id');

        if (!$package || $package->package_id === intval($packageId)) {
            return response()->json(['status' => 'upgrade_required']);
        }
        return response()->json(['status' => 'ok']);
    }

    public function checkPackagePermission(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json(['status' => 'request_login'], 403);
        }
        $package = $user->package;
        $packageId = $request->query('id');

        $listPermission = [1, 2, 3, 4];
        if ($package && in_array($package->package_id, $listPermission)) {
            return response()->json(['status' => 'ok']);
        }
        if (!$package || $package->package_id < intval($packageId)) {
            return response()->json(['status' => 'upgrade_required']);
        }
        return response()->json(['status' => 'ok']);
    }

    public function updateAccount(Request $request)
    {
        $request->validate([
            'first_login' => 'required|in:0,1',
        ]);

        $user = Auth::user();

        $user->first_login = $request->input('first_login');
        $user->save();

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn!'])
            : response()->json(['message' => 'Không thể gửi liên kết đặt lại mật khẩu. Vui lòng thử lại sau 60 giây!'], 500);
    }

    public function showResetForm(Request $request)
    {
        return Inertia::render('Client/UserSetting/ResetForm/Index', []);
    }

    public function reset(Request $request)
    {
        $request->validate(
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ],
            [
                'token.required' => 'Mã xác nhận là bắt buộc.',
                'email.required' => 'Email là bắt buộc.',
                'email.email' => 'Email không hợp lệ.',
                'password.required' => 'Mật khẩu là bắt buộc.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mật khẩu đã được đặt lại!'], 200)
            : response()->json(['message' => 'Đặt lại mật khẩu thất bại!'], 400);
    }

    public function showPermissionForm(Request $request)
    {
        return Inertia::render('Client/UserSetting/PermissionForm/Index', []);
    }

    public function activateAccountIndex(Request $request)
    {
        return Inertia::render('Client/ActivateAccount/Index');
    }
    public function activateAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6|max:255',
            'activationKey' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'activationKey.required' => 'Key kích hoạt không được để trống.',
            'activationKey.string' => 'Key kích hoạt phải là một chuỗi ký tự hợp lệ.',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $activationKey = $request->input('activationKey');
        $ip = $request->ip();
        $deviceInfo = $request->header('User-Agent');
        $userAgent = $request->header('User-Agent'); // Lấy User-Agent
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        $deviceSummary = $agent->platform() . ' (' . $agent->device() . ') - ' . $agent->browser();

        // Kiểm tra trong bảng failed_attempts
        $failedAttempt = FailedAttempt::firstOrCreate(
            ['ip_address' => $ip],
            ['attempts' => 0, 'locked_until' => null]
        );
        // Kiểm tra thời gian khóa
        if ($failedAttempt->locked_until && now()->lessThan($failedAttempt->locked_until)) {
            return response()->json([
                'message' => 'Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau ' . now()->diffForHumans($failedAttempt->locked_until),
            ], 400);
        }

        // Tìm Key trong bảng aff_keys
        $keyRecord = AffKey::where('key', $activationKey)
            ->where('is_used', 0)
            ->whereNull('user_id')
            ->with('configAff')
            ->first();

        // Kiểm tra key có tồn tại hay không
        if (!$keyRecord) {
            // Tăng số lần nhập sai
            $failedAttempt->increment('attempts');

            if ($failedAttempt->attempts >= 3) {
                $failedAttempt->update([
                    'locked_until' => now()->addHour(), // Khóa 1 giờ
                ]);

                return response()->json([
                    'message' => 'Bạn đã nhập sai quá 3 lần. Key đã bị khóa trong vòng 1 giờ.',
                ], 400);
            }

            return response()->json([
                'message' => 'Keys đã được kích hoạt hoặc không hợp lệ . Xin vui lòng nhập key hợp lệ để kích hoạt tài khoản. ',
                'remaining_attempts' => 3 - $failedAttempt->attempts,
            ], 400);
        }


        // Kiểm tra xem key đã được sử dụng chưa
        if ($keyRecord->is_used) {
            return response()->json([
                'message' => 'Key đã được kích hoạt. Xin vui lòng nhập key hợp lệ để kích hoạt tài khoản.',
            ], 400);
        }

        // Reset số lần nhập sai nếu thành công
        $failedAttempt->update([
            'attempts' => 0,
            'locked_until' => null,
        ]);

        // Lấy thông tin credit từ config_aff
        $credit = $keyRecord->configAff->credit ?? 0; // Nếu không có credit thì mặc định là 0
        $vipMonths = $keyRecord->configAff->month ?? 0; // Thời gian VIP nếu có

        // Tạo tài khoản mới trong bảng users
        $user = User::create([
            'name'  => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'credit'   => $credit,
            'vip_expired_at' => $vipMonths > 0 ? now()->addMonths($vipMonths) : now()->addDays($keyRecord->configAff->day ?? 0), // Tính thời gian VIP hết hạn
        ]);

        // Cập nhật trạng thái key thành đã sử dụng
        $keyRecord->update(
            [
                'is_used' => 1,
                'user_id' => $user->id
            ]
        );

        // Lưu lịch sử kích hoạt vào bảng activation_logs
        ActivationLog::create([
            'email' => $email,
            'key' => $activationKey,
            'device_info' => $deviceInfo,
            'device_summary' => $deviceSummary,
            'activated_at' => now(),
            'ip'   => $ip ?? ''
        ]);

        // Sysn data for san aff
        $agentInfo = DB::table('agents')->where('id', $keyRecord->agent_id)->first();
        if (!$agentInfo) {
            Log::error('Không tìm thấy agent Id ' . $keyRecord->agent_id);
        }
        // bắn event update cho sàn aff
        $agentService = app(AgentService::class);
        $params = [
            'coupon_parent' => $agentInfo->coupon_parent ?? null,
            'phone' => $agentInfo->phone ?? null,
            'email' => $email,
            'password' => $password,
        ];
        $agentService->syncAffMaster($params);

        // Đăng nhập tự động
        auth()->login($user);

        return response()->json([
            'message' => 'Tài khoản đã được kích hoạt thành công.',
            'redirect' => route('home.index'),
        ]);
    }


}

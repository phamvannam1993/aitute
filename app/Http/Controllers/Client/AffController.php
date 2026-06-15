<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\PurchaseMail;
use App\Models\AffKey;
use App\Models\ConfigAff;
use App\Models\User;
use App\Services\AffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AffController extends Controller
{
    public function __construct(
        protected AffService $affService,
    ) {}

    public function purchaseAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'secretKey' => 'required|string',
            'price' => 'required|string',
            'product_name' => 'required|string',
            'month' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
        }
        Log::info('Validate dữ liệu thành công');

        $secretKey = $request->input('secretKey');
        if($secretKey != config('auth.secretKey')) {
            Log::info('secretKey không chính xác');
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid secretKey',
            ], 400);
        }
        Log::info('Xác thực secretKey thành công');

        $record = ConfigAff::where('name', $request->input('product_name'))
                            ->where('month', $request->input('month'))
                            ->select('id', 'code')->first();
        if (empty($record)) {
            Log::info('không tồn tại gói' . $request->input('product_name') . ' month:' . $request->input('month') );
        }

        $keyRequest = new Request([
            'totalKey' => 1,
            'id' => $record->id,
            'code' => $record->code,
            'secretKey' => $secretKey
        ]);

        Log::info('Tạo key và lịch sử để cấp credit cho user');
        $generatedKeysResponse = $this->generateKeys($keyRequest);

        if ($generatedKeysResponse->status() !== 200) {
            return response()->json(['error' => 'Failed to generate key'], 500);
        }

        $generatedKey = $generatedKeysResponse->getData()->data[0];

        $configAff = ConfigAff::where('id', $generatedKey->config_aff_id)->first();

        $user = User::where('email', $request->input('email'))->first();

        $phone = $request->input('phone');

        if (!$user) {
            Log::info('User mới đăng ký email: '.$request->input('email'));
            $user = User::factory()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($phone),
                'phone' => $phone,
                'credit' => $configAff->credit,
                'vip_expired_at' => now()->addMonths($configAff->month)->addDays($configAff->day),
            ]);
        } else {
            Log::info('User đã tồn tại email: '.$request->input('email'));
            $user->update([
                'name' => $request->input('name'),
                'credit' => $user->credit + $configAff->credit,
                'password' => Hash::make($phone),
                'phone' => $phone,
                'vip_expired_at' => now()->addMonths($configAff->month)->addDays($configAff->day),
            ]);
        }

        $affKey = AffKey::find($generatedKey->id);
        $affKey->user_id = $user->id;
        $affKey->is_used = true;
        $affKey->save();
        Log::info('Lưu lịch sử user '.$user->id.' đã được cấp credit');

        Log::info('Bắt đầu gửi mail đến user -> email: '.$user->email);
        $this->sendMail($user, $phone);
        Log::info('Kết thúc gửi mail đến user -> email: '.$user->email);

        return response()->json([
            'message' => 'Account purchased successfully',
            'user' => $user,
        ], 200);
    }

    public function generateKeys(Request $request) {
        $validator = Validator::make($request->all(), [
            'totalKey' => 'required|integer',
            'code' => 'required|string',
            'secretKey' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
        }

        $configKey = ConfigAff::where('code', $request->input('code'))->first();
        if($request->id) {
            $configKey = ConfigAff::where('id', $request->id)->first();
        }
        if (!$configKey) {
            return response()->json(['error' => 'Configuration key not found'], 404);
        }

        $totalKey = $request->input('totalKey');
        $generatedKeys = [];

        for ($i = 0; $i < $totalKey; $i++) {
            do {
                $randomKey = Str::random(12);
            } while (AffKey::where('key', $randomKey)->exists());

            $generatedKeys[] = AffKey::create([
                'config_aff_id' => $configKey->id,
                'key' => $randomKey,
                'is_used' => false,
                'user_id' => null
            ]);
        }

        return response()->json(['message' => 'Keys generated successfully', 'data' => $generatedKeys], 200);
    }

    private function sendMail($user, $phone) {
        if ($user?->email) {
            $mail = (new PurchaseMail($user?->id, $phone))->onQueue('email-purchase-queue');
            $bcc = explode(',', config('mail.custom.bcc_email_receive_roadmap'));
            $bcc[] = config('mail.custom.email_receive_roadmap');
            Mail::to($user?->email)->bcc($bcc)->queue($mail);
        }
    }
}

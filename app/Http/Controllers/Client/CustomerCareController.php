<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\CustomerCareService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerCareController extends Controller
{
    protected $customerCareService;

    public function __construct(CustomerCareService $customerCareService)
    {
        $this->customerCareService = $customerCareService;
    }

    public function index()
    {
        $user = Auth::user();
        $perPage = 8;

        // nếu không có user thì đưa vê trang login
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has Facebook configurations
        $hasFacebookConfig = \App\Models\FacebookConfigs::where('user_id', $user->id)->exists();

        if (!$hasFacebookConfig) {
            return Inertia::render('Client/CustomerCare/Index', [
                'hasHistory' => false,
                'hasFacebookConfig' => false,
                'message' => 'Một tài khoản chỉ nên chọn một fanpage. Nếu chọn nhiều fanpage, tất cả các fanpage đó sẽ cùng trả lời dựa trên một nội dung mà bạn đã tải lên.'
            ]);
        }
        return app(FacebookConfigController::class)->index();

        /**$result = $this->customerCareService->getMessageHistories($user->id, $perPage);

        if ($result === null) {
            return Inertia::render('Client/CustomerCare/Index', [
                'hasHistory' => false,
                'hasFacebookConfig' => true,
                'message' => 'Bạn chưa có tin nhắn nào.'
            ]);
        }

        return Inertia::render('Client/CustomerCare/Index', [
            'hasHistory' => true,
            'hasFacebookConfig' => true,
            'data' => $result
        ]);
        */
    }

    public function checkAllMessagesStatus(Request $request)
    {
        try {
            $user_page_ids = $request->input('user_page_ids', []);
            $statuses = $this->customerCareService->checkAllMessagesStatus($user_page_ids);

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking message status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái tin nhắn'
            ], 500);
        }
    }
}
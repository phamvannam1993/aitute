<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,

    ) {
    }
    public function index(Request $request)
    {
        $result = $this->productRepository->getAll();
        return Inertia::render('Client/Pricing/Index', ['list' => $result]);
    }

    public function paymmentFinish()
    {
        return Inertia::render('Client/Pricing/PaymentFinish');
    }

    public function alepay(Request $request, PricingService $pricingService)
    {
        $data = $request->all();
        $request->session()->put('request_path', $data['request_path']);
        $validator = Validator::make($data, [
            'products' => 'required',
        ], [
            'products.required' => "Vui Lòng chọn gói muốn mua",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validator->errors()->all(),
            ], 400);
        }
        // dữ liệu người dùng đang được lấy từ bảng user, không phải của users
        $user = auth('web')->user();
        Log::info("user id: " . $user->id . " bấm vào nút thanh toán - product id: " . $data['products'] . " - At: " . date("Y-m-d H:i:s"));
        Log::info("user id: " . $user->id . " gọi function transfer -> at: " . date("Y-m-d H:i:s"));
        
        $type_payment = $data['type'];
        Log::info("user id: " . $user->id . " thanh toán bằng phương thức " . $type_payment . " -> at: " . date("Y-m-d H:i:s"));

        $result = $pricingService->getTransferUrl($data);

        Log::info("user id: " . $user->id . " đã gọi xong function transfer -> at: " . date("Y-m-d H:i:s"));
        Log::info(json_encode($result));
        if ($result['status']) {
            // Trả về kết quả dưới dạng JSON response
            Log::info("user id: " . $user->id . " kết quả " . $result['data']['url'] . "-> at: " . date("Y-m-d H:i:s"));
            return response()->json(['url' => $result['data']['url']]);
        } else {
            $lists = $pricingService->getList();

            $params = [
                'list' => $lists,
            ];
            return Inertia::render('Client/Pricing/Index', $params);
        }
    }

    public function alepaySuccess(Request $request, PricingService $service)
    {
        $data = $request->all([
            'transactionCode', 'errorCode',
        ]);
        $user = auth('web')->user();
        $request_path = $request->session()->pull('request_path', null);
        if ($data['errorCode'] == '000') {
            Log::info("User id: " . $user->id . " Gọi success transfer -> at: " . date("Y-m-d H:i:s"));
            $redirectUrl = $service->successTransfer($data['transactionCode'], $request_path);
            Log::info("User id: " . $user->id . " đã call xong success transfer -> at: " . date("Y-m-d H:i:s"));
        } else {
            Log::info("Thanh toán thất bại error code: " . $data['errorCode'] . " -> at: " . date("Y-m-d H:i:s"));
            $app_url = config('common.app_url');
            $redirectUrl = $app_url . '/pricing';
        }

        return redirect($redirectUrl);
    }

    public function alepayCancel(Request $request)
    {
        Log::info("Huy thanh toan. " . date("Y-m-d H:i:s"));
        $app_url = config('common.app_url');
        $redirectUrl = $app_url . '/pricing';
        return redirect($redirectUrl);
    }
}

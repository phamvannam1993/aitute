<?php

namespace App\Services;

use App\Constants\OrderStatus;
use App\Constants\PaymentMethod;
use App\Helper\OrderHelper;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PricingService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected OrderHistoryRepository $orderHistoryRepository,
        protected ProductRepository $productRepository
    ) {
    }

    public function getList($lang = 'vi')
    {
        $products = $this->productRepository->get([]);

        return $products;
    }

    public function getTransferUrl($params)
    {
        $user = auth('web')->user();
        $user_email = $user->email;

        if (preg_match('/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/', $user->email)) {
            Log::info("Email " . $user->email . " hợp lệ");
        } else {
            $user_email = 'gotechjsc@gmail.com';
            Log::info("Email " . $user->email . " không hợp lệ");
        }

        Log::info("User info send to alepay -> id: " . ($user->id) . " At: " . date("Y-m-d H:i:s"));

        $endpoint = config('common.payment.alepay.url') . '/request-payment';
        $token = config('common.payment.alepay.token');
        $ids = $params['products'] ?? [];
        $couponId = $params['couponId'] ?? [];
        $language = $params['language'] ?? 'en';
        $coupon = null;
        if ($language != 'vi') {
            $language = 'en';
        }
        if (empty($ids)) {
            return [
                'status' => false,
                'message' => "Vui Lòng chọn gói muốn mua",
            ];
        }

        $params['quantity'] = (isset($params['quantity']) && $params['quantity'] !== null && $params['quantity'] !== 0) ? $params['quantity'] : 1;

        // calculate totla price
        $totalOrder = $this->productRepository->getTotalPrice($ids, $coupon, $params['quantity']);

        // lấy thông tin của gói nạp, dùng để tính tổng credit
        $product = $this->productRepository->findOrFail($params['products']);
        
        // create order
        $orderData = [
            'user_id' => auth('web')->id(),
            'total_price' => $totalOrder,
            'status' => OrderStatus::PENDING,
            'total_point' => $product->credit,
            'order_at' => Carbon::now()
        ];
        $order = $this->orderRepository->create($orderData);
        // create details order
        $product = $this->productRepository->find($ids);
        $orderItemData = [
            'order_id' => $order->id,
            'product_id' => $ids,
            'quantity' => $params['quantity'],
            'price_each' => $product->price_actual
        ];
        $orderItem = $this->orderItemRepository->create($orderItemData);
        $app_url =  env('APP_URL');

        $userName = preg_replace('/[^a-zA-Z]/', '', $user->name);

        if (empty($userName)) {
            $userName = 'Gotech User';
        }

        $queryData = [
            'tokenKey' => $token,
            'orderCode' => base64_encode($order->id), // order_id
            'customMerchantId' => base64_encode($user->id), // user_id
            'amount' => $order->total_price, // total
            'currency' => $product->currency,
            'orderDescription' => 'Thanh toán giao dịch AIWOW',
            'totalItem' => 1, // number of items
            'returnUrl' => $app_url . "pricing/alepay-success",
            'cancelUrl' => $app_url . "pricing",
            'buyerName' => $userName,
            'buyerEmail' => $user_email, //user_email
            'buyerPhone' => '0862261966', //user_phone
            'buyerAddress' => $user->address ?? 'đường Thanh Bình, tổ dân phố 11, Phường Mộ Lao, Quận Hà Đông', // user_address
            'buyerCity' => 'Hà Nội', //user_city
            'buyerCountry' => 'Việt Nam',
            'checkoutType' => $params['type'] == 'VISA' ? 1 : 4,
            'language' => $language,
            'allowDomestic' => $params['type'] == 'VISA' ? false : true,
            'paymentMethod' => $params['type'] == 'VISA' ? null : "VA",
        ];

        Log::info("Data info send to alepay -> " . json_encode($queryData) . " At: " . date("Y-m-d H:i:s"));

        $queryData = $this->buildQueryData($queryData);
        $result = $this->postAlepay($endpoint, $queryData);
        Log::info($result);
        if (!empty($result['code']) && $result['code'] == '000') {
            $orderHistoryData = [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'request_id' => $result['transactionCode'] ?? 'NA',
                'amount' => $order->total_price,
                'order_at' => Carbon::now(),
                'status' =>  OrderStatus::REQUEST,
                'payment_method' => PaymentMethod::ALEPAY
            ];
            $orderHistory = $this->orderHistoryRepository->create($orderHistoryData);
            $orderAttributes = [
                'id' => $order->id,
            ];
            $orderValues = [
                'status' => OrderStatus::REQUEST,
                'request_id' => $result['transactionCode'] ?? 'NA',
            ];
            $orders = $this->orderRepository->updateOrCreate($orderAttributes, $orderValues);
            return [
                'status' => true,
                'data' => [
                    'url' => $result['checkoutUrl'],
                ],
            ];
        } else {
            return [
                'status' => false,
                'message' => "Có lỗi khi thực hiện thanh toán",
            ];
        }
    }

    private function buildQueryData($data)
    {
        $checksum = config('common.payment.alepay.checksum');
        ksort($data);

        $string = '';
        foreach ($data as $key => $value) {
            if ($key == 'allowDomestic') {
                // Check if the value is true or false and append accordingly
                $string .= empty($string) ? $key . '=' . ($value ? 'true' : 'false') : '&' . $key . '=' . ($value ? 'true' : 'false');
            } else {
                // For other keys, include the usual logic
                $string .= empty($string) ? $key . '=' . $value : '&' . $key . '=' . $value;
            }
        }

        Log::info("Debug make signature alepay -> " . $string . " At: " . date("Y-m-d H:i:s"));

        $signature = hash_hmac('sha256', $string, $checksum);

        $data['signature'] = $signature;

        return $data;
    }

    /**
     * postAlepay
     * Gọi API thanh toán của alepay
     * @param  mixed $endpoint
     * @param  mixed $queryData
     * @return void
     */
    private function postAlepay($endpoint, $queryData)
    {
        $result = Http::withHeaders([
            "content-type" => "application/json",
            "accept" => '*/*',
            'charset' => 'utf-8',
        ])
            ->timeout(15)
            ->post($endpoint, $queryData);

        return json_decode($result, true);
    }

    /**
     * successTransfer
     * Xử lý thanh toán thành công
     * @param  mixed $code
     * @return void
     */
    public function successTransfer($code, $request_path = null)
    {
        /** @var User $user */
        $user = auth('admin')->user();
        $endpoint = config('common.payment.alepay.url') . '/get-transaction-info';
        $token = config('common.payment.alepay.token');

        $queryData = [
            'tokenKey' => $token,
            'transactionCode' => $code,
        ];

        $queryData = $this->buildQueryData($queryData);

        $result = $this->postAlepay($endpoint, $queryData);
        Log::info("Order id: " . base64_decode($result['orderCode']) . " -> at: " . date("Y-m-d H:i:s"));
        $app_url =  env('APP_URL');
        if (!empty($result['code']) && $result['code'] == '000') {
            $orderId = base64_decode($result['orderCode']);
            $orderHelper = new OrderHelper( $this->orderRepository, $this->orderItemRepository, $this->userRepository, $this->productRepository);
            $order = $orderHelper->markAsPaid($orderId);
            Log::info("Thanh toan thanh cong!" . $order);
            $userId = Orders::select('user_id')->where('request_id', $code)->first();
            $user = $this->userRepository->findOrFail($userId);
            $orderHistory = $this->orderHistoryRepository->updateStatusPayment($order);
            $orderItem = OrderItem::where('order_id', $orderId)->first();
            $redirectUrl = $app_url . 'pricing';
        } else {
            $redirectUrl = $app_url . 'pricing';
        }

        return $redirectUrl;
    }
}

<?php

namespace App\Helper;

use App\Constants\OrderStatus;
use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderHelper
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected UserRepository $userRepository,
        protected ProductRepository $productRepository,
    ) {
    }
    public function markAsPaid($orderId)
    {
        try {
            DB::beginTransaction();
            Log::info("Mark as paid order id: " . $orderId . " -> at: " . date("Y-m-d H:i:s"));
            $order = $this->orderRepository->findOrFail($orderId);
            $user = $this->userRepository->findOrFail($order->user_id);
            if ($order->status === OrderStatus::REQUEST) {
                $now = Carbon::now();
                Log::info("Truy vấn order item với order id: " . $orderId . " -> at: " . date("Y-m-d H:i:s"));
                $orderItem = OrderItem::where('order_id', $orderId)->first();
                
                $product = $this->productRepository->find($orderItem->product_id);
                $user->credit += $order->total_point;
                $user->save();

                $order->status = OrderStatus::PAID;
                $order->finish_at = $now;
                $order->save();

                if (is_numeric($product->month) && $product->month != 0) {
                    if (!empty($user->vip_expired_at)) {
                        $date = Carbon::parse($user->vip_expired_at);

                        // Kiểm tra nếu vip_expired_at lớn hơn hoặc bằng ngày hiện tại
                        if ($date->greaterThanOrEqualTo(now())) {
                            $user->vip_expired_at = $date->addMonths($product->month);
                        } else {
                            // Cập nhật ngày hết hạn là ngày hiện tại cộng thêm số tháng từ sản phẩm
                            $user->vip_expired_at = now()->addMonths($product->month);
                        }
                    } else {
                        $user->vip_expired_at = $now->addMonths($product->month);
                    }

                    $user->save();
                }

                if (is_numeric($product->day) && $product->day != 0) {
                    if (!empty($user->vip_expired_at)) {
                        $date = Carbon::parse($user->vip_expired_at);

                        // Kiểm tra nếu vip_expired_at lớn hơn hoặc bằng ngày hiện tại
                        if ($date->greaterThanOrEqualTo(now())) {
                            $user->vip_expired_at = $date->addMonths(2); // Hard code chờ specs
                        } else {
                            // Cập nhật ngày hết hạn là ngày hiện tại cộng thêm số tháng từ sản phẩm
                            $user->vip_expired_at = now()->addMonths(2); // Hard code chờ specs
                        }
                    } else {
                        $user->vip_expired_at = $now->addMonths(2); // Hard code chờ specs
                    }

                    $user->save();
                }
            }
            DB::commit();
            return $order;
        } catch (\Throwable $e) {
            logger($e);
            DB::rollBack();
            abort(500);
        }
    }

    public function markAsFailed($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);
        $order->status = OrderStatus::FAILED;
        $order->save();
    }
}

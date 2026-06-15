<?php

namespace App\Http\Controllers\Client;

use App\Enums\AlepayStatus;
use App\Helper\OrderHelper;
use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\SlideProduct;
use App\Models\Roadmap;
use App\Models\User;
use App\Models\UserRoadmap;
use App\Notifications\WelcomeBand468Notification;
use App\Repositories\OrderHistoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JsonException;

class AlepayWebhookController extends Controller
{
    public function __construct(
        private readonly OrderHelper $orderHelper,
        private readonly OrderHistoryRepository $orderHistoryRepository,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        Log::info("Alepay webhook - Invoke");
        $transactionInfo = $request->collect('transactionInfo');
        if ($transactionInfo->get('status') === AlepayStatus::SUCCESS->value) {
            $this->handleCheckoutSuccess($transactionInfo->get('transactionCode'));
        }

        return response()->json([
            'message' => 'success',
        ]);
    }

    private function handleCheckoutSuccess(string $transactionCode): void
    {
        try {
            DB::beginTransaction();
            Log::info("Alepay webhook - transaction info: ". $transactionCode);
            $orderHistory = OrderHistory::where('request_id', $transactionCode)->firstOrFail();
            Log::info("Alepay webhook - call mark as paid function: ". $orderHistory->order_id);
            $order = $this->orderHelper->markAsPaid($orderHistory->order_id);
            $this->orderHistoryRepository->updateStatusPayment($order);
            $user = $orderHistory->user;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency($e->getMessage());
        }
    }
}

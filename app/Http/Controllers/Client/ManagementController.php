<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AiTuTeMessage;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserSale;
use App\Services\ManagementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ManagementController extends Controller
{
    private $managementService;
    public function __construct(ManagementService $managementService)
    {
        $this->managementService = $managementService;
    }

    public function index(Request $request)
    {
        $query = $request->query();
        $data = $this->managementService->getUsersSale($request->collect(), $query);
        $user = auth()->user();
        list($data, $listUserSaleContactStatus) = $this->managementService->getUsersSale($request->collect(), $query);

        $params = [
            'user' => $user->toArray(),
            'data' => $data,
            'listUserSaleContactStatus' => $listUserSaleContactStatus,
            'query' => $query,
        ];
        return Inertia::render('Client/Management/Customer/Index', $params);
    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $data = $this->managementService->getUsersSaleDetail($id);
        return Inertia::render('Client/Management/Customer/Detail', [
            'data' => $data,
        ]);
    }

    public function updateUserBank(Request $request)
    {
        try {
            $bank_name = $request->input('bank_name');
            $account_name = $request->input('account_name');
            $account_number = $request->input('account_number');
            $address = $request->input('address');
            $phone = $request->input('phone');

            $user = auth()->user();

            // check uniqueness, excluding current user
            $exists = User::where('phone', $phone)
            ->where('id', '!=', $user->id)
            ->exists();
            if ($exists) {
                return response()->json(['fail' => false, 'message' => 'Số điện thoại đã tồn tại'], 422);
            }

            $dataUpdate = [
                'bank_name' => $bank_name,
                'account_name' => $account_name,
                'account_number' => $account_number,
                'is_default' => true
            ];

            $file_image = $request->file('qr_code');

            if(isset($file_image)){
                $imageContent = file_get_contents($file_image);
                if ($imageContent === false) {
                    Log::error('Không thể tải hình ảnh từ URL.');
                }

                $filename = 'qr_code/' . uniqid('image_', true) . '.png';
                Storage::disk('s3')->put($filename, $imageContent);
                $dataUpdate['qr_code_url'] = $filename;
            }

            $user->update(['phone' => $phone]);
            $user->update(['address' => $address]);
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $dataUpdate]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'user_sale_contact_status_id' => 'nullable',
                'contact_note' => 'nullable|string',
            ]);

            $id = $request->id;
            $updateData = [
                'user_sale_contact_status_id' => $request->user_sale_contact_status_id,
                'contact_note' => $request->contact_note,
            ];

            UserSale::where('id', $id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật có lỗi.'
            ]);
        }
    }
    public function delete(Request $request)
    {
        try {
            $id = $request->id;

            // set isDelete = true
            $user = UserSale::findOrFail($id);

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật có lỗi.'
            ]);
        }
    }

    public function messageHistories($conversation_id)
    {
        try {
            $per_page = 5;
            $page = request()->get('page', 1);

            // Lấy 2 tin nhắn theo trang
            $messages = AiTuTeMessage::where('conversation_id', '=', $conversation_id)
                ->select('id', 'sender', 'content', 'path_image', 'is_hidden')
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $per_page)
                ->take($per_page)
                ->get()
                // ->sortBy(function($message) {
                //     return $message->sender === 'ai' ? 1 : 0;
                // })
                ->values();

            $histories = new LengthAwarePaginator(
                $messages,
                AiTuTeMessage::where('conversation_id', '=', $conversation_id)->count(),
                $per_page,
                $page,
                ['path' => request()->url()]
            );
            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi lấy lịch sử tin nhắn: ' . $e->getMessage());
            return null;
        }
    }

    public function updateUserSale(Request $request)
    {
        try {
            $user_sale = $request->get('user_sale') ?? null;
            $updateData = [
                'name' => $user_sale['name'],
                'email' => $user_sale['email'],
                'phone' => $user_sale['phone'],
                'age' => $user_sale['age'],
                'treatment' => $user_sale['treatment'],
            ];
            if (!empty($user_sale['appointment'])) {
                $updateData['appointment'] = (new Carbon($user_sale['appointment']))->timezone('Asia/Ho_Chi_Minh');
            }

            UserSale::where('id', $user_sale['id'])->update($updateData);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật user thành công',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            Helpers::logException($e);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi trong quá trình xử lý',
                'data' => []
            ], 500);
        }
    }
}

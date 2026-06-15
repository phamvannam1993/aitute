<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddCreditRequest;
use App\Http\Requests\User\ExportUserRequest;
use App\Http\Requests\User\StoreUserInAdminPageRequest;
use App\Http\Requests\User\UpdateUserInAdminPageRequest;
use App\Models\User;
use App\Services\ConfigAffService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
class AdminUserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private ConfigAffService $configAffService
    ) {}

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $params = $request->all();
        if ($admin->cannot('users.index')) {
        return to_route('admin.errors.403');
        }
        $packages = $this->userService->getPackages();

        $paginateUsers = $this->userService->paginateUsers($params);

        if (isset($params['is_gift']) && $params['is_gift'] == 1) {
            $totalKeyRevenue = 0;
        } else {
            // Tính tổng tiền từ việc bán key, loại trừ tài khoản tặng
            $params['is_gift'] = 0;
            $totalKeyRevenue = $this->userService->calculateTotalRevenue($params);
        }
        // Lấy tổng tiền nạp credit từ OrderHistoryService
        $totalCreditAmount = $this->userService->getTotalSuccessAmount($request->all());

        // Tổng cộng
        $grandTotal = $totalKeyRevenue + $totalCreditAmount;

        // foreach ($paginateUsers as $user) {
        //     $url = route('ai-bacsi.index', ["utm_source" => data_get($user, 'id')]);
        //     $user->docter_ai_url = $url;
        //     $user->download_qr_url = route('admin.ai-bacsi.download-qr', ["utm_source" => data_get($user, 'id')]);
        //     // QR code image
        //     $qrCode = QrCode::create($url)->setSize(100)->setMargin(2);
        //     $writer = new PngWriter();
        //     $result = $writer->write($qrCode);
        //     $user->docter_ai_qr = base64_encode($result->getString());
        // }

        $params = [
            'paginateUsers' => $paginateUsers,
            'totalKeyRevenue' => $totalKeyRevenue,
            'totalCreditAmount' => $totalCreditAmount,
            'grandTotal' => $grandTotal,
            'packagesSup' => $packages,
            'filters' => [
                'search' => $request->search ?? '',
                'package_id' => $request->package_id ?? '',
                'from_date' => $request->from_date ?? '',
                'to_date' => $request->to_date ?? '',
            ]
        ];

        return Inertia::render('Admin/User/Index', $params);
    }

    public function create()
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.create')) {
            return to_route('admin.errors.403');
        }

        $user = new User;
        $configAffs = $this->configAffService->all();
        $params = [
            'user' => $user,
            'configAffs' => $configAffs,
        ];
        return Inertia::render('Admin/User/Create', $params);
    }

    public function store(StoreUserInAdminPageRequest $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.create')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->userService->storeUserInAdmin($request->validated());
            return to_route('admin.users.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi tạo người dùng: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateUserInAdminPageRequest $request, $id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.edit')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->userService->updateUserInAdmin($request->validated(), $id);
            return to_route('admin.users.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi tạo người dùng: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.edit')) {
            return to_route('admin.errors.403');
        }

        $user = $this->userService->getUserById($id);
        $configAffs = $this->configAffService->all();

        $params = [
            'user' => $user,
            'configAffs' => $configAffs,
        ];
        return Inertia::render('Admin/User/Edit', $params);
    }

    public function destroy($id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.destroy')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->userService->destroy($id);
            return to_route('admin.users.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi xóa người dùng: ' . $e->getMessage(),
            ]);
        }
    }

    public function addCredit(AddCreditRequest $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.add-credit')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->userService->addCredit($request->validated());
            return to_route('admin.users.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi thêm credit: ' . $e->getMessage(),
            ]);
        }
    }

    public function export(ExportUserRequest $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('users.export')) {
            return to_route('admin.errors.403');
        }

        try {
            return  $this->userService->export($request->validated());
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi export users: ' . $e->getMessage(),
            ]);
        }
    }
}

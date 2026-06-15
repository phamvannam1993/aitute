<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('activity-logs.index')) {
            return to_route('admin.errors.403');
        }

        $paginateActivityLogs = $this->activityLogService->paginateActivityLogs($request->all());

        $params = [
            'paginateActivityLogs' => $paginateActivityLogs,
        ];

        return Inertia::render('Admin/ActivityLog/Index', $params);
    }
}

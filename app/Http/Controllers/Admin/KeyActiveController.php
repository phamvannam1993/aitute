<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AiAssistant;
use App\Models\Occupation;
use App\Models\Operation;
use App\Models\User;
use App\Services\AffService;
use App\Services\AiAssistantService;
use App\Services\AgentService;
use App\Services\ConfigAffService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class KeyActiveController extends Controller
{
    protected  $affService;
    protected  $configAffService;
    protected $agentService;

    public function __construct(AffService $affService, ConfigAffService $configAffService,  AgentService $agentService)
    {
        $this->affService = $affService;
        $this->configAffService = $configAffService;
        $this->agentService = $agentService;
    }

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.index')) {
            return to_route('admin.errors.403');
        }

        $paginateKeys = $this->affService->paginateKeys($request->all());
        $configAffs = $this->configAffService->all();
        $agents = Agent::all();
        $params = [
            'paginateKeys' => $paginateKeys,
            'configAffs' => $configAffs,
            'agents' => $agents,
        ];
        return Inertia::render('Admin/Key/Index', $params);
    }

    public function create()
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.create')) {
            return to_route('admin.errors.403');
        }

        // Sync agents từ API
        $this->agentService->syncAgents();

        $user = new User;
        $configAffs = $this->configAffService->all();
        $agents = Agent::all();
        $params = [
            'user' => $user,
            'configAffs' => $configAffs,
            'agents' => $agents,
        ];
        return Inertia::render('Admin/Key/Create', $params);
    }

    public function store(Request $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.create')) {
            return to_route('admin.errors.403');
        }

        $data = $request->validate([
            'configAff' => 'required|integer',
            'keyQuantity' => 'required|integer|min:1',
            'agent' => 'nullable|integer',
        ]);

        // Tạo keys
        for ($i = 0; $i < $data['keyQuantity']; $i++) {
            \App\Models\AffKey::create([
                'user_id' => null,
                'key' => Str::random(12),
                'config_aff_id' => $data['configAff'],
                'agent_id' => $data['agent'] ?? null,
            ]);
        }

        return redirect()->route('admin.keys.index')->with('success', 'Người dùng và keys đã được tạo thành công!');
    }

    public function destroy($key_id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.destroy')) {
            return to_route('admin.errors.403');
        }
        try {
            $this->affService->destroy($key_id);
            return to_route('admin.keys.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi xóa keys: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit($key_id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.edit')) {
            return to_route('admin.errors.403');
        }
        $key = $this->affService->getKeyById($key_id);
        $configAffs = $this->configAffService->all();

        $params = [
            'keyCreate' => $key,
            'configAffs' => $configAffs,
        ];
        return Inertia::render('Admin/Key/Edit', $params);
    }

    public function update(Request $request, $key_id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('keys.edit')) {
            return to_route('admin.errors.403');
        }
        $data = $request->validate([
            'configAff' => 'required|integer',
        ]);

        $keyrs = \App\Models\AffKey::findOrFail($key_id);
        $keyrs->update([
            'config_aff_id' => $data['configAff'],
        ]);

        return redirect()->route('admin.keys.index')->with('success', 'Key đã được cập nhật!');

    }

    public function exportCsv(Request $request)
    {
        $query = $this->affService->getFilteredKeys($request->all());

        $filename = "keys_" . now()->format('YmdHis') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $columns = ['STT', 'Key', 'Email', 'Đại lý', 'Gói', 'Kích hoạt', 'Ngày tạo'];

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $index = 1;
            foreach ($query as $row) {
                fputcsv($file, [
                    $index++,
                    $row->key,
                    $row->email,
                    $row->agent_name,
                    $row->config_aff_name,
                    $row->is_used ? 'Đã kích hoạt' : 'Chưa kích hoạt',
                    $row->created_at_formatted,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

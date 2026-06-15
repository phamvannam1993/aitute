<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AffKey;
use App\Models\Agent;
use App\Models\ConfigAff;
use App\Models\KeyTransferHistory;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class KeyStatisticsController extends Controller
{
    protected $agentService;
    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function getAgentKeyStatistics(Request $request, $code): JsonResponse
    {
        // Check xem có truyền api_token không
        if (!$request->has('api_token')) {
            return response()->json([
                'success' => false,
                'message' => 'API token is required'
            ], 403);
        }

        // Kiểm tra api_token từ param
        if ($request->get('api_token') !== env('API_TOKEN_AGENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token'
            ], 401);
        }

        try {
            // Sync agents từ API
            $this->agentService->syncAgents();
            // Lấy agent_id từ code
            $agent = Agent::where('code', $code)->first();

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đại lý với mã này'
                ], 404);
            }

            // Lấy thống kê chi tiết theo từng config_aff_id
            $packageStats = DB::table('aff_keys as ak')
                ->join('config_aff as ca', 'ak.config_aff_id', '=', 'ca.id')
                ->where('ak.agent_id', $agent->id)
                ->select(
                    'ca.id as package_id',
                    'ca.name as package_name',
                    DB::raw('COUNT(*) as total_keys'),
                    DB::raw('SUM(CASE WHEN ak.is_used = 1 THEN 1 ELSE 0 END) as activated_keys'),
                    DB::raw('SUM(CASE WHEN ak.is_used = 0 THEN 1 ELSE 0 END) as non_activated_keys')
                )
                ->groupBy('ca.id', 'ca.name')
                ->get();

            // Tổng hợp thống kê tổng
            $totalStats = [
                'total_keys' => 0,
                'activated_keys' => 0,
                'non_activated_keys' => 0
            ];

            $packageDetails = [];
            foreach ($packageStats as $stat) {
                $totalStats['total_keys'] += $stat->total_keys;
                $totalStats['activated_keys'] += $stat->activated_keys;
                $totalStats['non_activated_keys'] += $stat->non_activated_keys;

                $packageDetails[] = [
                    'package_id' => $stat->package_id,
                    'package_name' => $stat->package_name,
                    'total_keys' => (int)$stat->total_keys,
                    'activated_keys' => (int)$stat->activated_keys,
                    'non_activated_keys' => (int)$stat->non_activated_keys,
                    'activation_percentage' => $stat->total_keys > 0
                        ? round(($stat->activated_keys / $stat->total_keys) * 100, 2)
                        : 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'agent_info' => [
                        'code' => $code,
                        'name' => $agent->name,
                        'email' => $agent->email,
                        'phone' => $agent->phone,
                    ],
                    'total_statistics' => [
                        'total_keys' => (int)$totalStats['total_keys'],
                        'activated_keys' => (int)$totalStats['activated_keys'],
                        'non_activated_keys' => (int)$totalStats['non_activated_keys'],
                        'activation_percentage' => $totalStats['total_keys'] > 0
                            ? round(($totalStats['activated_keys'] / $totalStats['total_keys']) * 100, 2)
                            : 0
                    ],
                    'package_statistics' => $packageDetails
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncAgents(Request $request) {
        // Check xem có truyền api_token không
        if (!$request->has('api_token')) {
            return response()->json([
                'success' => false,
                'message' => 'API token is required'
            ], 403);
        }

        // Kiểm tra api_token từ param
        if ($request->get('api_token') !== env('API_TOKEN_AGENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token'
            ], 401);
        }

        try {
            // Sync agents từ API
            $this->agentService->syncAgents();

            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đồng bộ agents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transferKeys(Request $request): JsonResponse
    {
        Log::info('Param input transferKey. ' . json_encode($request->all()));
        // Validate input
        $validator = Validator::make($request->all(), [
            'from_agent_code' => 'required|string|exists:agents,code',
            'to_agent_code' => 'required|string|exists:agents,code',
            'number_of_keys' => 'required|integer|min:1',
            'package_id' => 'required|exists:config_aff,id',
            'package_name' => 'nullable|string',
            'api_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify API token
        if ($request->get('api_token') !== env('API_TOKEN_AGENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token'
            ], 401);
        }

        // Sync agents từ API
        $this->agentService->syncAgents();

        DB::beginTransaction();
        try {
            // Lấy thông tin đại lý gốc và đại lý nhận
            $fromAgent = Agent::where('code', $request->from_agent_code)->first();
            $toAgent = Agent::where('code', $request->to_agent_code)->first();
            $package = ConfigAff::find($request->package_id);

            // Kiểm tra xem đại lý gốc có đủ key chưa sử dụng không
            $availableKeys = AffKey::where([
                'agent_id' => $fromAgent->id,
                'config_aff_id' => $request->package_id,
                'is_used' => 0
            ])->count();

            if ($availableKeys < $request->number_of_keys) {
                Log::info('TransferKey. ' . "Đại lý {$fromAgent->name} không đủ key để chuyển nhượng");
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Đại lý {$fromAgent->name} không đủ key để chuyển nhượng",
                    'available_keys' => $availableKeys
                ], 400);
            }

            // Lấy danh sách key cần chuyển
            $keysToTransfer = AffKey::where([
                'agent_id' => $fromAgent->id,
                'config_aff_id' => $request->package_id,
                'is_used' => 0
            ])
                ->limit($request->number_of_keys)
                ->get();

            // Thực hiện chuyển nhượng
            foreach ($keysToTransfer as $key) {
                $key->agent_id = $toAgent->id;
                $key->updated_at = now();
                $key->save();
            }

            // Lưu lịch sử chuyển nhượng
            KeyTransferHistory::create([
                'from_agent_id' => $fromAgent->id,
                'to_agent_id' => $toAgent->id,
                'config_aff_id' => $request->package_id,
                'number_of_keys' => $request->number_of_keys,
                'transferred_at' => now(),
                'note' => "Chuyển nhượng {$request->number_of_keys} key gói {$package->name}"
            ]);

            DB::commit();

            // Lấy thống kê sau khi chuyển nhượng
            $fromAgentStats = $this->agentService->getAgentPackageStats($fromAgent->id, $request->package_id);
            $toAgentStats = $this->agentService->getAgentPackageStats($toAgent->id, $request->package_id);

            $result = [
                'success' => true,
                'message' => 'Chuyển nhượng key thành công',
                'data' => [
                    'transfer_details' => [
                        'from_agent' => [
                            'code' => $fromAgent->code,
                            'name' => $fromAgent->name,
                            'remaining_keys' => $fromAgentStats
                        ],
                        'to_agent' => [
                            'code' => $toAgent->code,
                            'name' => $toAgent->name,
                            'current_keys' => $toAgentStats
                        ],
                        'package' => [
                            'id' => $package->id,
                            'name' => $package->name,
                            'transferred_amount' => $request->number_of_keys
                        ],
                        'transferred_at' => now()->format('Y-m-d H:i:s')
                    ]
                ]
            ];
            Log::info('TransferKey Success . ' . json_encode($result));
            return response()->json($result, 200);

        } catch (\Exception $e) {
            log::error("Error Transfer : ", [$e]);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chuyển nhượng key',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableKeys(Request $request)
    {
        Log::info('Param input getAvailableKeys. ' . json_encode($request->all()));

        // Validate input
        $validator = Validator::make($request->all(), [
            'agent_code' => 'required|string|exists:agents,code',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|exists:config_aff,name',
            'packages.*.sl' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $agent = Agent::where('code', $request->agent_code)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đại lý'
            ], 404);
        }

        $results = [];

        foreach ($request->packages as $package) {
            $configAff = ConfigAff::where('name', $package['name'])->first();
            if (!$configAff) {
                Log::info('Không tìm thấy tên đại lý: ' . $package['name']);
                continue;
            }

            $availableKeys = AffKey::where([
                'agent_id' => $agent->id,
                'config_aff_id' => $configAff->id,
                'is_used' => 0
            ])->limit($package['sl'])
                ->get(['config_aff_id', 'agent_id', 'key', 'is_used', 'id'])
                ->toArray();

            $results[] = [
                'package_name' => $package['name'],
                'requested_keys' => $package['sl'],
                'returned_keys' => $availableKeys
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Danh sách key chưa active theo số lượng yêu cầu',
            'data' => $results
        ], 200);
    }


    public function syncStoreKey(Request $request)
    {
        $this->agentService->syncAgents();
        $token = $request->bearerToken() ?? '';

        if ($token !== env('API_TOKEN_AGENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token'
            ], 401);
        }
        $payload = $request->all();

        if (!is_array($payload)) {
            return response()->json(['message' => 'Dữ liệu đầu vào không hợp lệ'], 400);
        }

        $errors = [];
        $insertData = [];

        foreach ($payload as $index => $item) {
            $validator = Validator::make($item, [
                'package_name' => 'required|string|max:255',
                'key' => 'required|string|max:255',
                'seri' => 'required|string|max:50',
                'month' => 'required|integer|min:1',
                'coupon' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->messages();
                continue;
            }

            $agent = Agent::where('code', $item['coupon'])->first();
            if (!$agent) {
                $errors[$index]['coupon'] = ['Không tìm thấy agent với coupon: ' . $item['coupon']];
                continue;
            }

            $configAff = ConfigAff::where('name', $item['package_name'])
                ->where('month', $item['month'])
                ->first();

            if (!$configAff) {
                $errors[$index]['package'] = ['Không tìm thấy gói ' . $item['package_name'] . ' với month ' . $item['month']];
                continue;
            }

            $keyExists = AffKey::where('key', $item['key'])->exists();
            if ($keyExists) {
                $errors[$index]['key'] = ['Key đã tồn tại trong hệ thống'];
                continue;
            }

            $insertData[] = [
                'config_aff_id' => $configAff->id,
                'agent_id' => $agent->id,
                'key' => $item['key'],
                'seri' => $item['seri'],
                'is_used' => 0,
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ, không thực hiện ghi dữ liệu',
                'errors' => $errors
            ], 422);
        }

        AffKey::insert($insertData);

        return response()->json([
            'message' => 'Thêm tất cả key thành công',
            'inserted' => count($insertData)
        ]);
    }
}

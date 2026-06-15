<?php

namespace App\Services;

use App\Models\AffKey;
use App\Models\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentService
{
    protected $apiUrl;
    protected $apiToken = '$2y$10$3iwH0yvY4vueEbF0p7SRXOd8bC4CpTV1BOcucEKCghilF1orXFLfC';

//    public function __construct()
//    {
//        $this->apiToken = config('services.aiwow.api_token') ?? env('AIWOW_API_TOKEN');
//    }

    public function __construct()
    {
        $this->apiUrl = env('AGENT_API_URL') . '/allAgency';
    }

    public function syncAgents()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get($this->apiUrl, [
                'api_token' => $this->apiToken
            ]);

            if ($response->successful()) {
                $agencies = $response->json();
                foreach ($agencies as $agency) {
                    $data = [
                        'coupon_parent' => $agency['coupon_parent'] ?? '',
                        'name' => $agency['name'] ?? '',
                        'code' => $agency['coupon'] ?? '',
                        'phone' => $agency['tel'] ?? '',
                        'email' => $agency['email'] ?? ''
                    ];
                    Agent::updateOrCreate(
                        ['code' => $agency['coupon'] ?? ''],
                        $data
                    );
                }

                return true;
            }

            Log::error('Sync agents failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Sync agents error: ' . $e->getMessage());
            return false;
        }
    }

    public function syncAffMaster(array $data)
    {
        Log::info('Thong tin user ' .json_encode($data));
        try {
            $apiUrl = env('AGENT_API_URL') . '/form/dang-ky-tai-khoan';
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'coupon_parent' => $data['coupon_parent'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => $data['password']
            ]);

            if ($response->successful()) {
                Log::info('Kích hoạt gửi aff thành công - ' . $response->body(). ' - ' .json_encode($data));
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Register affiliate master failed: ' . $response->body());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Registration failed'
            ];

        } catch (\Exception $e) {
            Log::error('Register affiliate master error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAgentPackageStats($agentId, $packageId)
    {
        return [
            'total' => AffKey::where([
                'agent_id' => $agentId,
                'config_aff_id' => $packageId
            ])->count(),
            'used' => AffKey::where([
                'agent_id' => $agentId,
                'config_aff_id' => $packageId,
                'is_used' => 1
            ])->count(),
            'available' => AffKey::where([
                'agent_id' => $agentId,
                'config_aff_id' => $packageId,
                'is_used' => 0
            ])->count()
        ];
    }

}

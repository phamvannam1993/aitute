<?php

namespace App\Repositories;

use App\Constants\Zalo;
use App\Models\ZaloConfig;
use Carbon\Carbon;

class ZaloConfigRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ZaloConfig::class;
    }

    public function updateConfig(int $zalo_config_id, array $token)
    {
        return $this->model
            ->where('id', $zalo_config_id)
            ->update([
                'access_token' => $token['accessToken'],
                'refresh_token' => $token['refreshToken'],
                'access_token_expires_at' => Carbon::now()->addDays(Zalo::ACCESS_TOKEN_LIFETIME_DAY),
                'refresh_token_expires_at' => Carbon::now()->addDays(Zalo::REFRESH_TOKEN_LIFETIME_DAY),
            ]);
    }


    public function getZaloExpiredInNext30Days()
    {
        return $this->model
            ->where('refresh_token_expires_at', '<', Carbon::now()->addDays(Zalo::DAYS_REMAINING_TO_EXPIRE))
            ->where('refresh_token_expires_at', '>', Carbon::now())
            ->orderBy('refresh_token_expires_at')
            ->get();
    }
}

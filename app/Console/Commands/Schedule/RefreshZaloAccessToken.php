<?php

namespace App\Console\Commands\Schedule;

use App\Repositories\ZaloConfigRepository;
use App\Services\ZaloService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshZaloAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-zalo-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh-zalo-access-token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $zaloService = app(ZaloService::class);
        $zaloConfigRepository = app(ZaloConfigRepository::class);
        $zaloConfigs = $zaloConfigRepository->getZaloExpiredInNext30Days();
        foreach ($zaloConfigs as $zaloConfig) {
            try {
                $zaloService->setConfig($zaloConfig->app_id);
            } catch (\Throwable $e) {
                Log::info('RefreshZaloAccessToken: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }
    }
}

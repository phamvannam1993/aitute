<?php

namespace App\Console\Commands\Schedule;

use App\Repositories\FacebookRepository;
use App\Services\FacebookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshFacebookUserAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-facebook-user-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh-facebook-user-access-token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $facebookService = app(FacebookService::class);
        $facebookRepository = app(FacebookRepository::class);
        $facebooks = $facebookRepository->getFacebookExpiredInNext30Days();
        
        foreach ($facebooks as $facebook) {
            try {
                $facebookService->refreshAccessToken($facebook);
            } catch (\Throwable $e) {
                Log::info('RefreshFacebookUserAccessToken: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }
    }
}

<?php

namespace App\Jobs;

use App\Helper\Helpers;
use Illuminate\Bus\Queueable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;
use App\Services\KlingService;

class GenerateVideoAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $user_id;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, $user_id)
    {
        $this->data = $data;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService, KlingService $klingService): void
    {
        switch ($this->data['model']) {
            case 'Lucataco/animate-diff':
                $aIVideoService->generateVideoAudio($this->data, $this->user_id);
                $this->handleUsageCredit('animate-diff', $this->user_id);
                break;
            case 'Kling':
                $klingService->createTask($this->data, $this->user_id);
                $this->handleUsageCredit('kling', $this->user_id);
                break;
        }
    }

    private function handleUsageCredit($model, $user_id = null)
    {
        $user = $user_id ? User::find($user_id) : auth()->user();
        try {
            $user->chargeCredit($model, null, null);
        } catch (\Exception $e) {
            Log::error("Error charging credit in queue: " . $e->getMessage());
            throw $e; // Nên throw lại lỗi để job fail và có thể retry
        }
    }
}

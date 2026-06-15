<?php

namespace App\Jobs;

use App\Models\MyAIImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckFinetuneStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $jobId;
    public $apiUrl;
    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $apiUrl)
    {
        $this->jobId = $jobId;
        $this->apiUrl = $apiUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiToken = env('REPLICATE_API_TOKEN');
        $response = Http::withToken($apiToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get($this->apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? null;
    
                $model = MyAIImage::where('replicate_id',$this->jobId)->first();
                if ($model) {
                    $model->update([
                        'status' => $status,
                        'model_enpoint' => $data['version'],
                        'end_time' => now()
                    ]);
                }
    
                if ($status !== 'succeeded') {
                    CheckFinetuneStatus::dispatch($this->jobId, $this->apiUrl)
                        ->delay(now()->addSeconds(10));
                }
            } else {
                Log::error("Failed to fetch status for job ID: {$this->jobId}");
            }
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVirtualService;

class MergeAudioVideoMC implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $mcId;
    protected $audioUrl;
    public $timeout = 3000; 
    /**
     * Create a new job instance.
     */
    public function __construct($mcId, $audioUrl)
    {
        $this->mcId = $mcId;
        $this->audioUrl = $audioUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVirtualService $aiVirtualService): void
    {
        $aiVirtualService->mergeAudioVideo($this->mcId, $this->audioUrl);
    }
}
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;

class MergeAudioVideoLipsync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $lipsyncId;
    protected $audioUrl;
    public $timeout = 3000; 
    /**
     * Create a new job instance.
     */
    public function __construct($lipsyncId, $audioUrl)
    {
        $this->lipsyncId = $lipsyncId;
        $this->audioUrl = $audioUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService): void
    {
        $aIVideoService->mergeAudioVideoLipsync($this->lipsyncId, $this->audioUrl);
    }
}
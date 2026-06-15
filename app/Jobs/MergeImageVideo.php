<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;

class MergeImageVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $mediaId;
    protected $imageUrl;
    public $timeout = 3000; 
    /**
     * Create a new job instance.
     */
    public function __construct($mediaId, $imageUrl)
    {
        $this->mediaId = $mediaId;
        $this->imageUrl = $imageUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService): void
    {
        $aIVideoService->mergeImageVideo($this->mediaId, $this->imageUrl);
    }
}
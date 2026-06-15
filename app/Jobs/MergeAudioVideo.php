<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;
use App\Services\ShortVideoService;
use App\Services\VideoService;

class MergeAudioVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $mediaId;
    protected $type;
    protected $ratio;
    protected $audioUrl;
    public $timeout = 3000; 
    /**
     * Create a new job instance.
     */
    public function __construct($mediaId, $audioUrl, $type, $ratio)
    {
        $this->mediaId = $mediaId;
        $this->audioUrl = $audioUrl;
        $this->type = $type; 
        $this->ratio = $ratio; 
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService, ShortVideoService $shortVideoService, VideoService $videoService): void
    {
        if($this->type == "ratio") {
            $aIVideoService->convertRatio($this->mediaId, $this->ratio);
        } else if($this->type == 2) {
            $aIVideoService->mergeAudioVideoV2($this->mediaId, $this->audioUrl);
        } else if($this->type == 3) {
            $shortVideoService->mergeAudioVideo($this->mediaId, $this->audioUrl);
        } else if($this->type == 4) {
            $shortVideoService->mergeAudioVideoV2($this->mediaId, $this->audioUrl);
        } else if($this->type == 5) {
            $videoService->mergeAudioVideo($this->mediaId, $this->audioUrl);
        } else {
            $aIVideoService->mergeAudioVideo($this->mediaId, $this->audioUrl);
        }
    }
}
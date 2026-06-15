<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;
use App\Services\VideoService;

class MergeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $ids;
    protected $type;
    protected $aiMediaId; 
    protected $audioPath; 
    protected $imagePath; 
    protected $audioDescription;
    protected $ratio;
    /**
     * Create a new job instance.
     * 
     */
    public $timeout = 3000; 

    public function __construct($ids, $aiMediaId, $audioPath, $imagePath, $audioDescription, $ratio, $type = 1)
    {
        $this->ids = $ids;
        $this->type = $type;
        $this->aiMediaId = $aiMediaId;
        $this->audioPath = $audioPath;
        $this->imagePath = $imagePath;
        $this->audioDescription = $audioDescription;
        $this->ratio = $ratio;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService, VideoService $videoService): void
    {
        if($this->type == 1) {
            $aIVideoService->mergeVideo($this->ids, $this->aiMediaId, $this->audioPath, $this->imagePath, $this->audioDescription, $this->ratio);
        } else {
            $videoService->mergeVideo($this->ids, $this->aiMediaId, $this->audioPath, $this->imagePath, $this->audioDescription, $this->ratio);
        }
    }
}

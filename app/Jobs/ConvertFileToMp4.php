<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIVideoService;

class ConvertFileToMp4 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $videoPath;
    protected $videoPathConvert;
    public $timeout = 3000; 
    /**
     * Create a new job instance.
     */
    public function __construct($videoPath, $videoPathConvert)
    {
        $this->videoPath = $videoPath;
        $this->videoPathConvert = $videoPathConvert;
    }

    /**
     * Execute the job.
     */
    public function handle(AIVideoService $aIVideoService): void
    {
        $aIVideoService->convertFileToMp4($this->videoPath, $this->videoPathConvert);
    }
}
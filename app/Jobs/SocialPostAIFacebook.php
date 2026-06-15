<?php

namespace App\Jobs;

use App\Services\SocialPostService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SocialPostAIFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topic;
    protected $params;
    protected $scheduledPublishTime;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Carbon $scheduledPublishTime, string $topic, array $params)
    {
        $this->scheduledPublishTime = $scheduledPublishTime;
        $this->topic = $topic;
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(SocialPostService $socialPostService): void
    {
        $socialPostService->createContentAIPostToFacebook(
            $this->scheduledPublishTime,
            $this->topic,
            $this->params
        );
    }
}

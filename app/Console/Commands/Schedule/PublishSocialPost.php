<?php

namespace App\Console\Commands\Schedule;

use App\Models\SocialPost;
use App\Services\SocialPostService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishSocialPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-social-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Publishing social posts');
        $shouldPublishSocialPosts = SocialPost::query()->whereNull('published_at')
            ->where('scheduled_at', '<=', now())
            ->where('attempts', '<', SocialPost::MAX_ATTEMPTS)
            ->get();

        /** @var SocialPostService $socialPostService */
        $socialPostService = app(SocialPostService::class);
        foreach ($shouldPublishSocialPosts as $socialPost) {
            try {
                $socialPostService->publish($socialPost);
                Log::info('Published social post', ['social_post_id' => $socialPost->id]);
            } catch (\Exception $e) {
                Log::error('Failed to publish social post', [
                    'social_post_id' => $socialPost->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

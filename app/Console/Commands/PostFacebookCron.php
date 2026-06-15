<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FacebookContentService;

class PostFacebookCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:facebook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(FacebookContentService $facebookContentService)
    {
        $facebookContentService->postFacebookCron();
    }
}

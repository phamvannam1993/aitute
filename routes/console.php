<?php

use App\Console\Commands\Schedule\PublishSocialPost;
use App\Console\Commands\PostFacebookCron;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\GetKlingTask;
use App\Services\KlingService;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Artisan::command('kling:get-task', function () {
//    $task = new GetKlingTask();
//    $task->handle(
//        new KlingService()
//    );
//})->purpose('Get task from Kling Service')->everyMinute();

// Schedule::command(PublishSocialPost::class)->everyMinute();
Schedule::command(PostFacebookCron::class)->everyFiveMinutes();

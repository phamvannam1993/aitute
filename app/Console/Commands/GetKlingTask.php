<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KlingService;
use App\Models\AIGeneratedMedia;

class GetKlingTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kling:get-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get task from Kling Service';

    /**
     * Execute the console command.
     */
    public function handle(KlingService $klingService)
    {
        echo 'kling:get-task: running ... ';
        $taskIds = AIGeneratedMedia::query()->where('task_id', '!=', null)->where('s3_url', '')->pluck('task_id');
        foreach ($taskIds as $taskId) {
            $klingService->getTask($taskId);
        }
    }
}
